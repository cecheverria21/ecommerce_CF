<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrderStats::class
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('Todos'),
            'nuevo' => Tab::make()->query(fn ($query) => $query->where('status', 'nuevo')),
            'procesando' => Tab::make()->query(fn ($query) => $query->where('status', 'procesando')),
            'enviado' => Tab::make()->query(fn ($query) => $query->where('status', 'enviado')),
            'entregado' => Tab::make()->query(fn ($query) => $query->where('status', 'entregado')),
            'cancelado' => Tab::make()->query(fn ($query) => $query->where('status', 'cancelado')),
        ];
    }

    // protected function getFooterWidgets(): array
    // {
    //     return [
    //         OrderStats::class
    //     ];
    // }
}
