<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Schedule;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Presensi extends Component
{
    public $latitute;
    public $longitude;
    public $insideRadius = false;

    public function render()
    {
        $schedule = Schedule::where('user_id', Auth::id())->first();
        $insideRadius = $this->insideRadius;
        $attendance = Attendance::where('user_id', Auth::id())->whereDate('created_at', now())->first();

        return view('livewire.presensi', compact('schedule', 'insideRadius', 'attendance'))->layout('layouts.main');
    }

    public function store()
    {
        $this->validate([
            'latitute' => 'required',
            'longitude' => 'required',
        ]);

        $schedule = Schedule::where('user_id', Auth::id())->first();

        if ($schedule) {
            $attendance = Attendance::where('user_id', Auth::id())->whereDate('created_at', now())->first();

            if (!$attendance) {
                
                $attendance = Attendance::create([
                    'user_id' => Auth::user()->id,
                    'schedule_latitude' => $schedule->office->latitude,
                    'schedule_longitude' => $schedule->office->longitude,
                    'schedule_start_time' => $schedule->shift->start_time,
                    'schedule_end_time' => $schedule->shift->end_time,
                    'latitude' => $this->latitute,
                    'longitude' => $this->longitude,
                    'start_time' => Carbon::now()->toTimeString(),
                    'end_time' => Carbon::now()->toTimeString(),
                ]);

            } else {
                $attendance->update([
                    'latitude' => $this->latitute,
                    'longitude' => $this->longitude,
                    'end_time' => Carbon::now()->toTimeString(),
                ]);
            }


            // return redirect()->route('presensi',[
            //         'schedule' => $schedule,
            //         'insideRadius' => false,
            //     ]);

            Notification::make()
                ->title('Presensi Berhasil')
                ->success()
                ->body('Presensi Berhasil')
                ->send();

            return redirect('/dashboard/attendances');
        }
    }
}
