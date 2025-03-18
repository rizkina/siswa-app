<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TahunPelajaranResource\Pages;
use App\Filament\Resources\TahunPelajaranResource\RelationManagers;
use App\Models\TahunPelajaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\ToggleButtons;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TahunPelajaranResource extends Resource
{
    protected static ?string $model = TahunPelajaran::class;

    protected static ?string $navigationGroup = 'Data Periodik';

    protected static ?string $modelLabel = 'Tahun Pelajaran';

    protected static ?string $pluralModelLabel = 'Tahun Pelajaran';

    protected static ?string $navigationIcon = 'heroicon-c-clock';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('tahun_pelajaran')
                            ->label('Kode Tahun Pelajaran')
                            ->required()
                            ->placeholder('Contoh: 20251'),
                        Forms\Components\Select::make('tahun')
                            ->label('Tahun')
                            ->options(
                                array_combine(range(1950, date('Y')), range(1950, date('Y'))) // Tahun 2025 - 1950
                            ),
                        Forms\Components\Select::make('semester')
                            ->label('Semester')
                            ->required()
                            ->options([
                                '1' => 'Ganjil',
                                '2' => 'Genap',
                            ]),
                        Forms\Components\ToggleButtons::make('aktif')
                            ->label('Status Aktif')
                            // ->options([
                            //     '0' => 'Tidak Aktif',
                            //     '1' => 'Aktif',
                            // ])
                            // ->colors([
                            //     'danger' => 'Tidak Aktif',
                            //     'success' => 'Aktif',
                            // ])
                            ->boolean()
                            ->default('0')
                            ->inline()
                            ->required(),
                        Forms\Components\Datepicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->required(),
                        Forms\Components\Datepicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai')
                            ->required(),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan'),

                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tahun_pelajaran')->label('Tahun Pelajaran'),
                Tables\Columns\TextColumn::make('tahun')->label('Tahun'),
                Tables\Columns\TextColumn::make('semester')
                    ->label('Semester')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Ganjil' => 'primary',
                        'Genap' => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\ToggleColumn::make('aktif')
                    ->label('Status Aktif')
                    ->afterStateUpdated(fn(bool $state, $record) => $record->save())
                    ->inline(false)
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-x-circle')
                    ->onColor('success')
                    ->offColor('danger'),
                // Tables\Columns\IconColumn::make('aktif')
                //     ->label('Status Aktif')
                //     ->icon(fn(string $state): string => match ($state) {
                //         '0' => 'heroicon-s-x-circle',
                //         '1' => 'heroicon-s-check-circle',
                //     })
                //     ->color(fn(string $state): string => match ($state) {
                //         '0' => 'danger',
                //         '1' => 'success',
                //     }),

                Tables\Columns\TextColumn::make('tanggal_mulai')->label('Tanggal Mulai'),
                Tables\Columns\TextColumn::make('tanggal_selesai')->label('Tanggal Selesai'),
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
            'index' => Pages\ListTahunPelajarans::route('/'),
            'create' => Pages\CreateTahunPelajaran::route('/create'),
            'edit' => Pages\EditTahunPelajaran::route('/{record}/edit'),
        ];
    }
}
