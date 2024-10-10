<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        //Verifico si la tabla Order tiene registros
        $Status = Order::whereNull('status');

        
        if (isset($Status)) {
            //vacio
            return [
                Stat::make('Nuevos pedidos', 0),
                Stat::make('Procesamiento de pedidos', 0),
                Stat::make('Pedido enviado', 0),
                Stat::make('Precio medio', 0)
            ];
        }
        else {
            //con registros
            return [
                Stat::make('Nuevos pedidos', Order::query()->where('status', 'nuevo')->count()),
                Stat::make('Procesamiento de pedidos', Order::query()->where('status', 'procesando')->count()),
                Stat::make('Pedido enviado', Order::query()->where('status', 'enviado')->count()),
                Stat::make('Precio medio', Number::currency(Order::query()->avg('grand_total')))
            ];
        }

    }
}
