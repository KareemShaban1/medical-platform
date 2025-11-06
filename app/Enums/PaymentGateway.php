<?php

namespace App\Enums;

enum PaymentGateway: string
{
    case COD = 'cod';
    case PAYMOB = 'paymob';

    public function label(): string
    {
        return match($this) {
            self::COD => 'Cash on Delivery',
            self::PAYMOB => 'Paymob',
        };
    }

    public function displayName(): string
    {
        return $this->label();
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}


