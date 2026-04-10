<?php

namespace App\Filament\Resources\PromoCodeResource\Pages;

use App\Filament\Resources\PromoCodeResource;
use App\Models\PromoCode;
use Filament\Resources\Pages\Page;
use Illuminate\Pagination\LengthAwarePaginator;

class ListPromoCodesStable extends Page
{
    protected static string $resource = PromoCodeResource::class;

    protected static string $view = 'filament.resources.promo-code-resource.pages.list-promo-codes-stable';

    protected static ?string $title = 'Promo Codes (Stable Mode)';

    public string $search = '';

    public function mount(): void
    {
        $this->search = request()->query('search', '');
    }

    public function getRecordsProperty(): LengthAwarePaginator
    {
        $query = PromoCode::query()
            ->latest('id');

        if ($this->search) {
            $query->where('code', 'like', "%{$this->search}%");
        }

        return $query->paginate(15)->withQueryString();
    }
}
