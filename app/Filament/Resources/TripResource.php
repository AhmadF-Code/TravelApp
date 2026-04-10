<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TripResource\Pages;
use App\Models\Trip;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TripResource extends Resource
{
    protected static ?string $model = Trip::class;
    protected static ?string $navigationGroup = 'Operational';
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Paket Trip';
    protected static ?string $pluralModelLabel = 'Paket Trip';
    protected static ?string $modelLabel = 'Paket Trip';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Utama')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Judul Trip')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) =>
                            $set('slug', \Illuminate\Support\Str::slug($state))
                        ),
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug URL')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\Textarea::make('description')
                        ->label('Deskripsi')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('destination_country')
                        ->label('Negara Tujuan')
                        ->placeholder('Thailand, Malaysia, Singapore')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('price')
                        ->label('Harga (Rp)')
                        ->required()
                        ->numeric()
                        ->prefix('Rp'),
                    Forms\Components\TextInput::make('duration_days')
                        ->label('Durasi (hari)')
                        ->required()
                        ->numeric()
                        ->default(1)
                        ->suffix('hari'),
                    Forms\Components\Toggle::make('is_domestic')
                        ->label('Trip Domestik?')
                        ->default(true),
                    Forms\Components\Toggle::make('is_featured')
                    ->label('Produk Unggulan')
                    ->default(false)
                    ->helperText('Tampilkan di seksi produk unggulan landing page')
                    ->required(),
                Forms\Components\FileUpload::make('image')
                        ->label('Foto Utama')
                        ->image()
                        ->disk('public')
                        ->directory('trips')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Lokasi & Detail per Negara')
                ->description('Tambahkan setiap negara/destinasi yang akan dikunjungi beserta itinerary dan akomodasinya.')
                ->schema([
                    Forms\Components\Repeater::make('locations')
                        ->label('Destinasi / Negara')
                        ->relationship()
                        ->orderColumn('sort_order')
                        ->schema([
                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\TextInput::make('country')
                                    ->label('Negara')
                                    ->required()
                                    ->placeholder('Thailand'),
                                Forms\Components\TextInput::make('city')
                                    ->label('Kota')
                                    ->placeholder('Bangkok, Phuket'),
                                Forms\Components\TextInput::make('flag_emoji')
                                    ->label('Emoji Bendera')
                                    ->placeholder('🇹🇭')
                                    ->maxLength(10),
                            ]),

                            Forms\Components\Repeater::make('itineraries')
                                ->label('Rangkaian Itinerary')
                                ->relationship()
                                ->orderColumn('sort_order')
                                ->schema([
                                    Forms\Components\Grid::make(4)->schema([
                                        Forms\Components\TextInput::make('day')
                                            ->label('Hari ke-')
                                            ->numeric()
                                            ->required()
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('title')
                                            ->label('Judul Kegiatan')
                                            ->required()
                                            ->columnSpan(3),
                                    ]),
                                    Forms\Components\Textarea::make('description')
                                        ->label('Deskripsi')
                                        ->rows(2)
                                        ->columnSpanFull(),
                                ])
                                ->columnSpanFull()
                                ->collapsible()
                                ->itemLabel(fn ($state) => isset($state['day'], $state['title']) ? "Hari {$state['day']}: {$state['title']}" : 'Itinerary'),

                            Forms\Components\Repeater::make('accommodations')
                                ->label('Akomodasi')
                                ->relationship()
                                ->schema([
                                    Forms\Components\Grid::make(3)->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nama Hotel')
                                            ->required(),
                                        Forms\Components\TextInput::make('type')
                                            ->label('Tipe')
                                            ->placeholder('Bintang 4'),
                                        Forms\Components\TextInput::make('notes')
                                            ->label('Catatan')
                                            ->placeholder('Fasilitas, lokasi, dll'),
                                    ]),
                                ])
                                ->columnSpanFull()
                                ->collapsible()
                                ->itemLabel(fn ($state) => $state['name'] ?? 'Akomodasi'),
                        ])
                        ->columnSpanFull()
                        ->collapsible()
                        ->itemLabel(fn ($state) => isset($state['flag_emoji'], $state['country'])
                            ? "{$state['flag_emoji']} {$state['country']}" . (isset($state['city']) ? " — {$state['city']}" : '')
                            : 'Lokasi Baru'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Foto')->width(80),
                Tables\Columns\ToggleColumn::make('is_featured')
                    ->label('Unggulan'),
                Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('is_domestic')->label('Domestik')->boolean(),
                Tables\Columns\TextColumn::make('destination_country')->label('Negara')->searchable(),
                Tables\Columns\TextColumn::make('duration_days')->label('Durasi')->suffix(' hari')->sortable(),
                Tables\Columns\TextColumn::make('price')->label('Harga')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('locations_count')
                    ->label('# Lokasi')
                    ->counts('locations'),
                Tables\Columns\TextColumn::make('schedules_count')
                    ->label('# Jadwal')
                    ->counts('schedules'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTripsStable::route('/'),
            'create' => Pages\CreateTrip::route('/create'),
            'edit'   => Pages\EditTrip::route('/{record}/edit'),
        ];
    }
}
