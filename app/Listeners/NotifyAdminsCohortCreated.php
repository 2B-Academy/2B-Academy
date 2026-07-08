<?php

namespace App\Listeners;

use App\Events\CourseCohortCreated;
use App\Models\Admin;
use App\Notifications\CohortCreatedNotification;

class NotifyAdminsCohortCreated
{
    public function handle(CourseCohortCreated $event): void
    {
        foreach (Admin::all() as $admin) {
            $admin->notify(new CohortCreatedNotification($event->section));
        }
    }
}
