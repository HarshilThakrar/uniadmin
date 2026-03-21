<?php

namespace App\Filament\Resources;

use App\Models\JobOpening;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class JobOpeningResource extends Resource
{
    protected static ?string $model = JobOpening::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'Job Openings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')->required()->maxLength(255),
                Forms\Components\TextInput::make('location')->required()->maxLength(255),
                Forms\Components\TextInput::make('experience')->required()->maxLength(255),
                Forms\Components\RichEditor::make('description')
                    ->required()
                    ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'redo', 'undo'])
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('location')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('experience')->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Active'),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\JobOpeningResource\Pages\ListJobOpenings::route('/'),
            'create' => \App\Filament\Resources\JobOpeningResource\Pages\CreateJobOpening::route('/create'),
            'edit' => \App\Filament\Resources\JobOpeningResource\Pages\EditJobOpening::route('/{record}/edit'),
        ];
    }
}
