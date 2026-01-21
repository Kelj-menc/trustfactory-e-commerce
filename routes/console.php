<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use App\Jobs\SendDailySalesReportJob;
use Illuminate\Support\Facades\Schedule;

// every day at 17:15 send daily sales report to admin
Schedule::job(new SendDailySalesReportJob)->dailyAt('16:45');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
