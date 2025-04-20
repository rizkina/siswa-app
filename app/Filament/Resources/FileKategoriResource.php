<?php

namespace App\Filament\Resources;

// use App\Filament\Resources\FileKategoriResource;
use App\Filament\Resources\FileKategoriResource\Pages;
use App\Filament\Resources\FileKategoriResource\RelationManagers;
use App\Models\FileKategori;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Http\Request;

class FileKategoriResource extends Resource
{
    protected static ?string $model = FileKategori::class;

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Kategori File';

    protected static ?string $pluralLabel = 'Kategori File';


    protected static ?string $navigationIcon = 'heroicon-s-folder-plus';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('nama')
                                    ->label('Nama Kategori')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Masukkan Nama Kategori'),
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->wrap(),
                    Tables\Columns\TextColumn::make('folder_id')
                    ->label('Google Folder ID')
                    ->copyable()
                    ->wrap(),
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
            'index' => Pages\ListFileKategoris::route('/'),
            'create' => Pages\CreateFileKategori::route('/create'),
            'edit' => Pages\EditFileKategori::route('/{record}/edit'),
        ];
    }

    public function store(Request $request)
    {
        //
    }
}
