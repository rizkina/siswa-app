<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GoogleDriveSetupResource\Pages;
use App\Filament\Resources\GoogleDriveSetupResource\RelationManagers;
use App\Models\GoogleDriveSetup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\ToggleButtons;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GoogleDriveSetupResource extends Resource
{
    protected static ?string $model = GoogleDriveSetup::class;

    protected static ?string $navigationLabel = 'Google Drive Setup';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $navigationIcon = 'heroicon-s-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('client_id')
                                    ->label('Client ID')
                                    ->required()
                                    ->placeholder('Masukkan Client ID'),
                                Forms\Components\TextInput::make('client_secret')
                                    ->label('Client Secret')
                                    ->required()
                                    ->placeholder('Masukkan Client Secret'),
                                Forms\Components\TextInput::make('refresh_token')
                                    ->label('Refresh Token')
                                    ->required()
                                    ->placeholder('Masukkan Refresh Token'),
                                Forms\Components\TextInput::make('folder_id')
                                    ->label('Folder ID')
                                    ->nullable()
                                    ->placeholder('Masukkan Folder ID'),
                                Forms\Components\ToggleButtons::make('is_active')
                                    ->label('Aktif')
                                    ->inline()
                                    ->boolean()
                                    ->default('false')
                                    ->helperText('Klik untuk mengaktifkan pengaturan Google Drive'),
                            ]),
                    ])
                    ->columns([
                        'sm' => 2,
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client_id')
                    ->label('Client ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client_secret')
                    ->label('Client Secret')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('refresh_token')
                    ->label('Refresh Token')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_aktif')
                    ->label('Aktif')
                    ->inline(false)
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-x-circle')
                    ->onColor('success')
                    ->offColor('danger'),
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
}
