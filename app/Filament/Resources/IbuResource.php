<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IbuResource\Pages;
use App\Filament\Resources\IbuResource\RelationManagers;
use App\Models\Ibu;
use App\Models\Kelas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class IbuResource extends Resource
{
    protected static ?string $model = Ibu::class;

    protected static ?string $navigationGroup = 'Data Siswa';

    protected static ?string $modelLabel = 'Ibu';

    protected static ?string $pluralModelLabel = 'Ibu';

    protected static ?string $navigationIcon = 'heroicon-c-user-plus';

    public static function query(Builder $query): Builder
    {
        return $query->with('siswa');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Fieldset::make('Data Siswa')
                                    ->schema([
                                        Forms\Components\Placeholder::make('nisn')
                                            ->label('NISN')
                                            ->content(fn($record) => $record->siswa?->nisn ?? 'Tidak ada data'),
                                        Forms\Components\Placeholder::make('nama_siswa')
                                            ->label('Nama Siswa')
                                            ->content(fn($record) => $record->siswa?->nama ?? 'Tidak ada data'),
                                    ]),
                                Forms\Components\TextInput::make('nik')
                                    ->label('NIK')
                                    ->required()
                                    ->length(16) // Minimal 16 karakter
                                    ->numeric() // Hanya angka (0-9)
                                    ->rules(['regex:/^\d{16}$/']) // Pastikan tepat 16 digit angka
                                    ->placeholder('NIK Ibu'),
                                Forms\Components\TextInput::make('nama')
                                    ->label('Nama Ibu')
                                    ->required()
                                    ->placeholder('Nama Ibu'),
                            ])
                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Select::make('tahun_lahir')
                                    ->label('Tahun Lahir')
                                    ->options(
                                        array_combine(range(1900, date('Y')), range(1900, date('Y'))) // Tahun 2025 - 1950
                                    )
                                    ->required(),
                                Forms\Components\Fieldset::make('Detail Ibu')
                                    ->schema([
                                        Forms\Components\Select::make('pendidikan_id')
                                            ->label('Pendidikan')
                                            ->relationship('pendidikan', 'pendidikan', function ($query) {
                                                return $query->orderBy('id_pendidikan', 'asc');
                                            })
                                            ->required(),
                                        Forms\Components\Select::make('pekerjaan_id')
                                            ->label('Pekerjaan')
                                            ->relationship('pekerjaan', 'pekerjaan', function ($query) {
                                                return $query->orderBy('id_pekerjaan', 'asc');
                                            })
                                            ->required(),
                                        Forms\Components\Select::make('penghasilan_id')
                                            ->label('Penghasilan')
                                            ->relationship('penghasilan', 'penghasilan', function ($query) {
                                                return $query->orderBy('id_penghasilan', 'asc');
                                            })
                                            ->required(),
                                    ]),
                            ])
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $is_Siswa = Auth::user()->hasRole('Siswa');

                if ($is_Siswa) {
                    $query->where('nisn', Auth::user()->username);
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('nisn')
                    ->label('NISN'),
                Tables\Columns\TextColumn::make('siswa.nama')
                    ->label('Nama Siswa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Ibu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK'),
                Tables\Columns\TextColumn::make('tahun_lahir')
                    ->sortable()
                    ->searchable()
                    ->label('Tahun Lahir'),
                Tables\Columns\TextColumn::make('pendidikan.pendidikan')
                    ->label('Pendidikan'),
                Tables\Columns\TextColumn::make('pekerjaan.pekerjaan')
                    ->label('Pekerjaan'),
                Tables\Columns\TextColumn::make('penghasilan.penghasilan')
                    ->label('Penghasilan'),
            ])
            ->filters(
                self::getTableFilters()
            )
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function getTableFilters(): array
    {
        $filters = [];
        if (!Auth::user()->hasRole('Siswa')) {
            $filters[] = SelectFilter::make('kelas')
                ->label('Kelas')
                ->options(fn() => Kelas::exists()? Kelas::pluck ('kelas', 'id') : [])
                // ->relationship('kelas', 'kelas')
                ->preload()
                ->searchable()
                ->query(function (Builder $query, $data) {
                    if (!empty($data['value'])) {
                        $query->whereHas('siswa.kelas', function ($q) use($data) {
                            $q->where('id', $data['value']);
                        });
                    }
                });
        }

        if (Auth::user()->hasRole(['Admin', 'super_admin'])) {
            $filters[] = TrashedFilter::make();
        }

        return $filters;
    }

    // protected static function getTableBulkActions(): array
    // {
    //     $bulkActions = [
    //         Tables\Actions\BulkActionGroup::make([
    //             Tables\Actions\DeleteBulkAction::make(),
    //         ]),
    //     ];

    //     if (Auth::user()->hasRole(['Admin', 'super_admin'])) {
    //         $bulkActions[0]->actions([
    //             Tables\Actions\DeleteBulkAction::make(),
    //             Tables\Actions\ForceDeleteBulkAction::make(),
    //             Tables\Actions\RestoreBulkAction::make(),
    //         ]);
    //     }

    //     return $bulkActions;
    // }

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
