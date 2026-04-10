<?php

namespace App\Services;

use App\Models\VisitorLog;
use App\Models\VisitorEvent;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class AnalyticsService
{
    public static function logPageView($url = null)
    {
        $log = self::getOrCreateVisitorLog();
        VisitorEvent::create([
            'visitor_log_id' => $log->id,
            'event_type' => 'page_view',
            'url' => $url ?? Request::fullUrl()
        ]);
    }

    public static function logEvent($type, $name = null, $metadata = [])
    {
        $log = self::getOrCreateVisitorLog();
        VisitorEvent::create([
            'visitor_log_id' => $log->id,
            'event_type' => $type,
            'event_name' => $name,
            'metadata' => $metadata
        ]);
    }

    private static function getOrCreateVisitorLog()
    {
        $sessionId = Session::getId();
        
        // Find existing log for this session within the last hour
        $log = VisitorLog::where('session_id', $sessionId)
            ->where('created_at', '>=', now()->subHours(4))
            ->orderBy('created_at', 'desc')
            ->first();

        if ($log) return $log;

        // Otherwise create new
        $ua = Request::userAgent();
        $info = self::parseUserAgent($ua);
        
        // Get IP & Source info
        $ip = Request::ip();
        $referrer = Request::header('referer');
        $source = self::detectSource($referrer);

        // Simple Geo Detection Placeholder
        // In real world, we would use an API or local DB
        $geo = ['country' => 'Unknown', 'region' => 'Unknown', 'city' => 'Unknown'];
        if (app()->environment('production') || true) {
            try {
                // Quick Geo Fetch (simple api)
                $res = @json_decode(@file_get_contents("http://ip-api.com/json/{$ip}?fields=status,country,regionName,city"), true);
                if ($res && $res['status'] === 'success') {
                    $geo = [
                        'country' => $res['country'],
                        'region' => $res['regionName'],
                        'city' => $res['city']
                    ];
                }
            } catch (\Exception $e) {}
        }

        return VisitorLog::create([
            'session_id' => $sessionId,
            'ip_address' => $ip,
            'user_agent' => $ua,
            'device_type' => $info['device'],
            'platform' => $info['platform'],
            'browser' => $info['browser'],
            'country' => $geo['country'],
            'region' => $geo['region'],
            'city' => $geo['city'],
            'referrer' => $referrer,
            'source' => $source,
            'utm_source' => Request::query('utm_source'),
            'utm_medium' => Request::query('utm_medium'),
            'utm_campaign' => Request::query('utm_campaign')
        ]);
    }

    private static function parseUserAgent($ua)
    {
        $browser = 'Other'; $platform = 'Other'; $device = 'Desktop';
        
        if (preg_match('/mobile/i', $ua)) $device = 'Mobile';
        elseif (preg_match('/tablet|ipad|playbook|kindle/i', $ua)) $device = 'Tablet';

        if (preg_match('/windows/i', $ua)) $platform = 'Windows';
        elseif (preg_match('/android/i', $ua)) $platform = 'Android';
        elseif (preg_match('/iphone|ipad|ipod/i', $ua)) $platform = 'iOS';
        elseif (preg_match('/macintosh|mac os x/i', $ua)) $platform = 'macOS';
        elseif (preg_match('/linux/i', $ua)) $platform = 'Linux';

        if (preg_match('/chrome/i', $ua)) $browser = 'Chrome';
        elseif (preg_match('/safari/i', $ua) && !preg_match('/chrome/i', $ua)) $browser = 'Safari';
        elseif (preg_match('/firefox/i', $ua)) $browser = 'Firefox';
        elseif (preg_match('/edge/i', $ua)) $browser = 'Edge';

        return compact('browser', 'platform', 'device');
    }

    private static function detectSource($referrer)
    {
        if (!$referrer) return 'Direct';
        $host = parse_url($referrer, PHP_URL_HOST);
        if (str_contains($host, 'google.') || str_contains($host, 'bing.')) return 'Organic Search';
        if (str_contains($host, 'facebook.com') || str_contains($host, 'instagram.com') || str_contains($host, 't.co')) return 'Social Media';
        return 'Referral';
    }
}
