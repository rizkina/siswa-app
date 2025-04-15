<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GoogleDriveSetupResource\Pages;
use App\Filament\Resources\GoogleDriveSetupResource\RelationManagers;
use App\Models\GoogleDriveSetup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class GoogleDriveSetupResource extends Resource
{
    protected static ?string $model = GoogleDriveSetup::class;

    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?string $navigationLabel = 'Google Drive';
    protected static ?string $label = 'Google Drive Setup';

    protected static ?string $pluralLabel = 'Google Drive Setups';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                    Forms\Components\TextInput::make('client_id')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('client_secret')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('redirect_uri')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('folder_id')
                        ->maxLength(255),
                    Forms\Components\ToggleButtons::make('is_active')
                        ->label('Aktif')
                        ->inline()
                        ->boolean()
                        ->grouped()
                        ->default('false')
                        ->helperText('Aktif/Non Aktifkan pengaturan Google Drive')
                        ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('client_secret')
                    ->searchable(),
                Tables\Columns\TextColumn::make('redirect_uri')
                    ->searchable(),
                Tables\Columns\TextColumn::make('folder_id')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
            'index' => Pages\ListGoogleDriveSetups::route('/'),
            'create' => Pages\CreateGoogleDriveSetup::route('/create'),
            'edit' => Pages\EditGoogleDriveSetup::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->hasRole('super_admin');
    }
}
