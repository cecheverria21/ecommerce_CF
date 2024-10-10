<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
//use Filament\Actions\Action;
use Filament\Tables\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

use Filament\Tables\Columns\TextColumn;

class UltimosPedidos extends BaseWidget
{
    protected  int|string|array $columnSpan = 'full';

    protected static ?int $sort =2;  //para que ocupe la segunda linea del panel principal DashBoard

    public function table(Table $table): Table
    {
        return $table
            ->query(OrderResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('Orden ID')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Nombre')
                    ->searchable(),

                TextColumn::make('grand_total')
                    ->label('Gran Total'),
                    //->money('INR'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state):string => match($state){
                        'nuevo' => 'info',
                        'procesando' => 'warning',
                        'enviado' => 'success',
                        'entregado' => 'success',
                        'cancelado' => 'danger'
                    })
                    ->icon(fn (string $state):string => match($state){
                        'nuevo' => 'heroicon-m-sparkles',
                        'procesando' => 'heroicon-m-arrow-path',
                        'enviado' => 'heroicon-m-truck',
                        'entregado' => 'heroicon-m-check-badge',
                        'cancelado' => 'heroicon-m-x-circle'                          
                    })
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Método de pago')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('payment_status')
                    ->label('Estado del pago')                
                    ->sortable()
                    ->badge()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Fecha de pedido')                
                    ->dateTime()
            ])
            ->actions([
                Action::make('Ver pedido')
                    ->url(fn (Order $record): string => OrderResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-m-eye'),
            ]);
    }
}
