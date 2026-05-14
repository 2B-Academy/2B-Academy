<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\HRSystemService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetAllEmployeesFromHRSystemJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $hrService = new HRSystemService();
        $employees = $hrService->getAllEmployees();
        User::upsert($employees->map(function ($employee) {
            return [
                'system_id'       => $employee->id,
                'name'            => $employee->name,
                'email'           => $employee->email,
                'phone'           => $employee->phone,
                'machine_code'    => $employee->machineCode,
                'department_name' => $employee->departmentName,
            ];
        })->toArray(), ['system_id'], ['name','email','phone','machine_code','department_name']);
    }
}
