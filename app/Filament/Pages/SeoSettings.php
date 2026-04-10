<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Setting;
use App\Models\LandingPage;
use App\Services\SeoAuditService;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;

class SeoSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static string $view = 'filament.pages.seo-settings';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'SEO & Tracking';
    protected static ?string $title = 'SEO & Conversion Tracking';

    public $seo_title;
    public $seo_description;
    public $seo_keywords;
    public $seo_og_image;
    public $seo_favicon;

    public function mount()
    {
        $settings = Setting::where('group', 'seo')->get()->pluck('value', 'key');
        $this->seo_title = $settings['seo_title'] ?? '';
        $this->seo_description = $settings['seo_description'] ?? '';
        $this->seo_keywords = $settings['seo_keywords'] ?? '';
        $this->seo_og_image = $settings['seo_og_image'] ?? '';
        $this->seo_favicon = $settings['seo_favicon'] ?? '';
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('SEO Metadata')
                ->description('Pengaturan meta tag untuk optimasi mesin pencari (Google/Bing).')
                ->schema([
                    TextInput::make('seo_title')->label('Meta Title')
                        ->placeholder('Optimalkan judul (50-65 karakter)')
                        ->maxLength(255),
                    Textarea::make('seo_description')->label('Meta Description')
                        ->placeholder('Optimalkan deskripsi (150-160 karakter)')
                        ->rows(3),
                    TextInput::make('seo_keywords')->label('Keywords')
                        ->placeholder('travel, agent, trip, murah (pisahkan dengan koma)'),
                ]),
            
            Section::make('Social Sharing & Favicon')
                ->description('Pengaturan tampilan saat link dibagikan di media sosial dan favicon browser.')
                ->schema([
                    Grid::make(2)->schema([
                        FileUpload::make('seo_favicon')->label('Favicon')
                            ->image()->directory('seo'),
                        FileUpload::make('seo_og_image')->label('Share Image (Open Graph)')
                            ->image()->directory('seo')->helperText('Gunakan ukuran 1200x630 untuk hasil terbaik.'),
                    ])
                ])
        ];
    }

    public function save()
    {
        $data = [
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'seo_keywords' => $this->seo_keywords,
            'seo_og_image' => $this->seo_og_image,
            'seo_favicon' => $this->seo_favicon,
        ];

        foreach ($data as $k => $v) {
            Setting::updateOrCreate(
                ['group' => 'seo', 'key' => $k],
                ['value' => $v]
            );
        }

        Notification::make()->title('SEO Settings Updated!')->success()->send();
    }

    protected array $rules = [
        'seo_title' => 'string|max:255|nullable',
        'seo_description' => 'string|nullable',
        'seo_keywords' => 'string|nullable',
    ];

    public function getSeoAuditProperty()
    {
        $settings = [
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'seo_keywords' => $this->seo_keywords,
            'seo_og_image' => $this->seo_og_image,
        ];
        $landingPage = LandingPage::active() ?? LandingPage::first();
        return SeoAuditService::audit($settings, $landingPage);
    }
}
