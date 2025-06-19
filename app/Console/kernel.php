<?php

protected $commands = [
    \App\Console\Commands\MakeFetNetComponent::class,
    \App\Console\Commands\ParseFet::class, // ← Ini harus cocok dengan nama file dan nama class
];
protected function schedule(Schedule $schedule)
{
    $schedule->command('fet:watch')->everyMinute();
}
