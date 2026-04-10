<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LandingPageResource\Pages;
use App\Models\LandingPage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LandingPageResource extends Resource
{
    protected static ?string $model = LandingPage::class;
    protected static ?string $navigationGroup = 'Manajemen Konten';
    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    /** Hidden from nav — use Landing Page CMS page instead (supports staging flow) */
    public static function shouldRegisterNavigation(): bool { return false; }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identifikasi')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Jadikan Landing Page Utama')
                            ->required(),
                    ]),
                Forms\Components\Section::make('SEO & Analytics')
                    ->description('Optimasi Mesin Pencari dan Tag Manager')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')->maxLength(255),
                        Forms\Components\TextInput::make('gtm_id')->label('GTM ID')->maxLength(255),
                        Forms\Components\Textarea::make('meta_description')->maxLength(65535)->columnSpanFull(),
                        Forms\Components\Textarea::make('meta_keywords')->maxLength(65535)->columnSpanFull(),
                    ])->collapsed(),
                Forms\Components\Section::make('Hero Section')
                    ->description('Bagian paling atas dalam halaman utama')
                    ->schema([
                        Forms\Components\TextInput::make('hero_title')->maxLength(255),
                        Forms\Components\TextInput::make('hero_subtitle')->maxLength(255),
                        Forms\Components\Textarea::make('hero_text')->maxLength(65535)->columnSpanFull(),
                        Forms\Components\TextInput::make('hero_background_image')->label('Background Image URL')->url(),
                    ])->collapsed(),
                Forms\Components\Section::make('Produk Unggulan')
                    ->description('Judul pada bagian paket perjalanan terlaris')
                    ->schema([
                        Forms\Components\TextInput::make('featured_trip_title')->maxLength(255),
                        Forms\Components\TextInput::make('featured_trip_subtitle')->maxLength(255),
                    ])->collapsed(),
                Forms\Components\Section::make('Tentang Kami')
                    ->description('Block presentasi mengenai AVRA Tour')
                    ->schema([
                        Forms\Components\TextInput::make('about_title')->maxLength(255),
                        Forms\Components\TextInput::make('about_subtitle')->maxLength(255),
                        Forms\Components\Textarea::make('about_text')->maxLength(65535)->columnSpanFull(),
                        Forms\Components\TextInput::make('about_image')->label('About Image URL')->url(),
                    ])->collapsed(),
                Forms\Components\Section::make('Galeri & Media')
                    ->description('Video promosi YouTube dan dokumentasi pelanggan')
                    ->schema([
                        Forms\Components\TextInput::make('youtube_url')->url()->maxLength(255),
                        Forms\Components\FileUpload::make('gallery_images')
                            ->label('Upload Foto Dokumentasi')
                            ->multiple()
                            ->image()
                            ->disk('public')
                            ->maxFiles(8)
                            ->columnSpanFull(),
                    ])->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
                Tables\Columns\TextColumn::make('meta_title')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gtm_id')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('hero_title')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('hero_subtitle')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('featured_trip_title')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('about_title')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLandingPages::route('/'),
            'create' => Pages\CreateLandingPage::route('/create'),
            'edit' => Pages\EditLandingPage::route('/{record}/edit'),
        ];
    }
}
