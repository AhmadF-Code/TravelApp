<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class GeneralSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.general-settings';
    protected static ?string $navigationGroup = 'Master Setting';
    protected static ?string $title = 'Konfigurasi Website (Deprecated)';
    protected static ?string $navigationLabel = 'Setting Website';

    /** Hidden from nav — superseded by LandingPageCms */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        
        // Handle JSON decoded values for gallery
        if(isset($settings['documentation_gallery'])){
            $settings['documentation_gallery'] = json_decode($settings['documentation_gallery'], true);
        }

        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Hero Section')
                    ->description('Pengaturan bagian utama (atas) website.')
                    ->collapsible()
                    ->schema([
                        TextInput::make('hero_title')
                            ->label('Judul Hero')
                            ->placeholder('Contoh: Jelajahi Dunia Bersama Avra Tour')
                            ->required(),
                        Textarea::make('hero_subtitle')
                            ->label('Sub-judul Hero')
                            ->placeholder('Teks pendek di bawah judul utama'),
                        FileUpload::make('hero_image')
                            ->label('Gambar Hero')
                            ->image()
                            ->disk('public')
                            ->directory('settings')
                            ->helperText('Rekomendasi Resolusi: 1920x1080 (HD) untuk hasil terbaik.'),
                    ]),

                Section::make('Tentang Avra Tour')
                    ->description('Pengaturan konten seksi Tentang Kami.')
                    ->collapsible()
                    ->schema([
                        TextInput::make('about_title')
                            ->label('Judul Seksi')
                            ->default('Tentang Avra Tour'),
                        Textarea::make('about_content')
                            ->label('Konten Deskripsi')
                            ->rows(5)
                            ->required(),
                        FileUpload::make('about_image')
                            ->label('Foto Seksi Tentang')
                            ->image()
                            ->disk('public')
                            ->directory('settings')
                            ->helperText('Rekomendasi Resolusi: 800x600 px.'),
                    ]),

                Section::make('Youtube & Galeri')
                    ->description('Link dokumentasi video dan foto kegiatan.')
                    ->collapsible()
                    ->schema([
                        TextInput::make('youtube_link')
                            ->label('Link YouTube')
                            ->placeholder('https://www.youtube.com/watch?v=...')
                            ->url(),
                        FileUpload::make('documentation_gallery')
                            ->label('Foto Dokumentasi')
                            ->multiple()
                            ->image()
                            ->disk('public')
                            ->directory('gallery')
                            ->reorderable()
                            ->helperText('Upload beberapa foto dokumentasi terbaik untuk ditampilkan di gallery.'),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            foreach ($data as $key => $value) {
                // Encode arrays (like gallery) to JSON
                if(is_array($value)){
                    $value = json_encode($value);
                }

                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'group' => 'general']
                );
            }

            Notification::make()
                ->title('Berhasil!')
                ->body('Pengaturan website telah diperbarui.')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal!')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
