<?php

namespace App\Support;

class TimeGreeting
{
    /**
     * Get appropriate Indonesian greeting based on Asia/Jakarta current hour.
     */
    public static function greeting(): string
    {
        $hour = (int) now()->timezone('Asia/Jakarta')->format('H');

        return match (true) {
            $hour >= 4 && $hour < 11 => 'Selamat pagi',
            $hour >= 11 && $hour < 15 => 'Selamat siang',
            $hour >= 15 && $hour < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };
    }
}
