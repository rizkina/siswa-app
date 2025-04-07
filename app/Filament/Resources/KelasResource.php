<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KelasResource\Pages;
use App\Filament\Resources\KelasResource\RelationManagers;
use App\Models\Kelas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KelasResource extends Resource
{
    protected static ?string $model = Kelas::class;

    protected static ?string $navigationGroup = 'Data Periodik';

    protected static ?string $modelLabel = 'Kelas';

    protected static ?string $pluralModelLabel = 'Kelas';

    protected static ?string $navigationIcon = 'heroicon-s-square-3-stack-3d';

    protected static ?int $navigationSort = 4;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Group::make()
                            ->label('Data Kelas')
                            ->schema([
                                Forms\Components\Select::make('id_tahun_pelajaran')
                                    ->label('Tahun Pelajaran')
                                    // ->relationship('tahun_pelajaran', 'tahun_pelajaran', function ($query) {
                                    //     // return $query->where('aktif', 1); // Untuk menampilkan hanya tahun pelajran yang aktif saja
                                    //     return $query->orderByDesc('aktif')->orderBy('tahun_pelajaran', 'desc');
                                    // })
                                    ->options(function () {
                                        return \App\Models\TahunPelajaran::orderByDesc('aktif')
                                            ->orderBy('tahun_pelajaran', 'desc')
                                            ->get()
                                            ->mapWithKeys(function ($tp) {
                                                $label = $tp->tahun_pelajaran;
                                                if ($tp->aktif == 1) {
                                                    $label .= ' (Aktif)';
                                                }
                                                return [$tp->id => $label];
                                            })
                                            ->toArray();
                                    })
                                    ->preload()
                                    ->default(fn() => \App\Models\TahunPelajaran::where('aktif', 1)->first()->id)
                                    ->required(),
                                Forms\Components\Select::make('id_tingkat')
                                    ->label('Tingkat')
                                    ->relationship('tingkat', 'tingkat')
                                    ->preload()
                                    ->required(),
                                Forms\Components\Select::make('id_jurusan')
                                    ->label('Jurusan')
                                    ->relationship('jurusan', 'nama_jurusan')
                                    ->preload()
                                    ->required(),
                                Forms\Components\TextInput::make('kelas')
                                    ->label('Kelas')
                                    ->required()
                                    ->placeholder('Nama Kelas'),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tahun_pelajaran.tahun_pelajaran')
                    ->label('Tahun Pelajaran')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tingkat.tingkat')
                    ->label('Tingkat')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jurusan.nama_jurusan')
                    ->label('Jurusan')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('kelas')
                    ->label('Kelas')
                    ->sortable()
                    ->searchable(),
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
            'index' => Pages\ListKelas::route('/'),
            'create' => Pages\CreateKelas::route('/create'),
            'edit' => Pages\EditKelas::route('/{record}/edit'),
        ];
    }
}
