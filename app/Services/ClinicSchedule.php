<?php

namespace App\Services;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClinicSchedule
{
    public function slotTimes(): array
    {
        $start = Carbon::createFromFormat('H:i', config('clinic.opening_time'));
        $end = Carbon::createFromFormat('H:i', config('clinic.closing_time'));
        $step = (int) config('clinic.slot_minutes');

        $slots = [];
        while ($start->copy()->addMinutes($step)->lte($end)) {
            $slots[] = $start->format('H:i');
            $start->addMinutes($step);
        }

        return $slots;
    }

    public function dailyCapacity(): int
    {
        return count($this->slotTimes());
    }

    public function isClinicDay(Carbon $date): bool
    {
        if (DB::table('blocked_dates')->where('blocked_date', $date->toDateString())->exists()) {
            return false;
        }

        return in_array($date->dayOfWeek, config('clinic.days'), true);
    }

    public function openSlots(Carbon $date): array
    {
        if (! $this->isClinicDay($date)) {
            return [];
        }

        $taken = Appointment::active()
            ->whereDate('appointment_date', $date)
            ->pluck('appointment_time')
            ->all();

        return array_values(array_diff($this->slotTimes(), $taken));
    }

    public function daysLabel(): string
    {
        $names = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        return collect(config('clinic.days'))->sort()->map(fn ($d) => $names[$d])->implode(', ');
    }
}