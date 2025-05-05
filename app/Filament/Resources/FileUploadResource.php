<?php

namespace App\Filament\Resources;

use App\Models\Siswa;
use App\Models\FileKategori;
use App\Filament\Resources\FileUploadResource\Pages;
use App\Models\FileUpload;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\HasCurrentSiswa;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;
use Filament\Forms\Set;
use Filament\Tables\Filters\SelectFilter;



class FileUploadResource extends Resource
{
    use HasCurrentSiswa;

    protected static ?string $model = FileUpload::class;

    protected static ?string $navigationGroup = 'Data Siswa';
    protected static ?string $label = 'File Upload';
    protected static ?string $pluralLabel = 'File Uploads';
    protected static ?string $navigationLabel = 'File Upload';

    protected static ?string $navigationIcon = 'heroicon-s-document';

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        // Jika Admin atau Super Admin, tampilkan semua data
        if ($user->hasRole(['Admin', 'super_admin'])) {
            return parent::getEloquentQuery();
        }

        // Jika siswa, filter berdasarkan nisn siswa aktif
        $siswa = static::getCurrentSiswa();

        return parent::getEloquentQuery()
            ->where('nisn', $siswa?->nisn ?? '-');
    }

    public static function form(Form $form): Form
    {

        $file_kategori = FileKategori::all();

        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Fieldset::make('Data Siswa')
                                    ->schema([
                                        Forms\Components\Select::make('nisn')
                                            ->label('NISN')
                                            ->options(function () {
                                                if (Auth::user()->hasRole(['Admin', 'super_admin'])) {
                                                    return Siswa::pluck('nisn', 'nisn'); // pastikan key & value sesuai
                                                }
                                                return [];
                                            })
                                            ->required(Auth::user()->hasRole(['Admin', 'super_admin']))
                                            ->visible(Auth::user()->hasRole(['Admin', 'super_admin']))
                                            ->searchable()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, Set $set) {
                                                $siswa = Siswa::where('nisn', $state)->first();
                                                $set('nama_siswa', $siswa?->nama ?? '-');
                                            })
                                            ->afterStateHydrated(function ($state, Set $set) {
                                                $siswa = Siswa::where('nisn', $state)->first();
                                                $set('nama_siswa', $siswa?->nama ?? '-');
                                            }),

                                        Forms\Components\Hidden::make('nama_siswa'),

                                        Forms\Components\Placeholder::make('nama_siswa_show')
                                            ->label('Nama Siswa')
                                            ->content(fn($get) => $get('nama_siswa') ?? '-')
                                            // ->required(Auth::user()->hasRole(['Admin', 'super_admin']))
                                            ->visible(Auth::user()->hasRole(['Admin', 'super_admin'])),

                                        Forms\Components\Placeholder::make('nisn_siswa_autofill')
                                            ->label('NISN')
                                            ->content(function () {
                                                if (Auth::user()->hasRole('Siswa')) {
                                                    $siswa = \App\HasCurrentSiswa::getCurrentSiswa();
                                                    return $siswa?->nisn ?? '-';
                                                }
                                                return null;
                                            })
                                            ->visible(fn () => Auth::user()->hasRole('Siswa')),


                                        Forms\Components\Placeholder::make('nama_siswa_autofill')
                                            ->label('Nama Siswa')
                                            ->content(function () {
                                                if (Auth::user()->hasRole('Siswa')) {
                                                    $siswa = \App\HasCurrentSiswa::getCurrentSiswa();
                                                    return $siswa?->nama ?? '-';
                                                }
                                                return null;
                                            })
                                            ->visible(fn () => Auth::user()->hasRole('Siswa')),

                                    ]),

                                Forms\Components\Select::make('file_kategori_id')
                                    ->label('Kategori File')
                                    ->required()
                                    ->options($file_kategori->pluck('nama', 'id'))
                                    ->searchable(),

                                // Forms\Components\TextInput::make('nama_file')
                                //     ->label('Nama File')
                                //     ->default(function () use ($siswa, $file_kategori) {
                                //         $kategori = $file_kategori->first(); // Ambil kategori pertama jika tersedia
                                //         $kategoriNama = Str::slug($kategori->nama); // Menjadikan nama kategori slug
                                //         $nisn = $siswa?->nisn ?? '0000000000'; // Jika siswa tidak ada, defaultkan ke '0000000000'
                                //         $namaSiswa = Str::slug($siswa?->nama ?? 'noname'); // Slug untuk nama siswa
                                //         return "{$kategoriNama}_{$nisn}_{$namaSiswa}.pdf"; // Format nama file
                                //     })
                                //     ->disabled(),

                                Forms\Components\FileUpload::make('file')
                                    ->label('Pilih File')
                                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/bmp'])
                                    ->required(),
                            ]),
                    ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();

                if ($user->hasRole('Siswa')) {
                    $siswa = static::getCurrentSiswa();

                    $query->where('nisn', $siswa?->nisn ?? '-');
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

                Tables\Columns\TextColumn::make('fileKategori.nama')
                    ->label('Kategori File')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_file')
                    ->label('Nama File')
                    ->wrap(),

                Tables\Columns\IconColumn::make('google_drive_url')
                    ->label('Link File')
                    ->url(fn($record) => $record->google_drive_url, true) // buka di tab baru
                    ->icon('heroicon-s-document-magnifying-glass')
                    ->tooltip('Klik untuk membuka file')
                    ->wrap(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Upload')
                    ->dateTime('d M Y H:i'),
            ])
            ->filters([
                // Tambah filter kategori / tanggal kalau ingin
                Tables\Filters\Filter::make('uploaded_between')
                    ->label('Tanggal Upload')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('created_at', '<=', $data['until']));
                    }),

                SelectFilter::make('file_kategori_id')
                    ->label('Kategori File')
                    ->options(FileKategori::all()->pluck('nama', 'id'))
                    ->preload()
                    ->searchable(),
                
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => Auth::user()->hasRole(['Admin', 'super_admin']) || $record->nisn === Auth::user()->nisn)
                    ->authorize(fn($record) => Auth::user()->hasRole(['Admin', 'super_admin']) || $record->nisn === Auth::user()->nisn),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => Auth::user()->hasRole(['Admin', 'super_admin']) || $record->nisn === Auth::user()->nisn)
                    ->authorize(fn($record) => Auth::user()->hasRole(['Admin', 'super_admin']) || $record->nisn === Auth::user()->nisn),
            ])
            ->headerActions([
                ...(
                    Auth::user()->hasRole(['Admin', 'super_admin'])
                    ? [
                        ExportAction::make()->exports([
                            ExcelExport::make()
                                ->withColumns([
                                    Column::make('nisn')->heading('NISN'),
                                    Column::make('siswa.nama')->heading('Nama Siswa'),
                                    Column::make('fileKategori.nama')->heading('Kategori File'),
                                    Column::make('nama_file')->heading('Nama File'),
                                    Column::make('google_drive_url')->heading('Link Google Drive'),
                                    Column::make('created_at')->heading('Tanggal Upload')->format('d M Y H:i'),
                                ])
                                ->fromTable() // Mengikuti filter & sorting aktif
                                ->withFilename(fn() => 'file_uploads_' . now()->format('Ymd_His') . '.xlsx')
                        ])->tooltip('Export ke Excel')
                    ]
                    : []
                ),
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
