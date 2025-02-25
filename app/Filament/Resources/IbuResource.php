<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IbuResource\Pages;
use App\Filament\Resources\IbuResource\RelationManagers;
use App\Models\Ibu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IbuResource extends Resource
{
    protected static ?string $model = Ibu::class;

    protected static ?string $modelLabel = 'Ibu';

    protected static ?string $pluralModelLabel = 'Ibu';

    protected static ?string $navigationIcon = 'heroicon-c-user-plus';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListIbus::route('/'),
            'create' => Pages\CreateIbu::route('/create'),
            'edit' => Pages\EditIbu::route('/{record}/edit'),
        ];
    }
}
