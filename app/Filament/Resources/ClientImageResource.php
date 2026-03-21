<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientImageResource\Pages;
use App\Filament\Resources\ClientImageResource\RelationManagers;
use App\Models\ClientImage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientImageResource extends Resource
{
    protected static ?string $model = ClientImage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Bulk Upload')
                    ->schema([
                        Forms\Components\FileUpload::make('images')
                            ->multiple()
                            ->image()
                            ->required()
                            ->directory('client-images')
                            ->maxSize(2048)
                            ->columnSpanFull()
                            ->helperText('Select multiple images to create individual client records for each.'),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(999),
                    ])
                    ->hiddenOn('edit'),
                    
                Forms\Components\Section::make('Client Image Details')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->required()
                            ->directory('client-images')
                            ->maxSize(2048)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(999),
                    ])
                    ->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records->each->update(['is_active' => true]))
                        ->icon('heroicon-o-check-circle')
                        ->color('success'),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records->each->update(['is_active' => false]))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger'),
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
            'index' => Pages\ListClientImages::route('/'),
            'create' => Pages\CreateClientImage::route('/create'),
            'edit' => Pages\EditClientImage::route('/{record}/edit'),
        ];
    }
}
