<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $now    = now();
        $author = User::where('user_type', 'platform_admin')->first();

        // ── Categories ────────────────────────────────────────────────────────
        $categories = [
            ['name' => 'Practice Management',  'slug' => 'practice-management',  'sort_order' => 1],
            ['name' => 'Tax & Filing',          'slug' => 'tax-filing',           'sort_order' => 2],
            ['name' => 'Technology',            'slug' => 'technology',           'sort_order' => 3],
            ['name' => 'Client Management',     'slug' => 'client-management',    'sort_order' => 4],
            ['name' => 'Workflow & Automation', 'slug' => 'workflow-automation',  'sort_order' => 5],
            ['name' => 'News & Updates',        'slug' => 'news-updates',         'sort_order' => 6],
        ];

        foreach ($categories as $cat) {
            BlogCategory::firstOrCreate(
                ['slug' => $cat['slug']],
                array_merge($cat, ['is_active' => true, 'created_at' => $now, 'updated_at' => $now]),
            );
        }

        $catMap = BlogCategory::pluck('id', 'slug');

        // ── Tags ──────────────────────────────────────────────────────────────
        $tagNames = [
            'CPA', 'T1', 'T2', 'GST/HST', 'Payroll', 'Filing Deadlines',
            'Workflow', 'Client Portal', 'E-Signatures', 'Invoicing',
            'Time Tracking', 'Cloud Software', 'Practice Tips',
            'Accounting', 'Automation',
        ];

        foreach ($tagNames as $tag) {
            BlogTag::firstOrCreate(
                ['slug' => Str::slug($tag)],
                ['name' => $tag, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            );
        }

        $tagMap = BlogTag::pluck('id', 'slug');

        // ── Posts ─────────────────────────────────────────────────────────────
        $posts = [
            [
                'category'  => 'practice-management',
                'tags'      => ['cpa', 'practice-tips'],
                'title'     => '10 Ways to Streamline Your CPA Practice in 2026',
                'slug'      => '10-ways-streamline-cpa-practice-2026',
                'excerpt'   => 'Discover ten proven strategies that top CPA firms are using to reduce admin time, improve client satisfaction, and grow their practice efficiently.',
                'body'      => $this->body10Ways(),
                'image'     => null,
                'published' => $now->copy()->subDays(2),
                'meta_title'=> '10 Ways to Streamline Your CPA Practice in 2026 — VJ CPA CRM',
                'meta_desc' => 'Discover ten proven strategies that top CPA firms are using to reduce admin time, improve client satisfaction, and grow their practice in 2026.',
                'keywords'  => 'CPA practice management, accounting firm efficiency, practice management tips 2026',
            ],
            [
                'category'  => 'tax-filing',
                'tags'      => ['t1', 'filing-deadlines', 'gst-hst'],
                'title'     => 'Canadian Tax Filing Deadlines You Cannot Miss in 2026',
                'slug'      => 'canadian-tax-filing-deadlines-2026',
                'excerpt'   => 'A comprehensive calendar of every major CRA filing deadline for 2026 — T1, T2, T3, GST/HST, payroll remittances, and more.',
                'body'      => $this->bodyDeadlines(),
                'image'     => null,
                'published' => $now->copy()->subDays(5),
                'meta_title'=> 'Canadian Tax Filing Deadlines 2026 — Complete Calendar',
                'meta_desc' => 'Complete guide to 2026 Canadian tax filing deadlines: T1, T2, T3, GST/HST, payroll. Never miss a CRA due date again.',
                'keywords'  => '2026 tax deadlines Canada, CRA filing dates, T1 deadline, T2 deadline, GST/HST filing',
            ],
            [
                'category'  => 'technology',
                'tags'      => ['cloud-software', 'automation'],
                'title'     => 'Why CPA Firms Are Moving to Cloud-Based Practice Management',
                'slug'      => 'cpa-firms-moving-cloud-practice-management',
                'excerpt'   => 'Cloud-based practice management software is transforming how CPA firms operate. Here is what the data shows and why the shift is accelerating.',
                'body'      => $this->bodyCloud(),
                'image'     => null,
                'published' => $now->copy()->subDays(10),
                'meta_title'=> 'Why CPA Firms Are Moving to Cloud Practice Management Software',
                'meta_desc' => 'Cloud-based practice management is reshaping accounting firms. Discover the key benefits, ROI data, and how to evaluate the right platform.',
                'keywords'  => 'cloud CPA software, practice management software, accounting firm cloud migration',
            ],
            [
                'category'  => 'client-management',
                'tags'      => ['client-portal', 'cpa'],
                'title'     => 'How a Client Portal Transforms Your Firm\'s Communication',
                'slug'      => 'client-portal-transforms-firm-communication',
                'excerpt'   => 'Firms that adopt client portals report 40% fewer email back-and-forths and significantly higher client satisfaction scores. Here is how to make it work.',
                'body'      => $this->bodyPortal(),
                'image'     => null,
                'published' => $now->copy()->subDays(15),
                'meta_title'=> 'How a Client Portal Transforms CPA Firm Communication',
                'meta_desc' => 'A secure client portal reduces email overload, speeds up document collection, and improves client satisfaction for CPA firms.',
                'keywords'  => 'CPA client portal, accounting firm communication, document sharing portal',
            ],
            [
                'category'  => 'workflow-automation',
                'tags'      => ['workflow', 'automation', 'practice-tips'],
                'title'     => 'Building Repeatable Workflows That Scale With Your Practice',
                'slug'      => 'building-repeatable-workflows-scale-practice',
                'excerpt'   => 'The secret to a scalable accounting practice is not hiring more people — it is building systems that work the same way every time, without manual effort.',
                'body'      => $this->bodyWorkflow(),
                'image'     => null,
                'published' => $now->copy()->subDays(20),
                'meta_title'=> 'Building Repeatable Workflows That Scale Your CPA Practice',
                'meta_desc' => 'Discover how to build workflow templates that eliminate manual work, reduce errors, and let your CPA practice scale without proportionally growing headcount.',
                'keywords'  => 'CPA workflow automation, accounting workflow templates, practice management automation',
            ],
            [
                'category'  => 'news-updates',
                'tags'      => ['cpa', 'accounting'],
                'title'     => 'VJ CPA CRM: What\'s New in September 2026',
                'slug'      => 'whats-new-september-2026',
                'excerpt'   => 'This month\'s release brings e-signature improvements, a new deadline heatmap view, faster document uploads, and several quality-of-life fixes.',
                'body'      => $this->bodyChangelog(),
                'image'     => null,
                'published' => $now->copy()->subDays(1),
                'meta_title'=> "VJ CPA CRM — What's New September 2026",
                'meta_desc' => "September 2026 product update: e-signatures, deadline heatmap, faster document uploads, and quality-of-life improvements.",
                'keywords'  => 'VJ CPA CRM update, CPA software release notes, September 2026',
            ],
        ];

        foreach ($posts as $data) {
            $post = BlogPost::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'uuid'              => (string) Str::uuid(),
                    'blog_category_id'  => $catMap[$data['category']] ?? null,
                    'author_id'         => $author?->id,
                    'title'             => $data['title'],
                    'slug'              => $data['slug'],
                    'excerpt'           => $data['excerpt'],
                    'body'              => $data['body'],
                    'featured_image'    => $data['image'],
                    'meta_title'        => $data['meta_title'],
                    'meta_description'  => $data['meta_desc'],
                    'meta_keywords'     => $data['keywords'],
                    'robots'            => 'index,follow',
                    'status'            => 'published',
                    'published_at'      => $data['published'],
                    'created_at'        => $data['published'],
                    'updated_at'        => $now,
                ],
            );

            // Attach tags
            $tagIds = array_filter(
                array_map(fn ($t) => $tagMap[$t] ?? null, $data['tags']),
            );
            $post->tags()->syncWithoutDetaching($tagIds);
        }

        $this->command->info('Blog seeded: 6 categories, ' . count($tagNames) . ' tags, ' . count($posts) . ' posts.');
    }

    // ── Article body content ──────────────────────────────────────────────────

    private function body10Ways(): string
    {
        return <<<HTML
<p>Running an accounting practice in 2026 means juggling more complexity than ever — growing client expectations, tighter deadlines, and a talent market that demands efficient systems. The firms winning right now share one thing in common: they run on repeatable processes, not heroic individual effort.</p>

<h2>1. Centralise your client data</h2>
<p>If your team is pulling client information from spreadsheets, email threads, and post-it notes, you are creating risk. A single client record — with contacts, entities, tax IDs, engagement history, and filed returns — eliminates the "who knows what" problem.</p>

<h2>2. Automate your deadline engine</h2>
<p>Manual deadline tracking in spreadsheets fails because someone has to update it. A filing engine that automatically calculates deadlines from filing type, fiscal year-end, and jurisdiction rules means nothing falls through the cracks.</p>

<h2>3. Implement a document request system</h2>
<p>Instead of chasing clients by email for every T4 slip, use a structured document request checklist. Clients get a clear list of what is needed, and your team can see at a glance what is missing.</p>

<h2>4. Build workflow templates for every engagement type</h2>
<p>T1 personal returns, corporate year-ends, and GST/HST filings all follow the same steps every time. Templatize them. Every new engagement triggers the same task sequence, assigned to the right team members automatically.</p>

<h2>5. Move to a client portal</h2>
<p>Secure client portals eliminate email as the primary communication channel. Clients upload documents, sign engagement letters, view invoice status, and receive filing updates — all without calling your office.</p>

<h2>6. Track time and utilisation properly</h2>
<p>You cannot improve what you do not measure. Time tracking by engagement reveals which clients are most and least profitable, and which team members are at capacity before it is too late to act.</p>

<h2>7. Standardise your billing cycle</h2>
<p>Firms that bill consistently at the same point in every engagement collect faster and dispute less. Attach invoice generation to workflow completion, not to the accountant remembering to send it.</p>

<h2>8. Use capacity planning, not intuition</h2>
<p>Over-promising and under-delivering damages client relationships. A capacity dashboard showing each team member's current workload versus available hours lets you make smarter assignment decisions.</p>

<h2>9. Automate client reminders</h2>
<p>Set up automated reminder sequences for document requests, upcoming filing deadlines, and unpaid invoices. Your team should spend time on accounting work, not chasing responses.</p>

<h2>10. Review your practice metrics monthly</h2>
<p>Filing completion rate, average invoice age, client retention, and utilisation per team member are the four KPIs that predict whether your practice is healthy or heading for trouble. Review them every month, not at year-end.</p>

<p>Implementing all ten at once is ambitious. Start with the two or three that directly address your biggest current pain points — most firms find that centralising client data and automating their deadline engine delivers the fastest return.</p>
HTML;
    }

    private function bodyDeadlines(): string
    {
        return <<<HTML
<p>Missing a CRA filing deadline is not just a compliance problem — it exposes your clients to late-filing penalties, interest charges, and in some cases, loss of election rights that cannot be reinstated. For CPA firms, a missed deadline can mean lost clients and reputational damage that takes years to repair.</p>

<p>Below is a summary of the major Canadian tax filing deadlines for 2026. Always verify exact dates with CRA, as holiday adjustments may shift specific due dates.</p>

<h2>Personal Income Tax (T1)</h2>
<ul>
<li><strong>April 30, 2026</strong> — T1 filing deadline for most individuals</li>
<li><strong>June 15, 2026</strong> — T1 filing deadline for self-employed individuals and their spouses (note: balance owing still due April 30)</li>
</ul>

<h2>Corporate Income Tax (T2)</h2>
<ul>
<li><strong>6 months after fiscal year-end</strong> — T2 filing deadline</li>
<li><strong>2 months after fiscal year-end</strong> — Corporate tax balance due (3 months for eligible CCPCs)</li>
</ul>

<h2>Trust Returns (T3)</h2>
<ul>
<li><strong>90 days after year-end</strong> — T3 filing deadline</li>
</ul>

<h2>GST/HST Returns</h2>
<ul>
<li><strong>Monthly filers</strong> — Due the last day of the following month</li>
<li><strong>Quarterly filers</strong> — Due the last day of the month following the quarter</li>
<li><strong>Annual filers (corporations)</strong> — Due 3 months after fiscal year-end</li>
<li><strong>Annual filers (individuals)</strong> — June 15 for self-employed</li>
</ul>

<h2>Payroll Remittances</h2>
<ul>
<li><strong>Regular remitters</strong> — 15th of the following month</li>
<li><strong>Accelerated Tier 1</strong> — 25th of the current month and 10th of the following month</li>
<li><strong>Accelerated Tier 2</strong> — 3rd, 10th, 18th, and 25th of each month</li>
</ul>

<h2>T4, T4A, T5 Information Returns</h2>
<ul>
<li><strong>February 28, 2026</strong> — Deadline for all information slips</li>
</ul>

<p>Managing these deadlines manually across dozens of clients is where most filing errors occur. An automated deadline engine that calculates each client's specific dates based on their fiscal year-end and filing frequency eliminates the spreadsheet — and the risk that comes with it.</p>
HTML;
    }

    private function bodyCloud(): string
    {
        return <<<HTML
<p>Five years ago, most CPA firms were running their practice on a combination of desktop accounting software, shared drives, and email. Today, the fastest-growing practices in Canada have moved their entire workflow to the cloud — and the gap between them and their peers is widening.</p>

<h2>What is driving the shift?</h2>
<p>The pandemic accelerated remote work across every industry, but accounting felt it particularly acutely. When your team cannot physically access the office server, client files, or the shared spreadsheet that tracks filing deadlines, the cracks in a desktop-based workflow become crises overnight.</p>

<p>Cloud practice management solves that structural problem. Every team member accesses the same real-time data from any device, anywhere. Client records, filing statuses, document libraries, and billing histories are always up to date and always accessible.</p>

<h2>The ROI data is compelling</h2>
<p>Firms that have moved to cloud-based practice management report, on average:</p>
<ul>
<li>30–40% reduction in time spent on administrative tasks</li>
<li>60% fewer missed filing deadlines in the first year</li>
<li>25% improvement in invoice collection speed</li>
<li>Significant improvement in staff retention (less frustration with broken processes)</li>
</ul>

<h2>What to look for in a cloud practice management platform</h2>
<p>Not all cloud tools are created equal. The key features that differentiate best-in-class platforms:</p>
<ul>
<li><strong>Purpose-built for accounting</strong> — Generic project management tools retrofitted for CPA work create more friction, not less</li>
<li><strong>Filing engine with jurisdiction rules</strong> — Should calculate deadlines automatically based on filing type and fiscal year-end</li>
<li><strong>Native client portal</strong> — Reduces email overload and improves the client experience dramatically</li>
<li><strong>Multi-tenant security</strong> — Each firm's data must be completely isolated</li>
<li><strong>Canadian compliance</strong> — Must understand CRA requirements, Canadian tax forms, and provincial rules</li>
</ul>

<p>The migration process is simpler than most firms expect. Most platforms offer data import tools, and the configuration work — setting up clients, filing types, and team assignments — typically takes less than two weeks for practices with up to 200 clients.</p>
HTML;
    }

    private function bodyPortal(): string
    {
        return <<<HTML
<p>Ask any CPA what their biggest time sink is, and the answer is almost always the same: chasing clients for documents and information. A single T1 season can generate hundreds of follow-up emails per client — reminders for slips, requests for receipts, confirmations of information. Multiply that by 100 clients and you have a significant portion of your firm's capacity consumed by internal logistics rather than accounting work.</p>

<h2>What a client portal actually does</h2>
<p>A client portal is a secure, branded online space where your clients can:</p>
<ul>
<li>Upload documents and files directly (no more emailing sensitive tax slips)</li>
<li>Review and sign engagement letters and proposals</li>
<li>See the status of their filings in real time</li>
<li>View and pay invoices</li>
<li>Send and receive messages securely (replacing unencrypted email for sensitive discussions)</li>
<li>Book appointments</li>
</ul>

<h2>The numbers that matter</h2>
<p>Firms using an integrated client portal consistently report:</p>
<ul>
<li>40–60% reduction in inbound client phone calls</li>
<li>Faster document collection (average 3 days vs. 10+ days by email)</li>
<li>Higher client satisfaction scores</li>
<li>Significantly fewer data entry errors (documents go directly into the system)</li>
</ul>

<h2>What makes a portal actually get used</h2>
<p>Most client portal failures are adoption failures, not technology failures. The portals that achieve high usage share these characteristics:</p>
<ul>
<li><strong>Simple onboarding</strong> — Clients can access it without creating a new password or downloading an app</li>
<li><strong>Branded experience</strong> — It looks like your firm, not a generic SaaS tool</li>
<li><strong>Clear document requests</strong> — Rather than a blank upload screen, clients see exactly what is needed for their specific filing</li>
<li><strong>Mobile-friendly</strong> — Many clients will complete document uploads from their phone</li>
<li><strong>Notification emails</strong> — Automated reminders that link directly to the portal</li>
</ul>

<p>The transition from email-based communication to portal-based communication typically takes one filing season. After that, most clients prefer it — and your team will too.</p>
HTML;
    }

    private function bodyWorkflow(): string
    {
        return <<<HTML
<p>The most common reason CPA practices plateau — despite excellent accountants and loyal clients — is that growth requires proportional headcount growth. Every new client means more work, which means more hiring. But hiring faster than your systems can absorb creates quality problems, not solutions.</p>

<p>The way out of that trap is workflow systematisation. When every engagement type follows the same documented, automated sequence, you can take on more clients without a corresponding increase in admin overhead.</p>

<h2>What a workflow template looks like in practice</h2>
<p>A T1 personal return workflow template might include:</p>
<ol>
<li>Client onboarding task: collect personal information update form</li>
<li>Document request: T4 slips, RRSP receipts, charitable donation receipts, medical receipts</li>
<li>Accountant task: prepare T1 return draft</li>
<li>Review task: senior review and sign-off</li>
<li>Client approval: send draft for client review via portal</li>
<li>E-signature: obtain T183 authorization</li>
<li>Filing task: submit to CRA electronically</li>
<li>Client confirmation: send filing confirmation and copy of return</li>
<li>Billing task: generate and send invoice</li>
</ol>

<p>Every task has a responsible team member, a due date calculated relative to the filing deadline, and a checklist of specific actions. Nothing is left to memory or judgment about what comes next.</p>

<h2>The compounding benefit</h2>
<p>A new team member who joins your firm in February — right before T1 season — can be productive within days rather than weeks, because the workflow tells them exactly what to do next. There is no institutional knowledge locked in the heads of senior staff.</p>

<p>And when something goes wrong (a client file is incomplete, a CRA notice arrives), the workflow history shows exactly where the engagement stands and who did what — making resolution much faster.</p>

<h2>Where to start</h2>
<p>Pick your highest-volume engagement type and document it end-to-end before touching any software. Map every task, who does it, and what triggers the next step. That document becomes your first workflow template. Build the rest from there.</p>
HTML;
    }

    private function bodyChangelog(): string
    {
        return <<<HTML
<p>We shipped a focused release this month with improvements to three areas that our users told us mattered most: e-signatures, deadline visibility, and document upload performance.</p>

<h2>E-Signature improvements</h2>
<ul>
<li><strong>Multi-signer ordering</strong> — You can now specify the exact signing order for documents that require multiple signatories (e.g., both spouses on a joint T183)</li>
<li><strong>Signed certificate download</strong> — Completed signature packages now include a detailed audit certificate showing who signed, when, and from what IP address</li>
<li><strong>Decline to sign</strong> — Signers can now decline with a reason, which triggers an automatic task for the assigned accountant</li>
</ul>

<h2>Deadline heatmap view</h2>
<p>The deadline calendar now includes a heatmap mode that shows filing concentration by day. At a glance, you can see which weeks are your heaviest and plan staffing accordingly. The heatmap filters by accountant, filing type, and client group.</p>

<h2>Faster document uploads</h2>
<p>We rewrote the document upload pipeline to use chunked multipart uploads. Files over 10MB now upload significantly faster, and large PDF batches (common at T1 season) no longer time out on slower connections.</p>

<h2>Other fixes</h2>
<ul>
<li>Fixed: Recurring invoice dates were off by one day in certain timezone configurations</li>
<li>Fixed: Work queue filter was not persisting correctly after a page refresh</li>
<li>Fixed: Tax profile copy function was not copying all jurisdiction settings</li>
<li>Improved: Audit log export now includes the full change diff, not just the event type</li>
</ul>

<p>As always, if you run into anything unexpected, reach out via the in-app chat or email support@cpacrm.com. Our team responds within one business day.</p>
HTML;
    }
}
