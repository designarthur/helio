<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\DriverLog;
use App\Models\User; // The model used for driver authentication
use App\Models\Vendor; // For authentication fallback
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; // For database raw queries

class DriverLogController extends Controller
{
    // Define HOS rules (simplified for conceptual calculation)
    const HOS_RULES = [
        'DRIVE_TIME_LIMIT_MINUTES' => 11 * 60, // 11 hours
        'SHIFT_TIME_LIMIT_MINUTES' => 14 * 60, // 14 hours
        'CYCLE_TIME_LIMIT_MINUTES' => 70 * 60, // 70 hours (for 8-day cycle)
        'BREAK_REQUIRED_MINUTES' => 30, // 30 minutes
        'BREAK_AFTER_DRIVE_MINUTES' => 8 * 60, // after 8 hours of driving
        'OFF_DUTY_REQUIRED_MINUTES' => 10 * 60, // 10 consecutive hours off duty
    ];

    /**
     * Display the driver log (HOS) dashboard.
     * This will show current status, timers, and today's log.
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
        $today = Carbon::now()->toDateString(); // Today's date in 'YYYY-MM-DD'

        // Fetch today's log entries for the driver
        $dailyLogEntries = DriverLog::where('driver_id', $driverId)
                                     ->whereDate('start_time', $today)
                                     ->orderBy('start_time', 'asc')
                                     ->get();

        // Calculate current HOS status and remaining times
        $currentHOS = $this->calculateCurrentHOS($driverId, $dailyLogEntries);

        return view('driver.driver_log.index', compact(
            'user', // The authenticated driver
            'dailyLogEntries',
            'currentHOS' // Pass HOS calculations to the view
        ));
    }

    /**
     * Handle driver status changes (log a new HOS entry).
     */
    public function store(Request $request)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $driverId = $user->id;
        $vendorId = $user->vendor_id;
        $now = Carbon::now();

        $validatedData = $request->validate([
            'status' => ['required', 'string', Rule::in(['OFF_DUTY', 'SLEEPER_BERTH', 'DRIVING', 'ON_DUTY_NOT_DRIVING'])],
            'location' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ]);

        // Find the last active log entry for today
        $lastLog = DriverLog::where('driver_id', $driverId)
                            ->whereDate('start_time', $now->toDateString())
                            ->whereNull('end_time') // Find the currently open log entry
                            ->orderByDesc('start_time')
                            ->first();

        // Close the previous log entry if one exists and is active
        if ($lastLog && is_null($lastLog->end_time)) {
            $lastLog->end_time = $now;
            $lastLog->duration_minutes = $lastLog->start_time->diffInMinutes($now);
            $lastLog->save();
        }

        // Create a new log entry
        $newLog = DriverLog::create([
            'driver_id' => $driverId,
            'vendor_id' => $vendorId,
            'status' => $validatedData['status'],
            'start_time' => $now,
            'end_time' => null, // This is the currently active segment
            'duration_minutes' => null,
            'location' => $validatedData['location'] ?? null,
            'remarks' => $validatedData['remarks'] ?? null,
        ]);

        return redirect()->route('driver.driver_log.index')->with('success', 'Status updated to ' . str_replace('_', ' ', $newLog->status) . '.');
    }

    /**
     * Display past driver logs (conceptual list/calendar view).
     */
    public function showPastLogs(Request $request)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            Auth::guard('driver')->logout();
            return redirect()->route('driver.login')->with('error', 'Authentication failed.');
        }

        $driverId = $user->id;

        // Fetch past daily summaries or detailed logs, depending on implementation
        // For simplicity, let's fetch all logs not from today
        $pastLogs = DriverLog::where('driver_id', $driverId)
                             ->whereDate('start_time', '!=', Carbon::now()->toDateString())
                             ->orderByDesc('start_time')
                             ->paginate(20);

        return view('driver.driver_log.past_logs', compact('user', 'pastLogs'));
    }

    // No direct edit/update/destroy methods for individual log entries for compliance/auditability.
    // Changes would typically involve adding a new log entry to correct an error.

    /**
     * Helper function to calculate current HOS status and remaining times.
     * This is a simplified calculation and would be much more complex in a real ELD-compliant system.
     */
    private function calculateCurrentHOS($driverId, $dailyLogEntries)
    {
        $today = Carbon::now()->toDateString();
        $startOfDay = Carbon::parse($today); // 00:00:00 today

        $totalDriveTimeToday = 0; // minutes
        $totalOnDutyTimeToday = 0; // minutes (includes driving)
        $lastOffDutyEnd = null; // Last time driver went off duty

        foreach ($dailyLogEntries as $log) {
            $logStart = $log->start_time;
            $logEnd = $log->end_time ?? Carbon::now(); // Use current time if log is open

            $duration = $logStart->diffInMinutes($logEnd);

            if ($log->status === 'DRIVING') {
                $totalDriveTimeToday += $duration;
                $totalOnDutyTimeToday += $duration;
            } elseif ($log->status === 'ON_DUTY_NOT_DRIVING' || $log->status === 'SLEEPER_BERTH') {
                $totalOnDutyTimeToday += $duration;
            }

            if ($log->status === 'OFF_DUTY' || $log->status === 'SLEEPER_BERTH') {
                 $lastOffDutyEnd = $logEnd;
            }
        }

        // Current Status (from the last log entry)
        $currentStatus = $dailyLogEntries->last()->status ?? 'OFF_DUTY'; // Default if no logs today

        // Calculate remaining times (simplified)
        $driveTimeRemaining = max(0, self::HOS_RULES['DRIVE_TIME_LIMIT_MINUTES'] - $totalDriveTimeToday);
        $shiftTimeRemaining = max(0, self::HOS_RULES['SHIFT_TIME_LIMIT_MINUTES'] - $totalOnDutyTimeToday);

        // Cycle time: Very complex, often based on 7-day or 8-day cycle.
        // For simplicity, we'll just show the full limit unless a violation is conceptual.
        $cycleTimeRemaining = self::HOS_RULES['CYCLE_TIME_LIMIT_MINUTES']; // Simplified, not truly calculating usage across days

        // Violations (simplified)
        $violations = [];
        if ($totalDriveTimeToday >= self::HOS_RULES['DRIVE_TIME_LIMIT_MINUTES']) {
            $violations[] = 'Drive time exceeded';
        }
        if ($totalOnDutyTimeToday >= self::HOS_RULES['SHIFT_TIME_LIMIT_MINUTES']) {
            $violations[] = 'Shift time exceeded';
        }
        // Could add break violations, 10-hour rest violations etc.

        return [
            'current_status' => str_replace('_', ' ', $currentStatus),
            'drive_time_remaining' => $this->formatMinutesToHoursMinutes($driveTimeRemaining),
            'shift_time_remaining' => $this->formatMinutesToHoursMinutes($shiftTimeRemaining),
            'cycle_time_remaining' => $this->formatMinutesToHoursMinutes($cycleTimeRemaining),
            'violations_messages' => $violations,
            'has_violations' => !empty($violations),
            'total_drive_time_today' => $totalDriveTimeToday,
            'total_on_duty_time_today' => $totalOnDutyTimeToday,
        ];
    }

    /**
     * Helper to format minutes into "Xh Ym" string.
     */
    private function formatMinutesToHoursMinutes($totalMinutes)
    {
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        return "{$hours}h {$minutes}m";
    }
}