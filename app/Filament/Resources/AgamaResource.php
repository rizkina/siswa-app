<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgamaResource\Pages;
use App\Filament\Resources\AgamaResource\RelationManagers;
use App\Models\Agama;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AgamaResource extends Resource
{
    protected static ?string $model = Agama::class;
    protected static ?string $navigationGroup = 'Data Referensi';

    protected static ?string $modelLabel = 'Agama';

    protected static ?string $pluralModelLabel = 'Agama';

    protected static ?string $navigationIcon = 'heroicon-m-building-library';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id_agama')
                    ->label('Kode')
                    ->required()
                    ->numeric()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('agama')
                    ->label('Agama')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_agama')
                    ->label('Kode')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('agama')
                    ->label('Agama')
                    ->sortable()
                    ->searchable(),
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
            'index' => Pages\ListAgamas::route('/'),
            'create' => Pages\CreateAgama::route('/create'),
            'edit' => Pages\EditAgama::route('/{record}/edit'),
        ];
    }
}
