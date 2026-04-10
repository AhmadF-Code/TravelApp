<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LandingPage extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active'      => 'boolean',
        'gallery_images' => 'array',
        'testimonials'   => 'array',
        'published_at'   => 'datetime',
        'version'        => 'integer',
    ];

    // ─── SCOPES ──────────────────────────────────────────────────────────────

    /** The currently live version shown on the website. */
    public static function active(): ?self
    {
        return static::where('status', 'active')->latest('version')->first();
    }

    /** The latest draft being worked on. */
    public static function latestDraft(): ?self
    {
        return static::where('status', 'draft')->latest('id')->first();
    }

    // ─── STAGING ACTIONS ─────────────────────────────────────────────────────

    /**
     * Create a new DRAFT from the currently active version (or from scratch).
     * Safe: never touches the ACTIVE version.
     */
    public static function createDraftFromActive(array $overrides = []): self
    {
        $active = static::active();

        $data = $active
            ? array_merge($active->toArray(), ['id' => null, 'status' => 'draft', 'version' => $active->version + 1])
            : array_merge($overrides, ['status' => 'draft', 'version' => 1]);

        unset($data['id'], $data['created_at'], $data['updated_at'],
              $data['published_at'], $data['published_by'], $data['is_active']);

        return static::create(array_merge($data, $overrides));
    }

    /**
     * Publish (POST) this DRAFT:
     * 1. Archive the current ACTIVE version
     * 2. Promote this DRAFT → ACTIVE
     */
    public function publish(string $publishedBy = 'admin'): void
    {
        DB::transaction(function () use ($publishedBy) {
            // Archive all currently active versions
            static::where('status', 'active')->update([
                'status'    => 'archived',
                'is_active' => false,
            ]);

            // Promote this draft
            $this->update([
                'status'       => 'active',
                'is_active'    => true,
                'published_by' => $publishedBy,
                'published_at' => now(),
            ]);
        });
    }

    /**
     * Rollback to a specific archived version.
     */
    public static function rollbackTo(int $id): void
    {
        $target = static::findOrFail($id);

        DB::transaction(function () use ($target) {
            static::where('status', 'active')->update([
                'status'    => 'archived',
                'is_active' => false,
            ]);

            $target->update([
                'status'       => 'active',
                'is_active'    => true,
                'published_by' => auth()->user()?->name ?? 'admin',
                'published_at' => now(),
            ]);
        });
    }

    // ─── HELPERS ─────────────────────────────────────────────────────────────

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'active'   => ['label' => 'LIVE',     'bg' => '#dcfce7', 'tc' => '#166534'],
            'draft'    => ['label' => 'DRAFT',    'bg' => '#fef3c7', 'tc' => '#92400e'],
            'archived' => ['label' => 'ARCHIVED', 'bg' => '#f1f5f9', 'tc' => '#475569'],
            default    => ['label' => $this->status, 'bg' => '#f8fafc', 'tc' => '#64748b'],
        };
    }
}
