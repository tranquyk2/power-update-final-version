<?php

namespace App\Observers;

use App\Enums\DepartmentEnum;
use App\Models\History;
use Illuminate\Support\Facades\DB;

class HistoryObserver
{
    public function created(History $history)
    {
        $datetime = $history->datetime;
        $datetime_limit = $datetime->copy()->subMinutes(5);

        if (in_array($history->slave_id, [DepartmentEnum::AI->value, DepartmentEnum::Tuner2->value])) {
            DB::update("
            UPDATE histories ai
            JOIN histories tuner2
            ON ai.datetime BETWEEN tuner2.datetime - INTERVAL 30 SECOND AND tuner2.datetime + INTERVAL 30 SECOND
            AND tuner2.datetime > ?
            SET ai.kw = GREATEST(ai.kw_draft - tuner2.kw, 0)
            WHERE ai.slave_id = ?
            AND tuner2.slave_id = ?
            AND ai.datetime BETWEEN ? - INTERVAL 30 SECOND AND ? + INTERVAL 30 SECOND
            AND ai.datetime > ?
        ", [$datetime_limit, DepartmentEnum::AI->value, DepartmentEnum::Tuner2->value, $datetime, $datetime, $datetime_limit]);
        }

        if (in_array($history->slave_id, [DepartmentEnum::SMPS->value, DepartmentEnum::Tuner1->value, DepartmentEnum::SMT->value])) {
            DB::update("
            UPDATE histories smt
            LEFT JOIN histories smps 
            ON smps.slave_id = ? 
            AND smt.datetime BETWEEN smps.datetime - INTERVAL 30 SECOND AND smps.datetime + INTERVAL 30 SECOND
            AND smps.datetime > ?
            LEFT JOIN histories tuner1
            ON tuner1.slave_id = ? 
            AND smt.datetime BETWEEN tuner1.datetime - INTERVAL 30 SECOND AND tuner1.datetime + INTERVAL 30 SECOND
            AND tuner1.datetime > ?
            SET smt.kw = GREATEST(smt.kw_draft - COALESCE(smps.kw, 0) + COALESCE(tuner1.kw, 0), 0)
            WHERE smt.slave_id = ? 
            AND smt.datetime BETWEEN ? - INTERVAL 30 SECOND AND ? + INTERVAL 30 SECOND
            AND smt.datetime > ?
        ", [DepartmentEnum::SMPS->value, $datetime_limit, DepartmentEnum::Tuner1->value, $datetime_limit, DepartmentEnum::SMT->value, $datetime, $datetime, $datetime_limit]);
        }
    }
}
