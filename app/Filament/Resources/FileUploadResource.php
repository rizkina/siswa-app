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
        $file_kategori = FileKategori::all();

        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                $isSiswa
                                    ? Forms\Components\TextInput::make('nisn')
                                        ->label('NISN')
                                        ->default($user->nisn)
                                        ->disabled()
                                        ->dehydrated() // agar tetap tersimpan
                                        ->required()
                                    : Forms\Components\Select::make('nisn')
                                        ->label('Pilih Siswa')
                                        ->required()
                                        ->searchable()
                                        ->options(Siswa::pluck('nama', 'nisn')),

                                Forms\Components\Select::make('file_kategori_id')
                                    ->label('Kategori File')
                                    ->required()
                                    ->options($file_kategori->pluck('nama', 'id'))
                                    ->searchable(),

                                Forms\Components\TextInput::make('nama_file')
                                    ->label('Nama File')
                                    ->required()
                                    ->placeholder('Masukkan Nama File'),

                                Forms\Components\FileUpload::make('file')
                                    ->label('Unggah File')
                                    ->disk('local') // nanti override simpan ke Google Drive
                                    ->visibility('private')
                                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/bmp', 'image/jpg'])
                                    ->required()
                                    ->getUploadedFileNameForStorageUsing(function ($component, $file): string {
                                        $data = $component->getParentComponent()->getState();
                                        $nisn = $data['nisn'] ?? Auth::user()->nisn ?? 'unknown';
                                        $siswa = \App\Models\Siswa::where('nisn', $nisn)->first();
                                        $kategori = FileKategori::find($data['file_kategori_id']);
                                        $nama = Str::slug($siswa?->nama ?? 'noname');
                                        $kategoriNama = Str::slug($kategori?->name ?? 'kategori');

                                        $namaFile = Str::slug($data['nama_file']);
                                        $extension = $file->getClientOriginalExtension();

                                        return "{$nisn}_{$nama}_{$kategoriNama}_{$namaFile}.{$extension}";
                                    }),
                            ])
                        ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nisn')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('kategori.name')
                    ->label('Kategori'),
                Tables\Columns\TextColumn::make('nama_file'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Tanggal Upload'),
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
            'index' => Pages\ListFileUploads::route('/'),
            'create' => Pages\CreateFileUpload::route('/create'),
            'edit' => Pages\EditFileUpload::route('/{record}/edit'),
        ];
    }
}
