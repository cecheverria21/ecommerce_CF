<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;

use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $modelLabel = 'Producto';

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Información del producto')->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur:true)
                            ->afterStateUpdated(function(string $operation, $state, Set $set){
                                if($operation !== 'create'){
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated() //esto es para evitar el error, como el control esta disabled y es obligatorio gruardar
                            ->unique(Product::class, 'slug', ignoreRecord:true),

                        MarkdownEditor::make('description')
                            ->label('Descripción')
                            ->columnSpanFull()
                            ->fileAttachmentsDirectory('products')

                    ])->columns(2),

                    Section::make('Imágenes')->schema([
                        FileUpload::make('images')
                        ->label('Imágenes')
                        ->multiple()
                        ->directory('products')
                        ->maxFiles(5)
                        ->reorderable()
                    ])

                ])->columnSpan(2),

                Group::make()->schema([
                    Section::make('Precio')->schema([
                        TextInput::make('price')
                        ->label('Precio')
                        ->numeric()
                        ->required()
                        ->prefix('$')
                    ]),

                    Section::make('Asociaciones')->schema([
                        Select::make('category_id')
                            ->label('Categoría')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship('category', 'name'),

                        Select::make('brand_id')
                            ->label('Marca')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship('brand', 'name'),
                    ]),

                    Section::make('Estado')->schema([
                        Toggle::make('in_stock')
                            ->label('en Stock')
                            ->required()
                            ->default(true),
                        
                        Toggle::make('is_active')
                            ->label('¿está activo?')
                            ->required()
                            ->default(true),

                        Toggle::make('is_featured')
                            ->label('¿es destacado?')
                            ->required(),

                        Toggle::make('on_sale')
                            ->label('¿En venta?')
                            ->required()                            
                          
                    ])

                ])->columnSpan(1)

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),

                TextColumn::make('category.name')
                    ->label('Categoría')
                    ->sortable(),

                TextColumn::make('brand.name')
                    ->label('Marca')
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Precio')
                    ->sortable(),

                IconColumn::make('is_featured')
                    ->label('¿es destacado?')
                    ->boolean(),

                IconColumn::make('on_sale')
                    ->label('¿En venta?')
                    ->boolean(),

                IconColumn::make('in_stock')
                    ->label('en Stock')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('¿está activo?')
                    ->boolean(),

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
                SelectFilter::make('category')
                    ->relationship('category', 'name'),

                SelectFilter::make('brand')
                    ->relationship('category', 'name'),

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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
