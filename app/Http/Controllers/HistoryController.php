<?php

namespace App\Http\Controllers;

use App\Enums\DepartmentEnum;
use App\Exports\HistoryExport;
use App\Http\Controllers\Api\Traits\ResponseTrait;
use App\Models\Cost;
use App\Repositories\HistoryRepository;
use App\Repositories\HourlyDifferenceRepository;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Inertia\ResponseFactory;
use Maatwebsite\Excel\Facades\Excel;

class HistoryController extends Controller
{
    use ResponseTrait;

    public function __construct(
        protected HourlyDifferenceRepository $hourlyDifferenceRepository,
        protected HistoryRepository          $historyRepository
    ) {}

    public function byHour(): Response|ResponseFactory
    {
        $date = request('date', now()->format('Y-m-d'));
        $cacheKey = 'report_by_hour_' . $date;

        if (Cache::has($cacheKey)) {
            return inertia('Histories/ByHour', Cache::get($cacheKey));
        }

        $hours = range(0, 23);
        if (Carbon::parse($date)->isToday()) {
            $hours = range(now()->hour - 3, now()->hour);
        }

        $query = DB::table(DB::raw("(SELECT " . implode(" AS hour UNION SELECT ", $hours) . " AS hour) as hours_table"))
            ->crossJoin(DB::raw("(SELECT DISTINCT slave_id FROM hourly_difference WHERE date = '$date') as slaves"))
            ->leftJoin('hourly_difference', function ($join) use ($date) {
                $join->on('hours_table.hour', '=', 'hourly_difference.hour')
                    ->on('slaves.slave_id', '=', 'hourly_difference.slave_id')
                    ->where('hourly_difference.date', '=', $date);
            })
            ->select(
                'slaves.slave_id',
                'hours_table.hour',
                DB::raw('COALESCE(hourly_difference.hourly_consumption, 0) as hourly_consumption')
            )
            ->orderBy('slaves.slave_id')
            ->orderBy('hours_table.hour')
            ->get();

        $data = [
            'statistics' => $this->dataByFactory($query),
            'date' => $date,
        ];

        Cache::put($cacheKey, $data, now()->addMinutes(7));

        return inertia('Histories/ByHour', $data);
    }

    private function dataByFactory($query, $orderBy = 'hour', $groupBy = 'hour', $sumBy = 'hourly_consumption'): array
    {
        $factories = config('factory_setting');
        $statistics = [];
        foreach ($factories as $factory) {
            if (!$factory['active']) {
                continue;
            }

            $data = $query->whereIn('slave_id', $factory['slave_id']);
            $data = $data->sortBy($orderBy)->groupBy($groupBy)->map(function ($items, $hour) use ($sumBy) {
                try {
                    $day = Carbon::parse($hour)->format('d/m');
                } catch (\Exception $e) {
                    $day = null;
                }

                return [
                    'hour' => "$hour:00",
                    'day' => $day,
                    'value' => round($items->sum($sumBy), 2),
                ];
            })->values()->toArray();

            $statistics[] = [
                'name' => $factory['name'],
                'data' => array_column($data, 'value'),
                'hours' => array_column($data, 'hour'),
                'days' => array_column($data, 'day'),
            ];
        }

        return $statistics;
    }

