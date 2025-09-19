<?php

namespace App\Enums;

enum DepartmentEnum: int
{
    case Factory1 = 1;
    case Tuner1 = 2;
    case Tuner2 = 3;
    case SMPS = 4;
    case AI = 5;
    case SMT = 6;

    public static function getDepartment($id): ?string
    {
        return match ($id) {
            self::Factory1->value => 'Factory 1',
            self::Tuner1->value => 'Tunner1',
            self::Tuner2->value => 'Tunner2',
            self::SMPS->value => 'SMPS',
            self::AI->value => 'AI',
            self::SMT->value => 'SMT',
            default => null,
        };
    }

    public static function listDepartment(): array
    {
        return [
            ['name' => 'Factory 1', 'id' => self::Factory1, 'enabled' => true],
            ['name' => 'Tunner1', 'id' => self::Tuner1, 'enabled' => false],
            ['name' => 'Tunner2', 'id' => self::Tuner2, 'enabled' => false],
            ['name' => 'SMPS', 'id' => self::SMPS, 'enabled' => true],
            ['name' => 'AI', 'id' => self::AI, 'enabled' => true],
            ['name' => 'SMT', 'id' => self::SMT, 'enabled' => true],
        ];
    }
}
