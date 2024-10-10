<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AddressRelationManager extends RelationManager
{
    protected static string $relationship = 'address';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('first_name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                TextInput::make('last_name')
                    ->label('Apellido')
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('Telefono')
                    ->required()
                    ->tel()
                    ->maxLength(20),

                TextInput::make('city')
                    ->label('Ciudad')
                    ->required()
                    ->maxLength(255),

                TextInput::make('state')
                    ->label('Estado')
                    ->required()
                    ->maxLength(255),

                TextInput::make('zip_code')
                    ->label('Código postal')
                    ->required()
                    ->numeric()
                    ->maxLength(10),

                Textarea::make('street_address')
                    ->label('Dirección de la calle')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('street_address')
            ->columns([

                TextColumn::make('fullname')
                    ->label('Nombre'),

                TextColumn::make('phone')
                    ->label('Telefono'),

                TextColumn::make('city')
                    ->label('Ciudad'),

                TextColumn::make('state')
                    ->label('Estado'),

                TextColumn::make('zip_code')
                    ->label('Código postal'),

                TextColumn::make('street_address')
                    ->label('Dirección de la calle'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
