<?php

namespace App\Services;

use App\Enums\DepartmentEnum;
use App\Models\ByHour;
use App\Models\Cost;
use App\Models\History;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Log;

class CostService
{
    public static function calculateCostByMonth($year, $month)
    {
        for ($i = 1; $i <= Carbon::create($year, $month)->daysInMonth; $i++) {
            for ($hour = 0; $hour < 24; $hour++) {
                self::calculateCost(Carbon::create($year, $month, $i, $hour)->format('Y-m-d H:i:s'));
            }
        }
    }

    public static function calcDay() {
        for ($hour = 0; $hour < 24; $hour++) {
            self::calculateCost(Carbon::create(2025, 3, 12, $hour)->format('Y-m-d H:i:s'));
        }
    }

    public static function calculateCost($date)
    {
        $slaveIds = array_column(array_filter(DepartmentEnum::listDepartment(), function ($item) {
            return $item['enabled'];
        }), 'id');

        $now = Carbon::parse($date);
        $hourAgo = $now->copy()->subHours();
        $currentHourStart = $now->copy()->startOfHour();
        $currentHourEnd = $now->copy()->endOfHour();
        $halfHourMark = $currentHourStart->copy()->addMinutes(30)->endOfMinute();

        foreach ($slaveIds as $slaveId) {
            $previous = History::query()
                ->where('slave_id', $slaveId)
                ->where('datetime', '<', $hourAgo->endOfHour())
                ->orderBy('datetime', 'desc')
                ->first();

            $previousKw = $previous->kw ?? 0;

            $history = History::query()
                ->where('slave_id', $slaveId)
                ->whereBetween('datetime', [$currentHourStart, $currentHourEnd])
                ->orderBy('datetime')
                ->get();

            // Chia dữ liệu thành hai nửa
            $halfPart1 = $history->where('datetime', '<=', $halfHourMark)->last();
            $halfPart2 = $history->where('datetime', '>', $halfHourMark)->last();

            // Đảm bảo dữ liệu hợp lệ
            $kwHalf1 = max(($halfPart1->kw ?? 0) - $previousKw, 0);
            if ($halfPart1 && $halfPart2) {
                $kwHalf2 = max(($halfPart2->kw - $halfPart1->kw), 0);
            } else if ($halfPart2 && $previous) {
                $kwHalf2 = max(($halfPart2->kw ?? 0) - $previousKw, 0);
            } else if ($halfPart2) {
                $kwHalf2 = $halfPart2->kw;
            } else {
                $kwHalf2 = 0;
            }

            // Tính toán chi phí điện cho từng nửa
            $halfPart1Cost = 0;
            $halfPart2Cost = 0;
            if ($slaveId === DepartmentEnum::Factory1->value) {
                if ($halfPart1) {
                    $halfPart1Cost = self::calculateElectricityCost($currentHourStart->format('Y-m-d H:i:s'), $halfHourMark->format('Y-m-d H:i:s'), $kwHalf1);
                }

                if ($halfPart2) {
                    $halfPart2Cost = self::calculateElectricityCost($halfHourMark->format('Y-m-d H:i:s'), $currentHourEnd->format('Y-m-d H:i:s'), $kwHalf2);
                }
            } else {
                if ($halfPart1) {
                    $halfPart1Cost = self::calculateElectricityCostFactory2($currentHourStart->format('Y-m-d H:i:s'), $halfHourMark->format('Y-m-d H:i:s'), $kwHalf1);
                }

                if ($halfPart2) {
                    $halfPart2Cost = self::calculateElectricityCostFactory2($halfHourMark->format('Y-m-d H:i:s'), $currentHourEnd->format('Y-m-d H:i:s'), $kwHalf2);
                }
            }

            $totalCost = $halfPart1Cost + $halfPart2Cost;

            // Lưu vào DB nếu có thay đổi
            $cost = Cost::query()
                ->where('slave_id', $slaveId)
                ->firstWhere('date', $currentHourStart->format('Y-m-d'));

            if ($cost && $cost->last_executed_at && $cost->last_executed_at->greaterThanOrEqualTo($currentHourStart)) {
                continue;
            }

            if ($cost) {
                $cost->update([
                    'cost' => $cost->cost + $totalCost,
                    'last_executed_at' => $currentHourStart,
                ]);
            } else {
                Cost::query()->create([
                    'date' => $currentHourStart->format('Y-m-d'),
                    'cost' => $totalCost,
                    'last_executed_at' => $currentHourStart,
                    'slave_id' => $slaveId,
                ]);
            }
        }
    }

    private static function getTariffRatesInRange($datetimeFrom, $datetimeTo, $tariffData)
    {
        $from = new DateTime($datetimeFrom);
        $to = new DateTime($datetimeTo);

        $hourMinute = (int) $from->format('Hi'); // Chuyển về số để so sánh dễ hơn
        $dayOfWeek = $from->format('N'); // 1 = Thứ Hai, 7 = Chủ Nhật
        $rateType = "gio_binh_thuong"; // Mặc định là giờ bình thường

        foreach (["gio_cao_diem", "gio_thap_diem"] as $priorityType) {
            foreach ($tariffData[$priorityType]['khung_gio'] as $period) {
                [$start, $end, $days] = $period;
                $startHM = (int) str_replace(':', '', $start);
                $endHM = (int) str_replace(':', '', $end);

                if (
                    ($days === 'tat_ca_ngay') ||
                    ($days === 'chu_nhat' && $dayOfWeek == 7) ||
                    ($days === 'thu_hai_den_thu_bay' && $dayOfWeek >= 1 && $dayOfWeek <= 6)
                ) {
                    if (
                        ($startHM <= $endHM && $hourMinute >= $startHM && $hourMinute < $endHM) ||
                        ($startHM > $endHM && ($hourMinute >= $startHM || $hourMinute < $endHM))
                    ) {
                        $rateType = $priorityType;
                        break 2;
                    }
                }
            }
        }

        return $rateType;
    }

    private static function calculateElectricityCost($datetimeFrom, $datetimeTo, $consumption)
    {
        $tariffData = config('electricity_time_frames.factory1');
        $rate = self::getTariffRatesInRange($datetimeFrom, $datetimeTo, $tariffData);

        return $consumption * $tariffData[$rate]['don_gia'];
    }

    private static function calculateElectricityCostFactory2($datetimeFrom, $datetimeTo, $consumption)
    {
        $tariffData = config('electricity_time_frames.factory2');
        $rate = self::getTariffRatesInRange($datetimeFrom, $datetimeTo, $tariffData);

        return $consumption * $tariffData[$rate]['don_gia'];
    }
}
