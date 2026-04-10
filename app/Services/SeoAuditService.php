<?php

namespace App\Services;

use App\Models\VisitorEvent;
use Illuminate\Support\Facades\DB;

class SeoAuditService
{
    public static function audit($settings, $landingPage)
    {
        $checks = [];
        $score = 100;

        // 1. Meta Title Check
        $title = $settings['seo_title'] ?? $landingPage->meta_title ?? '';
        $tLen = strlen($title);
        if ($tLen < 30) { 
            $score -= 10; 
            $checks[] = ['lvl'=>'warn', 'msg'=>"Title is too short ($tLen chars). Min 30 recommended."]; 
        } elseif ($tLen > 65) { 
            $score -= 5; 
            $checks[] = ['lvl'=>'warn', 'msg'=>"Title is too long ($tLen chars). Max 65 recommended."]; 
        } else { 
            $checks[] = ['lvl'=>'ok', 'msg'=>'Title length is optimal.']; 
        }

        // 2. Meta Description Check
        $desc = $settings['seo_description'] ?? $landingPage->meta_description ?? '';
        $dLen = strlen($desc);
        if ($dLen < 120) { 
            $score -= 15; 
            $checks[] = ['lvl'=>'warn', 'msg'=>"Description is too short ($dLen chars). Min 120 recommended."]; 
        } elseif ($dLen > 165) { 
            $score -= 5; 
            $checks[] = ['lvl'=>'warn', 'msg'=>"Description is too long ($dLen chars). Max 165 recommended."]; 
        } else { 
            $checks[] = ['lvl'=>'ok', 'msg'=>'Description length is optimal.']; 
        }

        // 3. Heading Structure
        if (empty($landingPage->hero_title)) {
            $score -= 20;
            $checks[] = ['lvl'=>'fail', 'msg'=>'H1 (Hero Title) is missing. This is critical for SEO.'];
        } else {
            $checks[] = ['lvl'=>'ok', 'msg'=>'H1 heading found.'];
        }

        if (empty($landingPage->featured_trip_title) || empty($landingPage->about_title)) {
            $score -= 10;
            $checks[] = ['lvl'=>'warn', 'msg'=>'Missing secondary headings (H2).'];
        } else {
            $checks[] = ['lvl'=>'ok', 'msg'=>'Heading hierarchy (H1 -> H2) looks good.'];
        }

        // 4. Image Alt Texts (Basic check)
        // In this system, we don't have explicit alt fields, but we check if images are set.
        if (empty($landingPage->hero_background_image) || empty($landingPage->about_image)) {
            $score -= 10;
            $checks[] = ['lvl'=>'warn', 'msg'=>'Some section images are missing, which may impact visual SEO.'];
        } else {
            $checks[] = ['lvl'=>'ok', 'msg'=>'Core section images are present.'];
        }

        // 5. Open Graph / Social
        if (empty($settings['seo_og_image'])) {
            $score -= 10;
            $checks[] = ['lvl'=>'warn', 'msg'=>'Social share image (OG) not set. URL shares will look plain.'];
        }

        // 6. Performance (from visitor events)
        $avgLoadTime = VisitorEvent::where('event_type', 'page_perf')
            ->where('event_name', 'load_time')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->avg(function($e) {
                return $e->metadata['load_time_ms'] ?? 0;
            });

        if ($avgLoadTime > 3000) {
            $score -= 15;
            $checks[] = ['lvl'=>'warn', 'msg'=>"Avg load time is slow (".round($avgLoadTime/1000, 2)."s). Aim for < 2.5s."];
        } elseif ($avgLoadTime > 0) {
            $checks[] = ['lvl'=>'ok', 'msg'=>"Avg load time is healthy (".round($avgLoadTime/1000, 2)."s)."];
        }

        return [
            'score' => max(0, $score),
            'checks' => $checks,
            'stats' => [
                'avg_load_time' => $avgLoadTime
            ]
        ];
    }
}
