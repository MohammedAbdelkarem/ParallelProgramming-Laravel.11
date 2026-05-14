<?php

namespace App\Traits;

use Carbon\Carbon;

trait TimeFormatter
{
    /**
     * @param int $time         Time string to pass
     * @param int $diffToNow    The amount of seconds which return "now string"
     * @param int $diff         The amount of hours that make time retrun in "diffForHumans" format
     * @param bool $translated  Get the time translated
     * @param bool $daysOnly    Get the time in days diff only ex: (15 days ago) instead of (2 weeks ago)
     * @param string $format    Time format if it pass the (diff) amount
     *
     * @return string
     *
     */
    protected function getHumanReadableTime($time, int $diff = -48, $diffToNow = -180, bool $translated = true, bool $daysOnly = false, string $format = "Y/m/d g:i A"): string
    {
        if (!$time)
            return "";
        $time = Carbon::parse($time)->utcOffset(config('_custom.user_time_zone', 0));
        $now = Carbon::now()->utcOffset(config('_custom.user_time_zone', 0));
        $translated ? $local = app()->getLocale() : $local = "en";
        //Get Now
        if ($now->diffInSeconds($time, false) > $diffToNow) return __("resources.now", [], $local);
        //Get differnce
        if ($now->diffInHours($time, false) > $diff) {
            if ($daysOnly && $now->diffInHours($time, false) < - (3 * 24)) {
                $totalDays = round($time->locale($local)->diffAsCarbonInterval()->totalDays);
                if ($totalDays <= 10)
                    return __("resources.day_ago", ["day" => $totalDays], $local); //أيام بدل يوم
                return __("resources.days_ago", ["day" => $totalDays], $local);
            }
            return $time->locale($local)->diffForHumans();
        }
        $translated ?
            $timeToReturn = $time->translatedFormat($format)
            : $timeToReturn = $time->format($format);
        return $timeToReturn;
    }
}