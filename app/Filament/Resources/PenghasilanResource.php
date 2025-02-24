<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenghasilanResource\Pages;
use App\Filament\Resources\PenghasilanResource\RelationManagers;
use App\Models\Penghasilan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PenghasilanResource extends Resource
{
    protected static ?string $model = Penghasilan::class;

    protected static ?string $navigationGroup = 'Data Referensi';

    protected static ?string $modelLabel = 'Penghasilan';

    protected static ?string $pluralModelLabel = 'Penghasilan';

    protected static ?string $navigationIcon = 'heroicon-m-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->label('Kode')
                    ->required()
                    ->numeric()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('penghasilan')
                    ->label('Penghasilan')
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
                Tables\Columns\TextColumn::make('penghasilan')
                    ->label('Penghasilan')
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
            'index' => Pages\ListPenghasilans::route('/'),
            'create' => Pages\CreatePenghasilan::route('/create'),
            'edit' => Pages\EditPenghasilan::route('/{record}/edit'),
        ];
    }
}
