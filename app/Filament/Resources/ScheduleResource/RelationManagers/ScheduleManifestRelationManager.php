<?php

namespace App\Filament\Resources\ScheduleResource\RelationManagers;

use App\Filament\Resources\BookingResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ScheduleManifestRelationManager extends RelationManager
{
    protected static string $relationship = 'travelers';

    protected static ?string $title = 'Manifest Peserta';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('traveler_code')
                    ->label('TKT ID')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('WhatsApp'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('booking.booking_code')
                    ->label('Group ID')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'paid' => 'Sudah Bayar',
                        'pending' => 'Menunggu',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['value'], fn ($q) => $q->where('booking_travelers.status', $data['value']))
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('view_booking')
                    ->label('Buka Booking')
                    ->icon('heroicon-m-eye')
                    ->url(fn ($record) => BookingResource::getUrl('view', ['record' => $record->booking_id])),
            ]);
    }
}
