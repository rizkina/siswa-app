<?php

namespace App\Filament\Resources;

use App\Models\Siswa;
use App\Models\Agama;
use App\Models\Kelas;
use App\Models\Ibu;
use App\Models\Ayah;
use App\Filament\Resources\SiswaResource\Pages;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;
    protected static ?string $navigationGroup = 'Data Siswa';
    protected static ?string $modelLabel = 'Siswa';
    protected static ?string $pluralModelLabel = 'Siswa';
    protected static ?string $navigationIcon = 'heroicon-s-user-group';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('nisn')
                        ->label('NISN')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->length(10)
                        ->numeric()
                        ->rules(['regex:/^\d{10}$/'])
                        ->placeholder('Masukkan NISN'),

                    Forms\Components\TextInput::make('nipd')
                        ->label('NIPD')
                        ->placeholder('Masukkan NIPD/NIS')
                        ->unique(ignoreRecord: true),

                    Forms\Components\TextInput::make('nik')
                        ->label('NIK')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->length(16)
                        ->numeric()
                        ->rules(['regex:/^\d{16}$/'])
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
                        ]),
                ]),
            ]),

            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Fieldset::make('Data Kelahiran')->schema([
                        Forms\Components\TextInput::make('tempat_lahir')
                            ->label('Tempat Lahir')
                            ->placeholder('Tempat Lahir')
                            ->required(),

                        Forms\Components\DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->required(),
                    ]),

                    Forms\Components\Select::make('agama_id')
                        ->label('Agama')
                        ->relationship('agama', 'agama', fn ($query) => $query->orderBy('id_agama', 'asc'))
                        ->required(),

                    Forms\Components\TextInput::make('alamat')
                        ->placeholder('Masukkan Alamat')
                        ->label('Alamat'),

                    Forms\Components\FileUpload::make('foto')
                        ->label('Foto'),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(self::queryFilter(...))
            ->columns(self::getTableColumns())
            ->filters(self::getTableFilters())
            ->actions(self::getTableActions())
            ->headerActions(self::getTableHeaderActions())
            ->bulkActions(self::getTableBulkActions());
    }

    protected static function queryFilter(Builder $query): void
    {
        if (Auth::user()->hasRole('Siswa')) {
            $query->where('nisn', Auth::user()->username);
        }

        $query->with(['agama', 'kelas', 'ibu', 'ayah']);
    }

    protected static function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('nama')->label('Nama')->sortable()->searchable(),
            Tables\Columns\ImageColumn::make('foto')->circular(),
            Tables\Columns\TextColumn::make('nisn')->label('NISN')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('nipd')->label('NIPD')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('nik')->label('NIK')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('jns_kelamin')->label('L/P')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('tempat_lahir')->label('Tempat Lahir')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('tanggal_lahir')->label('Tanggal Lahir')->date()->sortable()->searchable(),
            Tables\Columns\TextColumn::make('agama.agama')->label('Agama')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('alamat')->label('Alamat')->searchable(),
            Tables\Columns\TextColumn::make('kelas.kelas')->label('Kelas')->sortable()->searchable(),
        ];
    }

    protected static function getTableFilters(): array
    {
        $filters = [
            SelectFilter::make('kelas')
                ->label('Kelas')
                ->relationship('kelas', 'kelas')
                ->preload()
                ->searchable(),
        ];

        if (Auth::user()->hasRole(['Admin', 'super_admin'])) {
            $filters[] = TrashedFilter::make();
        }

        return $filters;
    }

    protected static function getTableActions(): array
    {
        return [
            Tables\Actions\EditAction::make(),
        ];
    }

    protected static function getTableHeaderActions(): array
    {
        if (!Auth::user()->hasRole(['Admin', 'super_admin'])) {
            return [];
        }

        return [
            ExportAction::make()->exports([
                ExcelExport::make()
                    ->fromTable()
                    ->withColumns(self::getExportColumns())
                    ->withFilename(fn () => 'data_siswa_' . now()->format('Ymd_His') . '.xlsx'),
            ])
                ->tooltip('Export data siswa ke Excel'),
        ];
    }

    protected static function getExportColumns(): array
    {
        return [
            Column::make('nisn')->heading('NISN'),
            Column::make('nik')->heading('NIK'),
            Column::make('nama')->heading('Nama Siswa'),
            Column::make('jns_kelamin')->heading('Jenis Kelamin'),
            Column::make('tempat_lahir')->heading('Tempat Lahir'),
            Column::make('tanggal_lahir')->heading('Tanggal Lahir'),
            Column::make('agama.agama')->heading('Agama')->formatStateUsing(fn($record) => $record->agama->agama ?? '-'),
            Column::make('alamat')->heading('Alamat'),
            Column::make('kelas.kelas')->heading('Kelas')->formatStateUsing(fn($record) => $record->kelas->kelas ?? '-'),

            Column::make('ibu.nama')->heading('Nama Ibu')->formatStateUsing(fn($record) => $record->ibu->nama ?? '-'),
            Column::make('ibu.nik')->heading('NIK Ibu')->formatStateUsing(fn($record) => $record->ibu->nik ?? '-'),
            Column::make('ibu.tahun_lahir')->heading('Tahun Lahir Ibu')->formatStateUsing(fn($record) => $record->ibu->tahun_lahir ?? '-'),
            Column::make('ibu.pekerjaan')->heading('Pekerjaan Ibu')->formatStateUsing(fn($record) => $record->ibu->pekerjaan ?? '-'),
            Column::make('ibu.pendidikan')->heading('Pendidikan Ibu')->formatStateUsing(fn($record) => $record->ibu->pendidikan ?? '-'),
            Column::make('ibu.penghasilan')->heading('Penghasilan Ibu')->formatStateUsing(fn($record) => $record->ibu->penghasilan ?? '-'),

            Column::make('ayah.nama')->heading('Nama Ayah')->formatStateUsing(fn($record) => $record->ayah->nama ?? '-'),
            Column::make('ayah.nik')->heading('NIK Ayah')->formatStateUsing(fn($record) => $record->ayah->nik ?? '-'),
            Column::make('ayah.tahun_lahir')->heading('Tahun Lahir Ayah')->formatStateUsing(fn($record) => $record->ayah->tahun_lahir ?? '-'),
            Column::make('ayah.pekerjaan')->heading('Pekerjaan Ayah')->formatStateUsing(fn($record) => $record->ayah->pekerjaan ?? '-'),
            Column::make('ayah.pendidikan')->heading('Pendidikan Ayah')->formatStateUsing(fn($record) => $record->ayah->pendidikan ?? '-'),
            Column::make('ayah.penghasilan')->heading('Penghasilan Ayah')->formatStateUsing(fn($record) => $record->ayah->penghasilan ?? '-'),
        ];
    }

    protected static function getTableBulkActions(): array
    {
        $bulkActions = [
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ];

        if (Auth::user()->hasRole(['Admin', 'super_admin'])) {
            $bulkActions[0]->actions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
        }

        return $bulkActions;
    }

    public static function getRelations(): array
    {
        return [];
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
