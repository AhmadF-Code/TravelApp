<?php

namespace App\Filament\Pages;

use App\Models\LandingPage;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class LandingPageCms extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-paint-brush';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Landing Page CMS';
    protected static ?string $title           = 'Landing Page CMS';
    protected static ?int    $navigationSort  = 10;
    protected static string  $view            = 'filament.pages.landing-page-cms';

    // Working record (the DRAFT being edited)
    public ?int $draftId = null;

    // Form fields — hero
    public ?string $title_field          = null;
    public ?string $hero_title           = null;
    public ?string $hero_subtitle        = null;
    public ?string $hero_text            = null;
    public ?string $hero_background_image= null;

    // Featured section
    public ?string $featured_trip_title    = null;
    public ?string $featured_trip_subtitle = null;

    // About
    public ?string $about_title    = null;
    public ?string $about_subtitle = null;
    public ?string $about_text     = null;
    public ?string $about_image    = null;

    // Testimonials
    public ?string $testimonial_title = null;
    public array   $testimonials      = []; 

    // CTA
    public ?string $cta_title    = null;
    public ?string $cta_subtitle = null;

    // Footer
    public ?string $footer_text    = null;
    public ?string $footer_email   = null;
    public ?string $footer_phone   = null;
    public ?string $footer_address = null;

    // SEO / Config
    public ?string $meta_title       = null;
    public ?string $meta_description = null;
    public ?string $meta_keywords    = null;
    public ?string $gtm_id           = null;
    public ?string $youtube_url      = null;

    // Staging control
    public ?string $change_summary = null;

    public function mount(): void
    {
        $this->loadDraft();
    }

    private function loadDraft(): void
    {
        $draft = LandingPage::latestDraft() ?? LandingPage::active();

        if (!$draft) {
            return;
        }

        $this->draftId                 = $draft->id;
        $this->title_field             = $draft->title;
        $this->hero_title              = $draft->hero_title;
        $this->hero_subtitle           = $draft->hero_subtitle;
        $this->hero_text               = $draft->hero_text;
        $this->hero_background_image   = $draft->hero_background_image;
        $this->featured_trip_title     = $draft->featured_trip_title;
        $this->featured_trip_subtitle  = $draft->featured_trip_subtitle;
        $this->about_title             = $draft->about_title;
        $this->about_subtitle          = $draft->about_subtitle;
        $this->about_text              = $draft->about_text;
        $this->about_image             = $draft->about_image;
        
        $this->testimonial_title       = $draft->testimonial_title;
        $this->testimonials            = $draft->testimonials ?? [];

        $this->cta_title               = $draft->cta_title;
        $this->cta_subtitle            = $draft->cta_subtitle;

        $this->footer_text             = $draft->footer_text;
        $this->footer_email            = $draft->footer_email;
        $this->footer_phone            = $draft->footer_phone;
        $this->footer_address          = $draft->footer_address;

        $this->meta_title              = $draft->meta_title;
        $this->meta_description        = $draft->meta_description;
        $this->meta_keywords           = $draft->meta_keywords;
        $this->gtm_id                  = $draft->gtm_id;
        $this->youtube_url             = $draft->youtube_url;
    }

    // ─── TESTIMONIAL HELPERS ───────────────────────────────────────────────

    public function addTestimonial(): void
    {
        $this->testimonials[] = ['name' => '', 'quote' => '', 'stars' => 5];
    }

    public function removeTestimonial(int $index): void
    {
        unset($this->testimonials[$index]);
        $this->testimonials = array_values($this->testimonials);
    }

    // ─── CRUD ACTIONS ──────────────────────────────────────────────────────

    public function saveDraft(): void
    {
        $data = $this->getFormData();

        if ($this->draftId && LandingPage::where('id', $this->draftId)->where('status', 'draft')->exists()) {
            LandingPage::find($this->draftId)->update($data);
        } else {
            $draft = LandingPage::createDraftFromActive($data);
            $this->draftId = $draft->id;
        }

        Notification::make()
            ->title('Draft berhasil disimpan.')
            ->body('Perubahan belum tayang. Klik "Publish" untuk menayangkan.')
            ->success()
            ->send();
    }

    public function publishDraft(): void
    {
        $this->saveDraft();

        $draft = LandingPage::find($this->draftId);

        if (!$draft || $draft->status !== 'draft') {
            Notification::make()->title('Tidak ada DRAFT untuk di-publish.')->danger()->send();
            return;
        }

        $draft->update(['change_summary' => $this->change_summary]);
        $draft->publish(auth()->user()?->name ?? 'admin');

        Notification::make()
            ->title('Landing Page berhasil ditayangkan!')
            ->body('Versi v' . $draft->version . ' kini LIVE.')
            ->success()
            ->send();

        $this->loadDraft();
    }

    public function rollback(int $id): void
    {
        LandingPage::rollbackTo($id);
        Notification::make()->title('Rollback berhasil.')->success()->send();
        $this->loadDraft();
    }

    public function getActiveVersionProperty(): ?LandingPage
    {
        return LandingPage::active();
    }

    public function getDraftVersionProperty(): ?LandingPage
    {
        return $this->draftId ? LandingPage::find($this->draftId) : null;
    }

    public function getVersionHistoryProperty()
    {
        return LandingPage::whereIn('status', ['active', 'archived'])
            ->orderByDesc('version')
            ->limit(10)
            ->get();
    }

    private function getFormData(): array
    {
        return [
            'title'                  => $this->title_field ?? 'Landing Page',
            'hero_title'             => $this->hero_title,
            'hero_subtitle'          => $this->hero_subtitle,
            'hero_text'              => $this->hero_text,
            'hero_background_image'  => $this->hero_background_image,
            'featured_trip_title'    => $this->featured_trip_title,
            'featured_trip_subtitle' => $this->featured_trip_subtitle,
            'about_title'            => $this->about_title,
            'about_subtitle'         => $this->about_subtitle,
            'about_text'             => $this->about_text,
            'about_image'            => $this->about_image,
            
            'testimonial_title'      => $this->testimonial_title,
            'testimonials'           => $this->testimonials,

            'cta_title'              => $this->cta_title,
            'cta_subtitle'           => $this->cta_subtitle,

            'footer_text'            => $this->footer_text,
            'footer_email'           => $this->footer_email,
            'footer_phone'           => $this->footer_phone,
            'footer_address'         => $this->footer_address,

            'meta_title'             => $this->meta_title,
            'meta_description'       => $this->meta_description,
            'meta_keywords'          => $this->meta_keywords,
            'gtm_id'                 => $this->gtm_id,
            'youtube_url'            => $this->youtube_url,
        ];
    }
}
