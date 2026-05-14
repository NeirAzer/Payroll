<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class Payroll extends Component
{
    public $start_date;
    public $end_date;
    public $user_id;
    public $leave_pay = 0;

    public $pegawai;
    public $total_duration = '00:00:00';
    public $total_hour = 0 ;
    public $total_salary = 0;

    public $rate_per_hour = 35000;

    public function render()
    {
        $users = User::all();

        return view('livewire.payroll', compact('users'))->layout('layouts.main');
    }

    public function calculate()
    {
        $this->validate([
            'user_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $this->pegawai = User::find($this->user_id);

        $start = Carbon::parse($this->start_date)->startOfDay();
        $end = Carbon::parse($this->end_date)->endOfDay();

        $attendances = Attendance::where('user_id', $this->user_id)
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('duration')
            ->get();

        $attendanceSeconds = $attendances->sum(function ($item) {
                    return strtotime($item->duration) - strtotime('00:00:00');
        });

        // Detik Cuti

        $schedule = Schedule::where('user_id', $this->user_id)->first();
        $scheduleStart = Carbon::parse($schedule->shift->start_time);

        $scheduleEnd = Carbon::parse($schedule->shift->end_time);

        $scheduleSeconds = $scheduleStart->diffInSeconds($scheduleEnd);
        $cutis = Leave::where('user_id', $this->user_id)
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('status', ['approved', 'pending'])
            ->get();

        $totalLeaveDays = $cutis->count();
        $leaveSeconds = $totalLeaveDays * $scheduleSeconds;
        
        $leaveHours = $leaveSeconds / 3600;

        $this->leave_pay = $leaveHours * $this->rate_per_hour;

        $totalSeconds = $attendanceSeconds + $leaveSeconds;

        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        $this->total_duration = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

        $this->total_hour = $totalSeconds / 3600;

        $this->total_salary = $this->total_hour * $this->rate_per_hour;
    }

    public function getFormattedDurationProperty()
    {
        if (!$this->total_duration) return 'null';

        [$jam, $menit, $detik] = explode(':', $this->total_duration);
        return /*(int)*/$jam . ' Jam ' . /*(int)*/$menit . ' Menit ' . /*(int)*/$detik . ' Detik';
    }
}
