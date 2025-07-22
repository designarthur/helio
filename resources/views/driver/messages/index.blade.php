<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Driver Messages</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'chili-red': '#EA3A26',
                        'ut-orange': '#FF8600',
                        'chili-red-2': '#EA3D2A',
                        'tangelo': '#F54F1D',
                        'custom-red-2': '#FF0000', // For notification badges
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-in-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'pulse-custom': 'pulse 0.5s ease-in-out',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .message-list-item.unread {
            background-color: #fef2f2; /* Light red for unread */
            border-left: 4px solid #EA3A26; /* Chili red border */
        }
        .chat-message.sent {
            background-color: #EA3A26; /* chili-red */
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 0;
            border-top-right-radius: 12px;
        }
        .chat-message.received {
            background-color: #E5E7EB; /* gray-200 */
            color: #374151; /* gray-800 */
            align-self: flex-start;
            border-bottom-left-radius: 0;
            border-top-left-radius: 12px;
        }
        .chat-messages-container {
            flex-grow: 1; /* Allows container to take available height */
            overflow-y: auto;
            display: flex; /* Make it a flex container to push messages to top/bottom */
            flex-direction: column; /* Stack messages vertically */
            padding: 1rem;
        }
        /* Style for scrolling to bottom - needed for message container */
        .chat-messages-container::-webkit-scrollbar {
            width: 8px;
        }
        .chat-messages-container::-webkit-scrollbar-thumb {
            background-color: #cbd5e0; /* gray-300 */
            border-radius: 4px;
        }
        .chat-messages-container::-webkit-scrollbar-track {
            background-color: #f7fafc; /* gray-50 */
        }
        
        /* For message input area, align send button */
        .message-input-area {
            flex-shrink: 0; /* Prevent it from shrinking */
            padding-top: 1rem;
            border-top: 1px solid #E5E7EB;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 font-sans">
    <button 
        id="mobileNavToggle" 
        class="lg:hidden fixed top-4 left-4 z-50 bg-chili-red text-white p-3 rounded-lg shadow-lg hover:bg-chili-red-2 transition-colors duration-300"
    >
        <i class="fas fa-bars text-lg"></i>
    </button>

    <div 
        id="sidebarOverlay" 
        class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40 opacity-0 invisible transition-all duration-300"
    ></div>

    <div class="flex min-h-screen">
        <nav 
            id="sidebar" 
            class="fixed lg:relative bg-gradient-to-b from-black to-gray-800 w-72 h-screen overflow-y-auto z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out"
        >
            <div class="text-center py-8 px-6">
                <h1 class="text-chili-red text-3xl font-bold drop-shadow-lg">
                    <i class="fas fa-truck-pickup mr-2"></i>Helly
                </h1>
                <p class="text-gray-400 text-sm">Driver Portal</p>
            </div>

            <ul class="space-y-2 px-4">
                <li>
                    <a href="{{ route('driver.dashboard') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange relative">
                        <i class="fas fa-tachometer-alt mr-4 text-lg"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('driver.assigned_routes.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange relative">
                        <i class="fas fa-route mr-4 text-lg"></i>
                        Assigned Routes
                        <span class="absolute -top-1 -right-1 bg-custom-red-2 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">0</span> {{-- Will be dynamically updated --}}
                    </a>
                </li>
                <li>
                    <a href="{{ route('driver.schedule.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange relative">
                        <i class="fas fa-calendar-alt mr-4 text-lg"></i>
                        Schedule
                    </a>
                </li>
                <li>
                    <a href="{{ route('driver.vehicle_inspection.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange relative">
                        <i class="fas fa-clipboard-check mr-4 text-lg"></i>
                        Vehicle Inspection
                        <span class="absolute -top-1 -right-1 bg-custom-red-2 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">!</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('driver.driver_log.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange relative">
                        <i class="fas fa-hourglass-half mr-4 text-lg"></i>
                        Driver Log (HOS)
                    </a>
                </li>
                <li>
                    <a href="{{ route('driver.messages.index') }}" class="nav-link active bg-gradient-to-r from-chili-red to-tangelo border-ut-orange transform translate-x-2 flex items-center px-6 py-4 text-white transition-all duration-300 rounded-lg border-l-4 relative">
                        <i class="fas fa-comments mr-4 text-lg"></i>
                        Messages
                        <span class="absolute -top-1 -right-1 bg-custom-red-2 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center" id="messageBadge">{{ $unreadCountTotal }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('driver.profile.show') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange">
                        <i class="fas fa-user-circle mr-4 text-lg"></i>
                        Profile
                    </a>
                </li>
                <li class="mt-8">
                    <form action="{{ route('driver.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="nav-link w-full text-left flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange">
                            <i class="fas fa-sign-out-alt mr-4 text-lg"></i>
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </nav>

        <main class="flex-1 lg:ml-0 p-4 lg:p-8 mt-16 lg:mt-0 flex flex-col">
            <header class="bg-white rounded-2xl shadow-lg p-6 lg:p-8 mb-8 flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0">
                <div class="text-center lg:text-left">
                    <h2 class="text-chili-red text-2xl lg:text-3xl font-bold mb-2">Messages</h2>
                    <p class="text-gray-600 text-lg">Communicate with dispatch and team members</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
                    <button onclick="openNewConversationModal()" class="btn-primary bg-gradient-to-r from-chili-red to-tangelo text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl hover:transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-plus-circle mr-2"></i>
                        New Conversation
                    </button>
                </div>
            </header>

            {{-- Success/Error Messages from Controller --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            @if (session('info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Info:</strong>
                    <span class="block sm:inline">{{ session('info') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Validation Error!</strong>
                    <span class="block sm:inline">Please check your input.</span>
                    <ul class="mt-3 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="bg-white rounded-2xl shadow-lg p-6 lg:p-8 flex flex-col lg:flex-row gap-6 flex-grow">
                <div class="w-full lg:w-1/3 border-r lg:border-r-gray-200 pr-0 lg:pr-6 mb-6 lg:mb-0">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Conversations</h3>
                    <div class="space-y-2" id="conversationList">
                        {{-- Conversations will be rendered here by JavaScript --}}
                    </div>
                </div>

                <div class="flex-1 flex flex-col">
                    <div id="noConversationSelected" class="flex items-center justify-center h-full text-gray-500 text-lg @if($selectedConversationId) hidden @endif">
                        <i class="fas fa-arrow-left mr-2"></i> Select a conversation to view messages
                    </div>
                    <div id="chatWindow" class="flex-1 flex flex-col @if(!$selectedConversationId) hidden @endif">
                        <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-200">
                            <h3 class="text-xl font-bold text-gray-800" id="chatRecipientName">
                                {{ $selectedConversationMessages->first()->sender_name ?? '' }}
                            </h3>
                            <button onclick="closeChatWindow()" class="lg:hidden text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                        <div class="chat-messages-container space-y-4">
                            @forelse($selectedConversationMessages as $message)
                                <div class="chat-message max-w-[80%] p-3 rounded-xl shadow-sm {{ $message['is_sent_by_me'] ? 'sent' : 'received' }}">
                                    <p class="text-sm">{{ $message['message_content'] }}</p>
                                    <span class="block text-right text-xs opacity-75 mt-1">{{ $message['time'] }}</span>
                                </div>
                            @empty
                                <p class="text-center text-gray-500 py-4">No messages in this conversation yet. Send one!</p>
                            @endforelse
                        </div>
                        <div class="message-input-area flex items-center mt-auto">
                            <form action="{{ route('driver.messages.store') }}" method="POST" class="flex-1 flex items-center">
                                @csrf
                                <input type="hidden" name="recipient_id" value="{{ $selectedConversationId }}">
                                <input type="text" id="messageInput" name="message_content" placeholder="Type your message..."
                                    class="flex-1 p-3 border border-gray-300 rounded-lg focus:ring-chili-red focus:border-chili-red mr-3" required>
                                <button type="submit" class="bg-chili-red text-white px-5 py-3 rounded-lg hover:bg-chili-red-2 transition-colors duration-300 flex items-center">
                                    <i class="fas fa-paper-plane mr-2"></i>Send
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    {{-- New Conversation Modal --}}
    <div id="newConversationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 max-h-screen overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-2xl font-bold text-chili-red">Start New Conversation</h3>
                    <button onclick="closeConceptualModal('newConversationModal')" class="text-gray-400 hover:text-gray-600 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <form action="{{ route('driver.messages.startNewConversation') }}" method="POST">
                @csrf
                <div class="p-6">
                    <div class="mb-4">
                        <label for="recipientSelect" class="block text-sm font-medium text-gray-700 mb-2">Select Recipient:</label>
                        <select id="recipientSelect" name="recipient_id" required
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-chili-red focus:border-chili-red">
                            <option value="">Select a Team Member</option>
                            @foreach($potentialRecipients as $recipient)
                                <option value="{{ $recipient->id }}">{{ $recipient->name }} ({{ $recipient->role }})</option>
                            @endforeach
                        </select>
                        @error('recipient_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex gap-3 justify-end mt-6">
                        <button type="button" onclick="closeConceptualModal('newConversationModal')" class="border border-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition-colors duration-300">Cancel</button>
                        <button type="submit" class="bg-chili-red text-white px-6 py-3 rounded-lg hover:bg-chili-red-2 transition-colors duration-300">Start Chat</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div id="notificationContainer" class="fixed top-4 right-4 z-50"></div>

    <script>
        // Data passed from Controller
        const conversationsData = @json($conversations);
        const selectedConversationId = @json($selectedConversationId);
        const selectedConversationMessages = @json($selectedConversationMessages);
        const potentialRecipientsData = @json($potentialRecipients);

        // Get elements
        const conversationListContainer = document.getElementById('conversationList');
        const chatWindow = document.getElementById('chatWindow');
        const noConversationSelected = document.getElementById('noConversationSelected');
        const chatRecipientName = document.getElementById('chatRecipientName');
        const chatMessagesContainer = document.querySelector('.chat-messages-container');
        const messageInput = document.getElementById('messageInput');

        // --- Conversation List Functions ---
        function renderConversationList() {
            conversationListContainer.innerHTML = '';
            let totalUnread = 0;

            conversationsData.forEach(conv => {
                totalUnread += conv.unread_count;
                const item = document.createElement('a'); // Use 'a' tag for better navigation
                item.href = `{{ route('driver.messages.index') }}?conversation_id=${conv.id}`; // Link to route
                item.classList.add('message-list-item', 'bg-white', 'p-3', 'rounded-lg', 'shadow-sm', 'cursor-pointer', 'hover:bg-gray-100', 'transition-colors', 'duration-200', 'flex', 'items-center');
                
                if (conv.unread_count > 0) {
                    item.classList.add('unread');
                }
                if (conv.id == selectedConversationId) { // == for type coercion with number/string
                    item.classList.add('bg-gray-100', 'border-l-4', 'border-chili-red');
                }

                item.innerHTML = `
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center mr-3 text-lg">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-center">
                            <h4 class="font-semibold text-gray-800">${conv.name}</h4>
                            <span class="text-xs text-gray-500">${conv.time_ago}</span>
                        </div>
                        <p class="text-sm text-gray-600 truncate">${conv.last_message}</p>
                    </div>
                    ${conv.unread_count > 0 ? `<span class="flex-shrink-0 ml-3 bg-chili-red text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">${conv.unread_count}</span>` : ''}
                `;
                conversationListContainer.appendChild(item);
            });
            updateMessageBadge(totalUnread);
        }

        // --- Chat Window Functions ---
        function openConversation(id, name, messages) {
            // This function is now mostly handled by Blade and page reload,
            // but its logic is useful for client-side updates if using AJAX later.
            chatRecipientName.textContent = name;
            chatMessagesContainer.innerHTML = ''; // Clear current messages

            messages.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.classList.add('chat-message', 'max-w-[80%]', 'p-3', 'rounded-xl', 'shadow-sm');
                messageDiv.classList.add(msg.is_sent_by_me ? 'sent' : 'received');
                messageDiv.innerHTML = `
                    <p class="text-sm">${msg.message_content}</p>
                    <span class="block text-right text-xs opacity-75 mt-1">${msg.time}</span>
                `;
                chatMessagesContainer.appendChild(messageDiv);
            });

            chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight; // Scroll to bottom
            
            noConversationSelected.classList.add('hidden');
            chatWindow.classList.remove('hidden');

            // On mobile, hide conversation list to focus on chat
            if (window.innerWidth < 1024) {
                document.querySelector('.lg\\:w-1\\/3').classList.add('hidden'); // This hides the conversation list column
            }
        }

        // Send Message Function (now uses form submission)
        // The form for sending message is already in Blade and submits to driver.messages.store
        // No specific JS 'sendMessage' function needed here other than potential validation/UX.
        // The message input field's name is 'message_content' and hidden 'recipient_id' is already set.

        function openNewConversationModal() {
            // Populate recipient select in modal
            const recipientSelect = document.getElementById('recipientSelect');
            recipientSelect.innerHTML = '<option value="">Select a Team Member</option>';
            potentialRecipientsData.forEach(recipient => {
                const option = document.createElement('option');
                option.value = recipient.id;
                option.textContent = `${recipient.name} (${recipient.role})`;
                recipientSelect.appendChild(option);
            });

            openModal('newConversationModal');
        }

        function closeChatWindow() {
            chatWindow.classList.add('hidden');
            noConversationSelected.classList.remove('hidden');
            if (window.innerWidth < 1024) {
                document.querySelector('.lg\\:w-1\\/3').classList.remove('hidden'); // Show conversation list column
            }
        }

        function updateMessageBadge(count) {
            const messageBadge = document.getElementById('messageBadge');
            if (messageBadge) {
                messageBadge.textContent = count;
                if (count > 0) {
                    messageBadge.classList.remove('hidden');
                } else {
                    messageBadge.classList.add('hidden');
                }
            }
        }

        // General Modal Functions (reused from common patterns)
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.getElementById(modalId).classList.add('opacity-100', 'visible');
        }
        function closeConceptualModal(modalId) { // Used for newConversationModal
            document.getElementById(modalId).classList.add('opacity-0', 'invisible');
            document.getElementById(modalId).classList.remove('opacity-100', 'visible');
        }


        // Notification system (copied for consistency)
        function showNotification(message, type = 'info') {
            const notificationContainer = document.getElementById('notificationContainer');
            if (!notificationContainer) {
                console.warn("Notification container not found. Cannot display notification.");
                return;
            }
            const notification = document.createElement('div');
            notification.className = `p-4 rounded-lg shadow-lg max-w-sm mt-2 transition-all duration-300 transform translate-x-full opacity-0`;

            const colors = {
                success: 'bg-green-500 text-white',
                error: 'bg-red-500 text-white',
                info: 'bg-blue-500 text-white',
                warning: 'bg-yellow-500 text-white'
            };

            notification.className += ` ${colors[type]}`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <span class="flex-1">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            notificationContainer.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full', 'opacity-0');
                notification.classList.add('translate-x-0', 'opacity-100');
            }, 50);

            // Animate out and remove after a delay
            setTimeout(() => {
                notification.classList.remove('translate-x-0', 'opacity-100');
                notification.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Mobile navigation functionality (copied for consistency)
        const mobileNavToggle = document.getElementById('mobileNavToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function toggleMobileNav() {
            sidebar.classList.toggle('translate-x-0');
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('opacity-0');
            sidebarOverlay.classList.toggle('invisible');
            sidebarOverlay.classList.toggle('opacity-100');
            sidebarOverlay.classList.toggle('visible');

            const icon = mobileNavToggle.querySelector('i');
            if (sidebar.classList.contains('translate-x-0')) {
                icon.className = 'fas fa-times text-lg';
            } else {
                icon.className = 'fas fa-bars text-lg';
            }
        }

        mobileNavToggle.addEventListener('click', toggleMobileNav);
        sidebarOverlay.addEventListener('click', toggleMobileNav);

        // Close mobile nav when clicking on nav links
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    toggleMobileNav();
                }
            });
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                sidebarOverlay.classList.add('opacity-0', 'invisible');
                sidebarOverlay.classList.remove('opacity-100', 'visible');
                mobileNavToggle.querySelector('i').className = 'fas fa-bars text-lg';
            }
        });

        // Initial load functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Set active navigation state for Messages
            document.querySelector('.nav-link.active').classList.add('bg-gradient-to-r', 'from-chili-red', 'to-tangelo', 'border-ut-orange', 'transform', 'translate-x-2');
            
            renderConversationList(); // Initial render of conversation list

            // If a conversation_id is passed in the URL (e.g., after sending a message)
            // open that conversation automatically
            if (selectedConversationId) {
                openConversation(selectedConversationId, chatRecipientName.textContent, selectedConversationMessages);
            }
        });
    </script>
</body>
</html>