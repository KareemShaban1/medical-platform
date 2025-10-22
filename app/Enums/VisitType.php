<?php

namespace App\Enums;

enum VisitType: int
{
    case INITIAL = 0;
    case FOLLOW_UP = 1;
    case CONSULTATION = 2;

    public function label(): string
    {
        return match($this) {
            self::INITIAL => 'Initial Visit',
            self::FOLLOW_UP => 'Follow-up',
            self::CONSULTATION => 'Consultation',
        };
    }

    public static function options(): array
    {
        return [
            self::INITIAL->value => self::INITIAL->label(),
            self::FOLLOW_UP->value => self::FOLLOW_UP->label(),
            self::CONSULTATION->value => self::CONSULTATION->label(),
        ];
    }

    public static function fromValue(int $value): ?self
    {
        return match($value) {
            0 => self::INITIAL,
            1 => self::FOLLOW_UP,
            2 => self::CONSULTATION,
            default => null,
        };
    }
}

