<?php

namespace App\Console\Commands;

use App\Enums\DepartmentEnum;
use App\Models\ByHour;
use App\Models\Cost;
use App\Models\History;
use App\Models\HourlyDifference;
use App\Services\CostService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CalculateCostCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate-cost';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        CostService::calculateCost(now()->subHour()->format('Y-m-d H:i:s'));
        $this->info('Cost calculated successfully.');
    }
}
