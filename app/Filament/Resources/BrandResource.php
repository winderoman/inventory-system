<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\Pages;
use App\Filament\Resources\BrandResource\RelationManagers;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\ColorColumn;
use Filament\Support\Enums\Alignment;
class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Shop';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Group::make()
                    ->schema([
                        Section::make()
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->live(debounce: 500)
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                                ->maxLength(255),
                            Forms\Components\TextInput::make('slug')
                                ->required()
                                ->disabled()
                                ->unique(ignoreRecord: true)
                                ->dehydrated()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('url')
                                ->label('Website URL')
                                ->unique(ignoreRecord: true)
                                ->columnSpan('full')
                                ->maxLength(255),
                            Forms\Components\MarkdownEditor::make('description')
                                ->columnSpan('full')
                                ->maxLength(255),
                        ])->columns(2),
                    ])->columnSpan(['lg' =>  2]),


                Forms\Components\Group::make()
                ->schema([
                    Section::make()
                    ->schema([
                        Forms\Components\ColorPicker::make('primary_hex')
                        ->label('Primary Color'),
                        Forms\Components\Toggle::make('is_visible')
                            ->default(true)
                            ->required(),
                    ])->columnSpan(['lg' => 1]),
                ])    
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('url')
                    ->searchable(),
                ColorColumn::make('primary_hex')
                    ->alignment(Alignment::Center)
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_visible')
                    ->alignment(Alignment::Center)
                    ->boolean(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
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
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
