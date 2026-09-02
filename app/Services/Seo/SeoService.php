<?php

namespace App\Services\Seo;

use App\Models\SeoMeta;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

/**
 * Builds the SEO meta data array for any page.
 *
 * FIX: Cache stores a plain PHP array, not an Eloquent object.
 * Caching Eloquent objects breaks on cache deserialization when the
 * autoloader hasn't loaded the model class yet (common on first hit).
 *
 * Usage:
 *   SeoService::make('home')
 *   SeoService::make('blog.post', ['title' => $post->title, ...])
 *   SeoService::dynamic($title, $description, $canonical)
 */
final class SeoService
{
    private const CACHE_TTL = 3600; // 1 hour

    public static function make(string $routeKey, array $overrides = []): array
    {
        // ── DB lookup — cache as plain array, NEVER as Eloquent object ────────
        /** @var array<string,mixed>|null $db */
        $db = Cache::remember(
            "seo:{$routeKey}",
            self::CACHE_TTL,
            function () use ($routeKey): ?array {
                $row = SeoMeta::query()
                    ->where('route_key', $routeKey)
                    ->where('is_active', true)
                    ->first();

                if ($row === null) {
                    return null;
                }

                // Serialise to plain array immediately — no Eloquent in cache
                return [
                    'title'               => $row->title,
                    'description'         => $row->description,
                    'keywords'            => $row->keywords,
                    'canonical_url'       => $row->canonical_url,
                    'og_title'            => $row->og_title,
                    'og_description'      => $row->og_description,
                    'og_image'            => $row->og_image,
                    'og_type'             => $row->og_type,
                    'twitter_card'        => $row->twitter_card,
                    'twitter_title'       => $row->twitter_title,
                    'twitter_description' => $row->twitter_description,
                    'twitter_image'       => $row->twitter_image,
                    'schema_json'         => $row->schema_json, // already cast to array
                    'robots'              => $row->robots,
                ];
            },
        );

        // ── Config defaults ───────────────────────────────────────────────────
        $defaults = config('cpa.seo', []);
        $appName  = config('app.name', 'VJ CPA CRM');
        $appUrl   = rtrim(config('app.url', url('/')), '/');
        $suffix   = $defaults['title_suffix'] ?? " — {$appName}";

        $rawTitle = $overrides['title']
            ?? $db['title']
            ?? $defaults['default_title']
            ?? $appName;

        $fullTitle = str_contains($rawTitle, $suffix) ? $rawTitle : $rawTitle . $suffix;

        $description = $overrides['description']
            ?? $db['description']
            ?? $defaults['default_description']
            ?? '';

        $canonical = $overrides['canonical_url']
            ?? $db['canonical_url']
            ?? $appUrl . Request::getPathInfo();

        $ogImage = $overrides['og_image']
            ?? $db['og_image']
            ?? $appUrl . ($defaults['default_image'] ?? '/images/og-default.png');

        // ── Schema JSON-LD ────────────────────────────────────────────────────
        $schema = $overrides['schema'] ?? null;
        if ($schema === null && ! empty($db['schema_json'])) {
            $schema = is_string($db['schema_json'])
                ? $db['schema_json']
                : json_encode($db['schema_json']);
        }

        return [
            'title'       => $fullTitle,
            'raw_title'   => $rawTitle,
            'description' => $description,
            'keywords'    => $overrides['keywords'] ?? $db['keywords'] ?? '',
            'canonical'   => $canonical,
            'robots'      => $overrides['robots']   ?? $db['robots']   ?? 'index,follow',

            'og' => [
                'title'       => $overrides['og_title']       ?? $db['og_title']       ?? $rawTitle,
                'description' => $overrides['og_description'] ?? $db['og_description'] ?? $description,
                'image'       => $ogImage,
                'type'        => $overrides['og_type']        ?? $db['og_type']        ?? 'website',
                'url'         => $canonical,
                'site_name'   => $appName,
            ],

            'twitter' => [
                'card'        => $overrides['twitter_card']        ?? $db['twitter_card']        ?? 'summary_large_image',
                'title'       => $overrides['twitter_title']       ?? $db['twitter_title']       ?? $rawTitle,
                'description' => $overrides['twitter_description'] ?? $db['twitter_description'] ?? $description,
                'image'       => $overrides['twitter_image']       ?? $db['twitter_image']       ?? $ogImage,
                'site'        => $defaults['twitter_handle'] ?? '',
            ],

            'schema'    => $schema,
            'route_key' => $routeKey,
        ];
    }

    /**
     * Build SEO for a fully dynamic page (blog post, client profile, etc.).
     * No DB lookup. Defaults robots to noindex for internal app pages.
     */
    public static function dynamic(
        string  $title,
        string  $description = '',
        ?string $canonical   = null,
        ?string $image       = null,
        string  $robots      = 'index,follow',
        array   $schema      = [],
    ): array {
        $appName = config('app.name', 'VJ CPA CRM');
        $suffix  = config('cpa.seo.title_suffix', " — {$appName}");
        $appUrl  = rtrim(config('app.url', url('/')), '/');

        $fullTitle = str_contains($title, $suffix) ? $title : $title . $suffix;
        $ogImage   = $image ?? $appUrl . config('cpa.seo.default_image', '/images/og-default.png');
        $canonical ??= $appUrl . Request::getPathInfo();

        return [
            'title'       => $fullTitle,
            'raw_title'   => $title,
            'description' => $description,
            'keywords'    => '',
            'canonical'   => $canonical,
            'robots'      => $robots,

            'og' => [
                'title'       => $title,
                'description' => $description,
                'image'       => $ogImage,
                'type'        => 'article',
                'url'         => $canonical,
                'site_name'   => $appName,
            ],

            'twitter' => [
                'card'        => 'summary_large_image',
                'title'       => $title,
                'description' => $description,
                'image'       => $ogImage,
                'site'        => config('cpa.seo.twitter_handle', ''),
            ],

            'schema'    => empty($schema) ? null : json_encode($schema),
            'route_key' => '__dynamic__',
        ];
    }

    /** Clear cached SEO for a route key — call after updating seo_metas row. */
    public static function clearCache(string $routeKey): void
    {
        Cache::forget("seo:{$routeKey}");
    }
}
