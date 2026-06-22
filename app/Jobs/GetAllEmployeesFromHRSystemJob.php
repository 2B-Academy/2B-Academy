<?php

namespace App\Jobs;

use App\Models\JobTitle;
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

        // Cache job-title rows keyed by jobName so the per-employee
        // upsert stays O(distinct jobs) instead of O(employees).
        $jobTitleCache = [];
        $resolveTitleId = static function (?string $jobName) use (&$jobTitleCache): ?int {
            $jobName = $jobName !== null ? trim($jobName) : '';
            if ($jobName === '') {
                return null;
            }
            if (array_key_exists($jobName, $jobTitleCache)) {
                return $jobTitleCache[$jobName];
            }
            $row = JobTitle::firstOrCreate(['name' => $jobName]);
            return $jobTitleCache[$jobName] = $row->id;
        };

        // 2026 admin Users redesign dropped the denormalized
        // `users.job_title` string column. The link is now a proper
        // FK (`users.job_title_id` → `job_titles.id`) that we keep
        // aligned with HR's `jobName` field on every sync.
        User::upsert($employees->map(static function ($employee) use ($resolveTitleId) {
            return [
                'system_id'       => $employee->id,
                'name'            => $employee->name,
                'email'           => $employee->email,
                'phone'           => $employee->phone,
                'machine_code'    => $employee->machineCode,
                'department_name' => $employee->departmentName,
                'job_title_id'    => $resolveTitleId($employee->jobName ?? null),
            ];
        })->toArray(), ['system_id'], ['name','email','phone','machine_code','department_name','job_title_id']);
    }
}
