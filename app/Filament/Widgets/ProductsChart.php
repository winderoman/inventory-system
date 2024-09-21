<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Product;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ProductsChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        [$products, $months] = $this->getProductsPerMonth();

        return [
            'datasets' => [
                [
                    'label' => 'Total Transactions',
                    'data' => $products,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getProductsPerMonth(): array
    {
        $data = Trend::model(Product::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            $data->map(fn (TrendValue $value) => $value->aggregate),
            $data->map(fn (TrendValue $value) => now()->rawParse($value->date)->format('M')),
        ];
    }
}
