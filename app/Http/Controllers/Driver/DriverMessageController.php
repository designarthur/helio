<?php

namespace App->Http->Controllers->Driver;

use App->Http->Controllers->Controller;
use App->Models->Message;
use App->Models->User; // The model used for driver authentication and other staff
use App->Models->Vendor; // For authentication fallback
use Illuminate->Http->Request;
use Illuminate->Support->Facades->Auth;
use Carbon->Carbon;
use Illuminate->Validation->Rule;

class DriverMessageController extends Controller
{
    /**
     * Display the driver's message inbox (conversation list) and a selected conversation.
     */
    public function index(Request $request)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            Auth::guard('driver')->logout();
            return redirect()->route('driver.login')->with('error', 'Authentication failed. Please log in as a driver.');
        }

        $driverId = $user->id;
        $vendorId = $user->vendor_id;

        // Fetch all messages where the current driver is either sender or recipient
        $allMessages = Message::where('vendor_id', $vendorId)
                              ->where(function ($query) use ($driverId) {
                                  $query->where('sender_id', $driverId)
                                        ->orWhere('recipient_id', $driverId);
                              })
                              ->with(['sender', 'recipient']) // Eager load sender/recipient details
                              ->orderBy('created_at', 'asc')
                              ->get();

        // Group messages into conversations
        // A conversation is defined by a unique pair of sender and recipient (excluding self-messages)
        $conversations = $allMessages->groupBy(function ($message) use ($driverId) {
            $otherParticipantId = ($message->sender_id == $driverId) ? $message->recipient_id : $message->sender_id;
            return $otherParticipantId; // Group by the ID of the "other" participant
        })->map(function ($group) use ($driverId, $vendorId) {
            $lastMessage = $group->sortByDesc('created_at')->first();
            $otherParticipant = ($lastMessage->sender_id == $driverId) ? $lastMessage->recipient : $lastMessage->sender;
            
            $unreadCount = $group->where('recipient_id', $driverId)->whereNull('read_at')->count();

            return [
                'id' => $otherParticipant->id, // Use other participant's ID as conversation ID
                'name' => $otherParticipant->name,
                'last_message' => $lastMessage->message_content,
                'time_ago' => $lastMessage->created_at->diffForHumans(),
                'unread_count' => $unreadCount,
                'last_message_time' => $lastMessage->created_at, // For sorting
                'messages' => $group->sortBy('created_at')->values()->map(function ($msg) use ($driverId) {
                    return [
                        'sender_name' => $msg->sender->name,
                        'is_sent_by_me' => ($msg->sender_id == $driverId),
                        'message_content' => $msg->message_content,
                        'time' => $msg->created_at->format('M d, H:i A'),
                    ];
                })
            ];
        })->sortByDesc('last_message_time')->values(); // Sort conversations by last message time

        // Mark messages in the currently viewed conversation as read
        $selectedConversationId = $request->input('conversation_id');
        if ($selectedConversationId) {
            Message::where('vendor_id', $vendorId)
                   ->where('recipient_id', $driverId)
                   ->where(function($query) use ($selectedConversationId) {
                       $query->where('sender_id', $selectedConversationId)
                             ->orWhere('recipient_id', $selectedConversationId);
                   })
                   ->whereNull('read_at')
                   ->update(['read_at' => Carbon::now()]);
            
            // Re-fetch messages for selected conversation to show read status if needed
            $selectedConversationMessages = Message::where('vendor_id', $vendorId)
                                                ->where(function($query) use ($driverId, $selectedConversationId) {
                                                    $query->where(function($q) use ($driverId, $selectedConversationId) {
                                                        $q->where('sender_id', $driverId)->where('recipient_id', $selectedConversationId);
                                                    })->orWhere(function($q) use ($driverId, $selectedConversationId) {
                                                        $q->where('sender_id', $selectedConversationId)->where('recipient_id', $driverId);
                                                    });
                                                })
                                                ->with(['sender', 'recipient'])
                                                ->orderBy('created_at', 'asc')
                                                ->get()
                                                ->map(function ($msg) use ($driverId) {
                                                    return [
                                                        'sender_name' => $msg->sender->name,
                                                        'is_sent_by_me' => ($msg->sender_id == $driverId),
                                                        'message_content' => $msg->message_content,
                                                        'time' => $msg->created_at->format('M d, H:i A'),
                                                    ];
                                                });
        } else {
            $selectedConversationMessages = collect();
        }

        // Get recipients for 'New Conversation' dropdown (e.g., dispatchers, other drivers)
        $potentialRecipients = User::where('vendor_id', $vendorId)
                                   ->where('id', '!=', $driverId) // Exclude self
                                   ->whereIn('role', ['Dispatcher', 'Driver', 'Admin', 'Vendor Admin']) // Filter by relevant roles
                                   ->orderBy('name')
                                   ->get();

        $unreadCountTotal = $allMessages->where('recipient_id', $driverId)->whereNull('read_at')->count();


        return view('driver.messages.index', compact(
            'user', // The authenticated driver
            'conversations',
            'selectedConversationId', // ID of the conversation to open by default
            'selectedConversationMessages', // Messages for the selected conversation
            'potentialRecipients', // For new conversation dropdown
            'unreadCountTotal' // For sidebar badge
        ));
    }

    /**
     * Store a new message.
     * This method handles sending a message within an existing or new conversation.
     */
    public function store(Request $request)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validate([
            'recipient_id' => ['required', 'exists:users,id,vendor_id,' . $user->vendor_id], // Recipient must be a user of this vendor
            'message_content' => ['required', 'string', 'max:1000'],
        ]);

        $message = Message::create([
            'vendor_id' => $user->vendor_id,
            'sender_id' => $user->id,
            'recipient_id' => $validatedData['recipient_id'],
            'message_content' => $validatedData['message_content'],
            'read_at' => null, // New messages are unread
        ]);

        // In a real app, this would also trigger real-time notifications (e.g., websockets)
        // to the recipient and possibly dispatch.

        return redirect()->route('driver.messages.index', ['conversation_id' => $validatedData['recipient_id']])->with('success', 'Message sent!');
    }

    /**
     * Conceptual: Mark a specific message as read. (Can be called via AJAX)
     */
    public function markAsRead(Message $message)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver' || $message->recipient_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (is_null($message->read_at)) {
            $message->read_at = Carbon::now();
            $message->save();
        }

        return response()->json(['status' => 'success', 'message' => 'Message marked as read.']);
    }

    /**
     * Conceptual: Mark all messages in a specific conversation as read.
     */
    public function markConversationAsRead(Request $request, User $otherParticipant) // otherParticipant is the sender/receiver
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        Message::where('vendor_id', $user->vendor_id)
               ->where('recipient_id', $user->id)
               ->where('sender_id', $otherParticipant->id)
               ->whereNull('read_at')
               ->update(['read_at' => Carbon::now()]);

        return redirect()->route('driver.messages.index', ['conversation_id' => $otherParticipant->id])->with('success', 'Conversation marked as read.');
    }

    /**
     * Conceptual: Start a new conversation (redirect to index with recipient pre-selected)
     */
    public function startNewConversation(Request $request)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        $recipientId = $request->input('recipient_id');
        if ($recipientId) {
            // Check if recipient is valid for this vendor
            $recipient = User::where('id', $recipientId)
                             ->where('vendor_id', $user->vendor_id)
                             ->whereIn('role', ['Dispatcher', 'Driver', 'Admin', 'Vendor Admin'])
                             ->first();
            if (!$recipient) {
                return redirect()->back()->with('error', 'Invalid recipient selected.');
            }
            return redirect()->route('driver.messages.index', ['conversation_id' => $recipientId])->with('info', 'New conversation started.');
        }

        return redirect()->route('driver.messages.index')->with('info', 'Select a recipient to start a new conversation.');
    }
    
    // No direct update/destroy methods for individual messages for auditability.
    // Messages are typically archived or marked as read, not deleted.
    // Deletion of messages would typically be handled at a higher admin level.
}