    public function byDay(): Response|ResponseFactory|RedirectResponse
    {
        $subDay = $this->getSubDays();
        $dateFrom = Carbon::parse(request('date_from', now()->subDays($subDay)->format('Y-m-d')));
        $dateTo = Carbon::parse(request('date_to', $dateFrom->copy()->addDays($subDay)->format('Y-m-d')));

        if ($dateFrom->gt($dateTo)) {
            return back()->withErrors(['Ngày bắt đầu phải nhỏ hơn ngày kết thúc.']);
        }

        if ($dateTo->diffInDays($dateFrom) > 31) {
            return back()->withErrors(['Khoảng thời gian tối đa là 31 ngày.']);
        }

        $cacheKey = 'report_by_day_' . $dateFrom->format('Ymd') . '_' . $dateTo->format('Ymd');

        if (Cache::has($cacheKey)) {
            return inertia('Histories/ByDay', Cache::get($cacheKey));
        }

        $days = [];
        $daysInMonth = [];
        $startDate = Carbon::parse($dateFrom);
        $endDate = Carbon::parse($dateTo);

        while ($startDate->lte($endDate)) {
            $days[] = $startDate->format('Y-m-d');
            $daysInMonth[] = $startDate->format('d/m');
            $startDate->addDay();
        }

        $query = DB::table(DB::raw("(SELECT '" . implode("' AS date UNION SELECT '", $days) . "' AS date) as days_table"))
            ->crossJoin(DB::raw("(SELECT DISTINCT slave_id FROM daily_difference WHERE date BETWEEN '$dateFrom' AND '$dateTo') as slaves"))
            ->leftJoin('daily_difference', function ($join) {
                $join->on('days_table.date', '=', 'daily_difference.date')
                    ->on('slaves.slave_id', '=', 'daily_difference.slave_id');
            })
            ->select(
                'slaves.slave_id',
                'days_table.date',
                DB::raw('COALESCE(daily_difference.daily_consumption, 0) as daily_consumption')
            )
            ->orderBy('slaves.slave_id')
            ->orderBy('days_table.date')
            ->get();

        $costs = Cost::query()
            ->whereBetween('date', [$dateFrom->format('Y-m-d'), $dateTo->format('Y-m-d')])
            ->get();

        $factories = config('factory_setting');
        $_costs = [];
        foreach ($factories as $factory) {
            if (!$factory['active']) {
                continue;
            }

            $_costs[] = [
                'name' => $factory['name'],
                'cost' => $costs->whereIn('slave_id', $factory['slave_id'])->sum('cost'),
            ];
        }

        $data = [
            'statistics' => $this->dataByFactory($query, 'date', 'date', 'daily_consumption'),
            'dateFrom' => $dateFrom->format('Y-m-d'),
            'dateTo' => $dateTo->format('Y-m-d'),
            'days' => $daysInMonth,
            'costs' => $_costs,
            'costMonth' => now()->format('m/Y'),
        ];

        Cache::put($cacheKey, $data, now()->addMinutes(7));

        return inertia('Histories/ByDay', $data);
    }

    public function index(Request $request): Response|ResponseFactory
    {
        $histories = $this->historyRepository
            ->orderBy('datetime', 'desc')
            ->paginate(request('size', 30));

        return inertia('Histories/Index', [
            'histories' => $histories,
            'departments' => DepartmentEnum::listDepartment(),
            'dateFrom' => $request->input('dateFrom'),
            'dateTo' => $request->input('dateTo'),
            'filter' => $request->input('filter'),
        ]);
    }

    public function export()
    {
        $histories = $this->historyRepository
            ->orderBy('datetime', 'desc');
        if ((clone $histories)->count() > 10000) {
            return redirect()->back()->withErrors(['Số lượng bản ghi quá lớn. Vui lòng lọc thêm điều kiện.']);
        }

        $histories = $histories->get();
        return Excel::download(new HistoryExport($histories), 'Data.xlsx');
    }

    private function getHours()
    {
        $_hours = [
            1 => range(0, 5),
            2 => range(6, 11),
            3 => range(12, 17),
            4 => range(17, 23),
        ];

        $currentHour = Carbon::now()->hour;

        // 1. Tìm tất cả các step hợp lệ (có chứa bất kỳ giờ nào ≤ hiện tại)
        $validSteps = [];
        foreach ($_hours as $step => $range) {
            if (array_filter($range, fn($hour) => $hour <= $currentHour)) {
                $validSteps[] = $step;
            }
        }

        // 2. Lấy step trước đó từ Cache để tránh trùng lặp
        $lastStep = Cache::get('last_used_step', null);

        // 3. Nếu có nhiều step hợp lệ, chọn step kế tiếp so với lần trước (vòng lặp)
        if ($lastStep !== null && in_array($lastStep, $validSteps)) {
            $currentIndex = array_search($lastStep, $validSteps);
            $nextIndex = ($currentIndex + 1) % count($validSteps); // Lấy step tiếp theo (vòng lại từ đầu nếu hết)
            $nextStep = $validSteps[$nextIndex];
        } else {
            $nextStep = reset($validSteps); // Lấy step đầu tiên nếu chưa có step trước
        }

        // 4. Lưu step mới vào Cache
        Cache::set('last_used_step', $nextStep);

        return $_hours[$nextStep];
    }

    private function getSubDays(): int
    {
        $days = [
            1 => 2,
            2 => 1,
            3 => 0,
        ];

        $dayStep = Cache::get('day_step', 1);

        if (!isset($days[$dayStep])) {
            Cache::set('day_step', 1);
            $dayStep = 1;
        }

        $subDay = $days[$dayStep];

        if ($dayStep == 3) {
            Cache::set('day_step', 1);
        } else {
            Cache::set('day_step', $dayStep + 1);
        }

        return $subDay;
    }
}
