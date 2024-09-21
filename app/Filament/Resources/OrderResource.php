<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Shop';
    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::processing()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = (int) static::getNavigationBadge();
        return $count > 10 ? 'warning' : 'success';
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Order Details')->icon('heroicon-m-shopping-bag')->schema([
                        Forms\Components\TextInput::make('number')
                            ->default('OR-' . random_int(100000,99999999))
                            ->disabled()
                            ->required()
                            ->dehydrated()
                            ->maxLength(255),
                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->preload()
                            ->native(false)
                            ->searchable()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->unique(ignoreRecord:true)
                                    ->prefixIcon('heroicon-m-envelope')
                                    ->email()
                                    ->label('Email Address')
                                    ->required()
                                    ->maxLength(160),
                                Forms\Components\TextInput::make('phone')
                                    ->prefixIcon('heroicon-m-phone')
                                    ->label('Phone Number')
                                    ->unique(ignoreRecord:true)
                                    ->tel()
                                    ->numeric()
                                    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                    ->required()
                                    ->maxLength(50),
                                    
                            ]),
                        Forms\Components\TextInput::make('shipping_price')
                            ->label('Shipping Cost')
                            ->required()
                            ->numeric()
                            ->dehydrated(),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->native(false)
                            ->options(OrderStatus::options())
                            ->default(OrderStatus::Pending),
                        Forms\Components\MarkdownEditor::make('notes')
                            ->columnSpanFull(),
                    ])->columns(2),

                    Forms\Components\Wizard\Step::make('Order Items')->icon('heroicon-m-squares-plus')->schema([
                        Forms\Components\Repeater::make('items')
                            ->label(false)
                            ->addActionLabel('Add item')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Product')
                                    ->options(Product::query()->pluck('name','id'))
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('unit_price',Product::find($state)?->price ?? 0 ) )
                                    ->native(false),
                                Forms\Components\TextInput::make('quantity')
                                    ->default(1)
                                    ->minValue(1)
                                    ->required()
                                    ->live()
                                    ->dehydrated()
                                    ->numeric(),
                                Forms\Components\TextInput::make('unit_price')
                                    ->dehydrated()
                                    ->numeric()
                                    ->disabled()
                                    ->required(),
                                Forms\Components\Placeholder::make('total_price')
                                    ->content(fn (Get $get) => intval($get('quantity')) * intval($get('unit_price')))
                            ])->columns(4)
                    ])

                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipping_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\ViewAction::make(),
                        Tables\Actions\EditAction::make(),
                    ])->dropdown(false), // divider
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}