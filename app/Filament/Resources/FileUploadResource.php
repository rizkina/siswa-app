<?php

namespace App\Filament\Resources;

use App\Models\Siswa;
use App\Models\FileKategori;
use App\Filament\Resources\FileUploadResource\Pages;
use App\Filament\Resources\FileUploadResource\RelationManagers;
use App\Models\FileUpload;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class FileUploadResource extends Resource
{
    protected static ?string $model = FileUpload::class;

    protected static ?string $navigationGroup = 'Data Siswa';
    protected static ?string $label = 'File Upload';
    protected static ?string $pluralLabel = 'File Uploads';
    protected static ?string $navigationLabel = 'File Upload';

    protected static ?string $navigationIcon = 'heroicon-s-document';

    
    public static function form(Form $form): Form
    {
        $user = Auth::user();
        $isSiswa = $user->hasRole('Siswa');
        $siswa = $isSiswa 
            ? \App\Models\Siswa::where('nisn', $user->username)->first()
            : null;

        $file_kategori = FileKategori::all();

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
                                            ->content(function ($record) use ($siswa) {
                                                if ($record && $record->siswa) {
                                                    return $record->siswa->nisn;
                                                }
                                                return $siswa?->nisn ?? 'Tidak ada data';
                                            })
                                            ->disabled(),

                                        Forms\Components\Placeholder::make('nama_siswa')
                                            ->label('Nama Siswa')
                                            ->content(function ($record) use ($siswa) {
                                                if ($record && $record->siswa) {
                                                    return $record->siswa->nama;
                                                }
                                                return $siswa?->nama ?? 'Tidak ada data';
                                                })
                                            ->disabled(),
                                    ]),

                                Forms\Components\Select::make('file_kategori_id')
                                    ->label('Kategori File')
                                    ->required()
                                    ->options($file_kategori->pluck('nama', 'id'))
                                    ->searchable(),

                                Forms\Components\TextInput::make('nama_file')
                                    ->label('Nama File')
                                    ->required()
                                    ->default(function () use ($siswa) {
                                        $kategori = FileKategori::first(); // atau kategori default jika ada
                                        $kategoriNama = Str::slug($kategori?->nama ?? 'File');
                                        $nisn = $siswa?->nisn ?? '0000000000';
                                        $nama = Str::slug($siswa?->nama ?? 'noname');
                                        return "{$kategoriNama}_{$nisn}_{$nama}";
                                    })
                                    ->placeholder('Nama file otomatis terisi')
                                    ->disabled(), // kalau kamu tidak ingin user mengubahnya


                                Forms\Components\FileUpload::make('file')
                                    ->label('Unggah File')
                                    ->disk('local') // sementara, akan di-handle simpan ke Google Drive nanti
                                    ->visibility('private')
                                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/bmp', 'image/jpg'])
                                    ->required(),
                            ])
                    ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (Auth::user()->hasRole('Siswa')) {
                    $query->where('nisn', Auth::user()->username); // atau Auth::user()->nisn
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('siswa.nama')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('kategori.nama')
                    ->label('Kategori File')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_file')
                    ->label('Nama File')
                    ->wrap(),

                Tables\Columns\TextColumn::make('file')
                    ->label('Link File')
                    ->formatStateUsing(fn (?string $state): string => $state ? route('file.download', ['file' => $state]) : '-')
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-down-tray')
                    ->tooltip('Klik untuk unduh'),
                

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Upload')
                    ->dateTime('d M Y H:i'),
            ])
            ->filters([
                // Tambah filter kategori / tanggal kalau ingin
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListFileUploads::route('/'),
            'create' => Pages\CreateFileUpload::route('/create'),
            'edit' => Pages\EditFileUpload::route('/{record}/edit'),
        ];
    }
}
