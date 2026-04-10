<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\RelationManagers;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;
    protected static ?string $navigationGroup = 'Operational';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?int $navigationSort = 2;
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('General Information')
                        ->schema([
                            Forms\Components\TextInput::make('booking_code')
                                ->label('Group Booking ID')
                                ->disabled()
                                ->dehydrated(false),
                            Forms\Components\Select::make('schedule_id')
                                ->relationship('schedule', 'id')
                                ->getOptionLabelFromRecordUsing(fn ($record) => $record->trip->title . ' (' . $record->departure_date->format('d M Y') . ')')
                                ->required(),
                            Forms\Components\Select::make('branch_id')
                                ->relationship('branch', 'name')
                                ->required(),
                            Forms\Components\TextInput::make('pax')
                                ->label('Total Quota Seats')
                                ->numeric()
                                ->required()
                                ->helperText('Initial number of seats booked.'),
                        ])->columns(2),
                ])->columnSpanFull(),
                
                        Forms\Components\Section::make('Detail Pembayaran & Refund')
                            ->schema([
                                Forms\Components\Placeholder::make('booking_code')
                                    ->label('Kode Booking')
                                    ->content(fn ($record) => $record?->booking_code),
                                Forms\Components\TextInput::make('refund_amount')
                                    ->label('Total Dana Direfund')
                                    ->numeric()
                                    ->prefix('IDR')
                                    ->disabled()
                                    ->helperText('Dana yang telah dikembalikan kepada peserta.'),
                                Forms\Components\TextInput::make('total_amount')
                                    ->label('Total Bayar (Sisa)')
                                    ->numeric()
                                    ->prefix('IDR')
                                    ->required()
                                    ->helperText('Total dana masuk setelah dikurangi refund.'),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'paid' => 'Paid',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->required(),
                            ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_code')
                    ->label('Group ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('schedule.trip.title')
                    ->label('Trip')
                    ->searchable(),
                Tables\Columns\TextColumn::make('schedule.departure_date')
                    ->label('Departs')
                    ->date('M j, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Leader')
                    ->searchable(),
                Tables\Columns\TextColumn::make('active_travelers_count')
                    ->label('Active Pax')
                    ->badge()
                    ->counts([
                        'travelers' => fn (Builder $query) => $query->where('status', '!=', 'cancelled'),
                    ])
                    ->color('info'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Paid (Final)')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('refund_amount')
                    ->label('Refunded')
                    ->money('IDR')
                    ->color('danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string|null $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'cancelled' => 'danger',
                        'on_followup' => 'info',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([10, 25, 50]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\BookingTravelersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookingsStable::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'view' => Pages\ViewBooking::route('/{record}'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
            'refund' => Pages\RefundTraveler::route('/{record}/refund/{traveler}'),
        ];
    }
}
