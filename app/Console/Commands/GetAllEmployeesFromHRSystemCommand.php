<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\HRSystemService;
use Illuminate\Console\Command;

class GetAllEmployeesFromHRSystemCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:employees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get All Employees From HR System';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hrService = new HRSystemService();
        $page = 0;
        $batchSize = 10;

        while (true) {
            // Get all employees from API
            $employees = $hrService->getAllEmployees();

            if ($employees->isEmpty()) {
                $this->info('no employees');
                break;
            }
            // Slice the next batch
            $batch = $employees->slice($page * $batchSize, $batchSize);
            if ($batch->isEmpty()) {
                break;
            }
            foreach ($batch as $employee) {
                User::updateOrCreate(
                    ['system_id' => $employee->id], // unique key
                    [
                        'name' => $employee->name,
                        'email' => $employee->email,
                        'phone' => $employee->phone,
                        'machine_code' => $employee->machineCode,
                        'department_name' => $employee->departmentName,
                        'updated_at' => now(),
                    ]
                );
            }

            $this->info("Processed batch " . ($page + 1));
            $page++;
        }
    }
}
