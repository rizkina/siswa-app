<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiswaResource\Pages;
use App\Filament\Resources\SiswaResource\RelationManagers;
use App\Models\Siswa;
use App\Models\Agama;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class SiswaResource extends Resource
{
    use HasRoles;
    protected static ?string $model = Siswa::class;

    protected static ?string $navigationGroup = 'Data Siswa';

    protected static ?string $modelLabel = 'Siswa';

    protected static ?string $pluralModelLabel = 'Siswa';

    protected static ?string $navigationIcon = 'heroicon-s-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('nisn')
                                    ->label('NISN')
                                    ->required()
                                    // ->readOnly()
                                    ->unique(ignoreRecord: true)
                                    ->length(10) // Minimal 10 karakter
                                    ->numeric() // Hanya angka (0-9)
                                    ->rules(['regex:/^\d{10}$/']) // Pastikan tepat 10 digit angka
                                    ->placeholder('Masukkan NISN'),
                                Forms\Components\TextInput::make('nipd')
                                    ->label('NIPD')
                                    ->placeholder('Masukkan NIPD/NIS')
                                    // ->required()
                                    ->unique(ignoreRecord: true),
                                Forms\Components\TextInput::make('nik')
                                    ->label('NIK')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->length(16) // Minimal 10 karakter
                                    ->numeric() // Hanya angka (0-9)
                                    ->rules(['regex:/^\d{16}$/']) // Pastikan tepat 10 digit angka
                                    ->placeholder('NIK Siswa'),
                                Forms\Components\TextInput::make('nama')
                                    ->label('Nama')
                                    ->placeholder('Nama Siswa')
                                    ->required(),
                                Forms\Components\Radio::make('jns_kelamin')
                                    ->label('Jenis Kelamin')
                                    ->required()
                                    ->options([
                                        'L' => 'Laki-laki',
                                        'P' => 'Perempuan',
                                    ])
                                    ->unique(ignoreRecord: true),

                            ])
                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Fieldset::make('Data Kelahiran')
                                    ->schema([
                                        Forms\Components\TextInput::make('tempat_lahir')
                                            ->label('Tempat Lahir')
                                            ->placeholder('Tempat Lahir')
                                            ->required(),
                                        Forms\Components\DatePicker::make('tanggal_lahir')
                                            ->label('Tanggal Lahir')
                                            // ->native(false)
                                            ->required(),
                                    ]),
                                Forms\Components\Select::make('agama_id')
                                    ->label('Agama')
                                    ->relationship('agama', 'agama', function ($query) {
                                        return $query->orderBy('id_agama', 'asc');
                                    })
                                    ->required(),
                                Forms\Components\TextInput::make('alamat')
                                    ->placeholder('Masukkan Alamat')
                                    ->label('Alamat'),
                                Forms\Components\FileUpload::make('foto')
                                    ->label('Foto'),
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

                $query->with([
                    'agama',
                    'kelas',
                ]);
            })
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('foto')
                    ->circular(),
                Tables\Columns\TextColumn::make('nisn')
                    ->label('NISN')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nipd')
                    ->label('NIPD')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jns_kelamin')
                    ->label('L/P')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tempat_lahir')
                    ->label('Tempat Lahir')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_lahir')
                    ->label('Tanggal Lahir')
                    ->date()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('agama.agama')
                    ->label('Agama')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->label('Alamat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kelas.kelas')
                    ->label('Kelas')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kelas')
                    ->label('Kelas')
                    ->relationship('kelas', 'kelas') // Hubungkan ke relasi 'kelas'
                    ->preload() // Load data langsung
                    ->searchable(), // Memungkinkan pencarian dalam daftar kelas
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
            'index' => Pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit' => Pages\EditSiswa::route('/{record}/edit'),
        ];
    }
}
