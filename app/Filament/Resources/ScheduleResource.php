<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleResource\Pages;
use App\Filament\Resources\ScheduleResource\RelationManagers;
use App\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Booking;
use App\Filament\Resources\BookingResource;
use App\Filament\Resources\FollowUpBookingResource;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $navigationGroup = 'Operational';

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('trip_id')
                    ->relationship('trip', 'title')
                    ->label('Paket Trip')
                    ->searchable()
                    ->required(),
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\DatePicker::make('departure_date')
                            ->label('Keberangkatan')
                            ->required()
                            ->reactive(),
                        Forms\Components\DatePicker::make('return_date')
                            ->label('Kepulangan')
                            ->required(),
                        Forms\Components\TextInput::make('quota')
                            ->label('Kuota Pax')
                            ->numeric()
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('trip.title')
                    ->label('Paket Trip')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('departure_date')
                    ->label('Berangkat')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('return_date')
                    ->label('Pulang')
                    ->date('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->label('Kondisi')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        if ($record->status === 'cancelled') return 'Batal';
                        if (now()->startOfDay()->greaterThan($record->departure_date)) return 'Selesai';
                        return 'Akan Datang';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Akan Datang' => 'info',
                        'Selesai' => 'success',
                        'Batal' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('quota')
                    ->label('Kuota')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('travelers_count')
                    ->label('Terisi')
                    ->counts('travelers')
                    ->formatStateUsing(fn ($state) => "{$state} Pax"),
                Tables\Columns\TextColumn::make('remaining')
                    ->label('Sisa')
                    ->getStateUsing(function ($record) {
                        return $record->quota - $record->travelers()->count();
                    })
                    ->color(fn ($state) => $state < 5 ? 'warning' : 'success')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('departure_date', 'asc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('view_manifest')
                    ->label('Manifest Peserta')
                    ->icon('heroicon-o-users')
                    ->color('info')
                    ->url(fn ($record) => static::getUrl('manifest', ['record' => $record])),
                Tables\Actions\Action::make('cancel_trip')
                    ->label('Batal Berangkat')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->url(fn ($record) => route('admin.schedules.cancel_manual', $record))
                    ->extraAttributes([
                        'onclick' => "return confirm('Batalkan keberangkatan ini? Semua pesanan berbayar akan otomatis dipindah ke daftar Follow-up. Tindakan ini tidak bisa dibatalkan.')"
                    ])
                    ->visible(fn ($record) => $record->status !== 'cancelled' && now()->startOfDay()->lessThan($record->departure_date)),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([10, 25, 50]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ScheduleManifestRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedulesStable::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'view' => Pages\ViewSchedule::route('/{record}'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
            'manifest' => Pages\ViewManifest::route('/{record}/manifest'),
        ];
    }
}
