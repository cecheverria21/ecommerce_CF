<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\AddressRelationManager;
use App\Models\Order;
use App\Models\Product;
//use Faker\Core\Number;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Number;

class OrderResource extends Resource
{
    protected static ?string $modelLabel = 'Pedido';

    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Información del pedido')->schema([
                        Select::make('user_id')
                            ->label('Cliente')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('payment_method')
                            ->label('Método de pago')
                            ->options([
                                'stripe' => 'Stripe',
                                'cod' => 'Pago contra reembolso'
                            ])
                            ->required(),

                        Select::make('payment_status')
                            ->label('Estado del pago')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'pagado' => 'Pagado',
                                'fallido' => 'Fallido'
                            ])
                            ->default('pendiente')
                            ->required(),

                        ToggleButtons::make('status')
                            ->label('Estado')
                            ->inline()
                            ->default('nuevo')
                            ->required()
                            ->options([
                                'nuevo' => 'Nuevo',
                                'procesando' => 'Procesando',
                                'enviado' => 'Enviado',
                                'entregado' => 'Entregado',
                                'cancelado' => 'Cancelado'
                            ])
                            ->colors([
                                'nuevo' => 'info',
                                'procesando' => 'warning',
                                'enviado' => 'success',
                                'entregado' => 'success',
                                'cancelado' => 'danger'
                            ])
                            ->icons([
                                'nuevo' => 'heroicon-m-sparkles',
                                'procesando' => 'heroicon-m-arrow-path',
                                'enviado' => 'heroicon-m-truck',
                                'entregado' => 'heroicon-m-check-badge',
                                'cancelado' => 'heroicon-m-x-circle'                                
                            ]),

                        Select::make('currency')
                            ->label('Moneda')
                            ->options([
                                'cld' => 'CLD',
                                'usd' => 'USD',
                                'eur' => 'EUR'
                            ])
                            ->default('CLD')
                            ->required(),

                        Select::make('shipping_method')
                            ->label('Método de envío')
                            ->options([
                                'fedex' => 'FedEx',
                                'ups' => 'UPS',
                                'dhl' => 'DHL',
                                'usps' => 'USPS'
                            ])
                            ->required(),

                        Textarea::make('notes')
                            ->label('Notas')
                            ->columnSpanFull()
                    ])->columns(2),

                    Section::make('Productos de la orden')->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->label('Producto')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->columnSpan(4)
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, Set $set) => $set('unit_amount', Product::find($state)?->price ?? 0))
                                    ->afterStateUpdated(fn ($state, Set $set) => $set('total_amount', Product::find($state)?->price ?? 0)),

                                TextInput::make('quantity')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(1)
                                    ->columnSpan(2)
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, Set $set, Get $get) => $set('total_amount', $state * $get('unit_amount'))),

                                TextInput::make('unit_amount')
                                    ->label('Cantidad unitaria')
                                    ->numeric()
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(3),

                                TextInput::make('total_amount')
                                    ->label('Importe total')
                                    ->numeric()
                                    ->required()
                                    ->dehydrated()
                                    ->columnSpan(3),


                            ])->columns(12),

                            Placeholder::make('grand_total_placeholder')
                                ->label('Gran total')
                                ->content(function (Get $get, Set $set){
                                    $total = 0;
                                    if(!$repeaters = $get('items')){
                                        return $total;
                                    }

                                    foreach($repeaters as $key => $repeater){
                                        $total += $get("items.{$key}.total_amount");
                                    }
                                    $set('grand_total', $total);  //este es para que la columna oculta 'grand_total' pueda tener el total general
                                    return Number::currency($total);
                                }),

                            Hidden::make('grand_total')
                                ->default(0)
                    ])

                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('grand_total')
                    ->label('Gran Total')
                    ->numeric()
                    ->sortable(),
                    //->money('INR'),

                TextColumn::make('payment_method')
                    ->label('Método de pago')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('payment_status')
                    ->label('Estado de pago')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('currency')
                    ->label('Moneda')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('shipping_method')
                    ->label('Método de envío')
                    ->sortable()
                    ->searchable(),

                SelectColumn::make('status')
                    ->label('Estado')
                    ->options([
                        'nuevo' => 'Nuevo',
                        'procesando' => 'Procesando',
                        'enviado' => 'Enviado',
                        'entregado' => 'Entregado',
                        'cancelado' => 'Cancelado'                        
                    ])
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado el')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault:true),

                TextColumn::make('updated_at')
                    ->label('Actualizado el')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault:true)

            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AddressRelationManager::class
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count(); //entrega en el borde del menú la cantidad de pedidos
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? 'success': 'danger';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
