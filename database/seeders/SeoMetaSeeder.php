<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeoMetaSeeder extends Seeder
{
    public function run(): void
    {
        $appName = config('app.name', 'VJ CPA CRM');
        $appUrl  = rtrim(config('app.url', 'https://cpacrm.com'), '/');
        $now     = now();

        $pages = [
            [
                'route_key'   => 'home',
                'title'       => "Enterprise CPA Practice Management Software | VJ CPA CRM",
                'description' => "The all-in-one practice management platform for CPA firms. Manage clients, filings, deadlines, workflows, documents, accounting, and billing in one place.",
                'keywords'    => "CPA practice management software, accounting firm software, tax filing management, client portal CPA, CPA workflow automation",
                'canonical_url' => $appUrl . '/',
                'og_title'    => "VJ CPA CRM — Modern Practice Management for CPA Firms",
                'og_description' => "Streamline your accounting practice with VJ CPA CRM. Client management, filing deadlines, document sharing, invoicing and more.",
                'og_type'     => 'website',
                'twitter_card' => 'summary_large_image',
                'robots'      => 'index,follow',
                'schema_json' => json_encode([
                    '@context' => 'https://schema.org',
                    '@type'    => 'SoftwareApplication',
                    'name'     => 'VJ CPA CRM',
                    'applicationCategory' => 'BusinessApplication',
                    'operatingSystem'     => 'Web',
                    'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'CAD'],
                    'description' => 'Enterprise CPA practice management software for accounting firms.',
                ]),
            ],
            [
                'route_key'   => 'features',
                'title'       => "Features — VJ CPA CRM",
                'description' => "Explore all the powerful features of VJ CPA CRM: client management, filing engine, deadline tracking, e-signatures, document management, invoicing, and AI assistance.",
                'keywords'    => "CPA software features, accounting practice management features, tax filing software, e-signature CPA",
                'canonical_url' => $appUrl . '/features',
                'og_type'     => 'website',
                'robots'      => 'index,follow',
            ],
            [
                'route_key'   => 'pricing',
                'title'       => "Pricing — VJ CPA CRM",
                'description' => "Simple, transparent pricing for CPA firms of all sizes. Start your 14-day free trial. No credit card required.",
                'keywords'    => "CPA CRM pricing, accounting software cost, practice management subscription, CPA software plans",
                'canonical_url' => $appUrl . '/pricing',
                'og_type'     => 'website',
                'robots'      => 'index,follow',
                'schema_json' => json_encode([
                    '@context'    => 'https://schema.org',
                    '@type'       => 'Product',
                    'name'        => 'VJ CPA CRM',
                    'description' => 'Enterprise CPA practice management software',
                    'offers'      => [
                        ['@type' => 'Offer', 'name' => 'Starter',      'price' => '49',  'priceCurrency' => 'CAD', 'priceSpecification' => ['@type' => 'UnitPriceSpecification', 'billingIncrement' => 1]],
                        ['@type' => 'Offer', 'name' => 'Professional', 'price' => '99',  'priceCurrency' => 'CAD'],
                        ['@type' => 'Offer', 'name' => 'Enterprise',   'price' => '199', 'priceCurrency' => 'CAD'],
                    ],
                ]),
            ],
            [
                'route_key'   => 'about',
                'title'       => "About Us — VJ CPA CRM",
                'description' => "Learn about VJ CPA CRM — built by accountants, for accountants. Our mission is to make practice management effortless for Canadian CPA firms.",
                'keywords'    => "about VJ CPA CRM, CPA software company, accounting software team",
                'canonical_url' => $appUrl . '/about',
                'og_type'     => 'website',
                'robots'      => 'index,follow',
            ],
            [
                'route_key'   => 'contact',
                'title'       => "Contact Us — VJ CPA CRM",
                'description' => "Get in touch with the VJ CPA CRM team. We're here to help with questions, demos, and onboarding support.",
                'keywords'    => "contact VJ CPA CRM, CPA software support, accounting software demo",
                'canonical_url' => $appUrl . '/contact',
                'og_type'     => 'website',
                'robots'      => 'index,follow',
            ],
            [
                'route_key'   => 'privacy',
                'title'       => "Privacy Policy — VJ CPA CRM",
                'description' => "Read our Privacy Policy to understand how VJ CPA CRM collects, uses, and protects your personal information.",
                'canonical_url' => $appUrl . '/privacy',
                'robots'      => 'noindex,follow',
            ],
            [
                'route_key'   => 'terms',
                'title'       => "Terms of Service — VJ CPA CRM",
                'description' => "Read the Terms of Service governing your use of VJ CPA CRM.",
                'canonical_url' => $appUrl . '/terms',
                'robots'      => 'noindex,follow',
            ],
            [
                'route_key'   => 'demo',
                'title'       => "Book a Demo — VJ CPA CRM",
                'description' => "Watch a 5-minute product walkthrough or book a personal 30-minute demo with our team. See how VJ CPA CRM helps CPA firms manage clients, filings, and workflows.",
                'keywords'    => "CPA CRM demo, accounting software demo, practice management walkthrough, book a demo",
                'canonical_url' => $appUrl . '/demo',
                'og_type'     => 'website',
                'robots'      => 'index,follow',
            ],
            [
                'route_key'   => 'security',
                'title'       => "Security — VJ CPA CRM",
                'description' => "How VJ CPA CRM protects your firm and client data: end-to-end encryption, multi-tenant isolation, RBAC, immutable audit logs, and PIPEDA compliance.",
                'keywords'    => "CPA software security, accounting data security, PIPEDA compliance, encrypted CPA software",
                'canonical_url' => $appUrl . '/security',
                'og_type'     => 'website',
                'robots'      => 'index,follow',
            ],
            [
                'route_key'   => 'blog',
                'title'       => "Blog — VJ CPA CRM",
                'description' => "Practice management tips, tax filing guides, workflow automation ideas, and product updates for Canadian CPA firms.",
                'keywords'    => "CPA blog, accounting practice management blog, tax filing tips, CPA workflow",
                'canonical_url' => $appUrl . '/blog',
                'og_type'     => 'website',
                'robots'      => 'index,follow',
                'schema_json' => json_encode([
                    '@context' => 'https://schema.org',
                    '@type'    => 'Blog',
                    'name'     => 'VJ CPA CRM Blog',
                    'description' => 'Insights for CPA firms — practice management, tax filing, and product updates.',
                    'url'      => $appUrl . '/blog',
                    'publisher'=> ['@type' => 'Organization', 'name' => 'VJ CPA CRM'],
                ]),
            ],
        ];

        foreach ($pages as $page) {
            DB::table('seo_metas')->updateOrInsert(
                ['route_key' => $page['route_key']],
                array_merge($page, [
                    'og_title'       => $page['og_title']       ?? null,
                    'og_description' => $page['og_description'] ?? null,
                    'og_image'       => $page['og_image']       ?? null,
                    'twitter_card'   => $page['twitter_card']   ?? 'summary_large_image',
                    'schema_json'    => $page['schema_json']    ?? null,
                    'is_active'      => true,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]),
            );
        }
    }
}
