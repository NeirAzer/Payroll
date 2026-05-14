<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Override;

class Attendance extends Model
{
    protected $guarded = ['id'];
    protected $with = ['user'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isLate()
    {
        $scheduleStartTime = Carbon::parse($this->schedule_start_time);
        $startTime = Carbon::parse($this->start_time);

        return $startTime->greaterThan($scheduleStartTime);
    }

    public function workDuration()
    {
        $startTime = Carbon::parse($this->start_time);
        $endTime = Carbon::parse($this->end_time);

        $duration = $endTime->diff($startTime);

        $hours = $duration->h;
        $minutes = $duration->i;

        return $hours . ' jam ' . $minutes . ' menit';
    }

    protected static function booted(): Void
    {
        static::saving(function ($attendance) {
            if ($attendance->start_time && $attendance->end_time) {
                $start = Carbon::parse($attendance->start_time);
                $end = Carbon::parse($attendance->end_time);

                if ($end->lessThan($start)) {
                    $end->addDay();
                }

                $totalSeconds = $start->diffInSeconds($end);

                $data['duration'] = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            }
        });
    }
}
