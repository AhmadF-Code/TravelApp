<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParticipantResource\Pages;
use App\Models\BookingTraveler;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ParticipantResource extends Resource
{
    protected static ?string $model = BookingTraveler::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Database Peserta';
    protected static ?string $navigationGroup = 'Operational';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Peserta')
                    ->description('Data identitas peserta yang terdaftar.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('ktp')
                            ->label('Nomor KTP')
                            ->placeholder('Masukkan 16 digit NIK')
                            ->maxLength(16)
                            ->helperText('Hanya dapat diakses dan diubah melalui halaman Database Peserta.'),
                        Forms\Components\TextInput::make('passport_number')
                            ->label('Passport Number')
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['booking.schedule.trip']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParticipantsStable::route('/'),
        ];
    }
}
