<?php

namespace App\Enums;

enum Availability: string
{
    case InStock = 'in_stock';
    case OutOfStock = 'out_of_stock';
    case OnDemand = 'on_demand';

    public function label(): string
    {
        return match ($this) {
            self::InStock => 'En stock',
            self::OutOfStock => 'Agotado',
            self::OnDemand => 'Bajo pedido',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::InStock => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            self::OutOfStock => 'bg-red-50 text-[#c63427] ring-red-200',
            self::OnDemand => 'bg-amber-50 text-amber-700 ring-amber-200',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases(),
        );
    }
}
