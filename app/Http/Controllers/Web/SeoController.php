<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    /**
     * Dynamic robots.txt
     */
    public function robots(): Response
    {
        $appUrl  = rtrim(config('app.url', url('/')), '/');
        $isLocal = in_array(config('app.env'), ['local', 'testing'], true);

        if ($isLocal) {
            $content = "User-agent: *\nDisallow: /\n";
        } else {
            $content = implode("\n", [
                'User-agent: *',
                'Allow: /',
                '',
                '# Block authenticated/private areas',
                'Disallow: /dashboard',
                'Disallow: /platform',
                'Disallow: /portal',
                'Disallow: /api/',
                'Disallow: /login',
                'Disallow: /register',
                'Disallow: /forgot-password',
                'Disallow: /reset-password',
                'Disallow: /email/',
                'Disallow: /two-factor',
                '',
                '# Sitemaps',
                "Sitemap: {$appUrl}/sitemap.xml",
                '',
            ]);
        }

        return response($content, 200, ['Content-Type' => 'text/plain']);
    }

    /**
     * Dynamic sitemap.xml — includes all public marketing pages + published blog posts.
     */
    public function sitemap(): Response
    {
        $appUrl = rtrim(config('app.url', url('/')), '/');
        $now    = now()->toAtomString();

        // ── Static marketing pages ────────────────────────────────────────────
        $urls = [
            ['loc' => $appUrl . '/',          'changefreq' => 'weekly',  'priority' => '1.0', 'lastmod' => $now],
            ['loc' => $appUrl . '/features',  'changefreq' => 'monthly', 'priority' => '0.9', 'lastmod' => $now],
            ['loc' => $appUrl . '/pricing',   'changefreq' => 'monthly', 'priority' => '0.9', 'lastmod' => $now],
            ['loc' => $appUrl . '/demo',      'changefreq' => 'monthly', 'priority' => '0.9', 'lastmod' => $now],
            ['loc' => $appUrl . '/about',     'changefreq' => 'monthly', 'priority' => '0.7', 'lastmod' => $now],
            ['loc' => $appUrl . '/contact',   'changefreq' => 'monthly', 'priority' => '0.7', 'lastmod' => $now],
            ['loc' => $appUrl . '/security',  'changefreq' => 'monthly', 'priority' => '0.7', 'lastmod' => $now],
            ['loc' => $appUrl . '/blog',      'changefreq' => 'weekly',  'priority' => '0.8', 'lastmod' => $now],
            ['loc' => $appUrl . '/privacy',   'changefreq' => 'yearly',  'priority' => '0.3', 'lastmod' => $now],
            ['loc' => $appUrl . '/terms',     'changefreq' => 'yearly',  'priority' => '0.3', 'lastmod' => $now],
        ];

        // ── Blog posts (published only) ───────────────────────────────────────
        try {
            $posts = BlogPost::query()
                ->published()
                ->select(['slug', 'updated_at', 'published_at'])
                ->orderByDesc('published_at')
                ->get();

            foreach ($posts as $post) {
                $lastmod = $post->updated_at?->toAtomString()
                    ?? $post->published_at?->toAtomString()
                    ?? $now;

                $urls[] = [
                    'loc'        => $appUrl . '/blog/' . $post->slug,
                    'changefreq' => 'monthly',
                    'priority'   => '0.7',
                    'lastmod'    => $lastmod,
                ];
            }
        } catch (\Throwable) {
            // If blog table doesn't exist yet, skip silently
        }

        // ── Build XML ─────────────────────────────────────────────────────────
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n";
        $xml .= '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9'
              . ' http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1) . "</loc>\n";
            $xml .= "    <lastmod>{$url['lastmod']}</lastmod>\n";
            $xml .= "    <changefreq>{$url['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$url['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
