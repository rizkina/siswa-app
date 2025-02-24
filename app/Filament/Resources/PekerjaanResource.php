<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PekerjaanResource\Pages;
use App\Filament\Resources\PekerjaanResource\RelationManagers;
use App\Models\Pekerjaan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PekerjaanResource extends Resource
{
    protected static ?string $model = Pekerjaan::class;
    
    protected static ?string $navigationGroup = 'Data Referensi';

    protected static ?string $modelLabel = 'Pekerjaan';

    protected static ?string $pluralModelLabel = 'Pekerjaan';

    protected static ?string $navigationIcon = 'heroicon-m-hand-raised';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->label('Kode')
                    ->required()
                    ->numeric()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('pekerjaan')
                    ->label('Pekerjaan')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Kode')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('pekerjaan')
                    ->label('Pekerjaan')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('deleted_at')
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
            'index' => Pages\ListPekerjaans::route('/'),
            'create' => Pages\CreatePekerjaan::route('/create'),
            'edit' => Pages\EditPekerjaan::route('/{record}/edit'),
        ];
    }
}
