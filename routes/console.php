<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Previously defined in app/Console/Kernel.php
Schedule::command('sync:employees')->daily();

// Roll cohort + course statuses forward every day right after
// midnight so the persisted columns track the calendar. Resources
// also derive these on the fly for live reads — this job exists so
// raw SQL consumers (reports, dashboards) stay in sync.
Schedule::command('cohorts:sync-statuses')->dailyAt('00:05');
