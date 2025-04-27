<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GoogleDriveSetupResource\Pages;
use App\Filament\Resources\GoogleDriveSetupResource\RelationManagers;
use App\Models\GoogleDriveSetup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;

class GoogleDriveSetupResource extends Resource
{
    protected static ?string $model = GoogleDriveSetup::class;

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Google Drive Setup';

    protected static ?string $pluralLabel = 'Google Drive Setups';

    protected static ?string $navigationIcon = 'heroicon-c-rectangle-stack';

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
                                    ->maxLength(255)
                                    ->placeholder('Masukkan Client ID'),
                                Forms\Components\TextInput::make('client_secret')
                                    ->label('Client Secret')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Masukkan Client Secret'),
                                Forms\Components\TextInput::make('redirect_uri')
                                    ->label('Redirect URI')
                                    ->maxLength(255),
                                Forms\Components\TextArea::make('refresh_token')
                                    ->label('Refresh Token')
                                    ->rows(2),
                                Forms\Components\TextInput::make('access_token')
                                    ->label('Access Token')
                                    ->disabled(),
                            ]),
                        ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('token_type')
                                    ->label('Token Type')
                                    ->maxLength(255)
                                    ->disabled(),
                                Forms\Components\TextInput::make('expires_in')
                                    ->label('Expires In')
                                    ->numeric()
                                    ->disabled(),
                                Forms\Components\TextInput::make('folder_id')
                                    ->label('Folder ID (Google Drive)')
                                    ->maxLength(255),
                                Forms\Components\ToggleButtons::make('is_active')
                                    ->label('Aktif')
                                    ->inline()
                                    ->boolean()
                                    ->grouped()
                                    ->default(false)
                                    ->helperText('Aktifkan Google Drive Setup')
                                    ->required(),
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client_id')
                    ->label('Client ID')
                    ->limit(20),
                Tables\Columns\TextColumn::make('folder_id')
                    ->label('Folder ID'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->color('success'),
                Tables\Columns\IconColumn::make('status_koneksi')
                    ->label('Status Koneksi')
                    ->getStateUsing(function (GoogleDriveSetup $record) {
                        try {
                            $client = new \Google\Client();
                            $client->setClientId($record->client_id);
                            $client->setClientSecret($record->client_secret);
                            $client->setAccessType('offline');
                
                            if ($record->refresh_token) {
                                $client->refreshToken($record->refresh_token);
                            } else {
                                return false;
                            }
                
                            $service = new \Google\Service\Drive($client);
                            $about = $service->about->get(["fields" => "user"]);
                            return true;
                        } catch (\Exception $e) {
                            return false;
                        }
                    })
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->color(fn (bool $state) => $state ? 'success' : 'danger'),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('authorize')
                    ->label('Autentikasi')
                    ->icon('heroicon-o-lock-closed')
                    ->url(fn () => route('google.drive.auth'))
                    ->openUrlInNewTab()
                    ->visible(fn (GoogleDriveSetup $record) => $record->is_active),

                Tables\Actions\Action::make('test_connection')
                    ->label('Tes Koneksi')
                    ->icon('heroicon-o-bolt')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(fn (GoogleDriveSetup $record) => self::testConnection($record)),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function testConnection(GoogleDriveSetup $record): void
    {
        try {
            $client = new \Google\Client();
            $client->setClientId($record->client_id);
            $client->setClientSecret($record->client_secret);
            $client->setAccessType('offline');
            $client->setPrompt('consent');
    
            if ($record->refresh_token) {
                $client->refreshToken($record->refresh_token);
            }
    
            $service = new \Google\Service\Drive($client);
            $about = $service->about->get(["fields" => "user"]);
    
            Notification::make()
                ->title('Berhasil terkoneksi dengan Google Drive')
                ->body('Akun: ' . $about->getUser()->getDisplayName())
                ->success()
                ->send();
    
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal koneksi ke Google Drive')
                ->body('Pesan error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
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
