<?php

namespace Database\Seeders;

use App\Models\History;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenerateTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kW = 1.25;
        $currentTime = Carbon::parse('2024-01-01 00:00:00');

        for ($i = 0; $i < 9000; $i++) {
            History::query()->create([
                'kw' => $kW,
                'hour' => $currentTime->hour,
                'day' => $currentTime->day,
                'com_port' => 'COM3',
                'slave_id' => 1,
                'datetime' => $currentTime,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]);
            History::query()->create([
                'kw' => $kW,
                'hour' => $currentTime->hour,
                'day' => $currentTime->day,
                'com_port' => 'COM4',
                'slave_id' => 2,
                'datetime' => $currentTime,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]);
            History::query()->create([
                'kw' => $kW,
                'hour' => $currentTime->hour,
                'day' => $currentTime->day,
                'com_port' => 'COM5',
                'slave_id' => 3,
                'datetime' => $currentTime,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]);
            History::query()->create([
                'kw' => $kW,
                'hour' => $currentTime->hour,
                'day' => $currentTime->day,
                'com_port' => 'COM6',
                'slave_id' => 4,
                'datetime' => $currentTime,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]);

            $kW += rand(3, 10);

            if ($currentTime->minute < 45) {
                $currentTime->addMinutes(15);
                continue;
            }

            if ($currentTime->minute == 45) {
                $currentTime->addMinutes(14);
                continue;
            }

            if ($currentTime->minute == 59) {
                $currentTime->addMinutes(1);
                continue;
            }
        }
    }
}
