<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\ContactRequest;
use App\Http\Requests\Web\StoreDemoRequestRequest;
use App\Mail\ContactFormMail;
use App\Mail\DemoRequestMail;
use App\Services\Seo\SeoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class MarketingController extends Controller
{
    public function home(): Response
    {
        return Inertia::render('Marketing/Home', [
            'seo' => SeoService::make('home'),
        ]);
    }

    public function features(): Response
    {
        return Inertia::render('Marketing/Features', [
            'seo' => SeoService::make('features'),
        ]);
    }

    public function pricing(): Response
    {
        return Inertia::render('Marketing/Pricing', [
            'seo' => SeoService::make('pricing'),
        ]);
    }

    public function about(): Response
    {
        return Inertia::render('Marketing/About', [
            'seo' => SeoService::make('about'),
        ]);
    }

    public function contact(): Response
    {
        return Inertia::render('Marketing/Contact', [
            'seo'    => SeoService::make('contact'),
            'status' => session('contact_status'),
        ]);
    }

    public function submitContact(ContactRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Send to support email
        try {
            Mail::to(config('cpa.support_email', 'support@cpacrm.com'))
                ->queue(new ContactFormMail(
                    name:    $data['name'],
                    email:   $data['email'],
                    subject: $data['subject'],
                    message: $data['message'],
                    company: $data['company'] ?? null,
                ));
        } catch (\Throwable $e) {
            logger()->error('Contact form mail failed', ['error' => $e->getMessage()]);
        }

        return back()->with('contact_status', 'sent');
    }

    public function privacy(): Response
    {
        return Inertia::render('Marketing/Privacy', [
            'seo' => SeoService::make('privacy'),
        ]);
    }

    public function terms(): Response
    {
        return Inertia::render('Marketing/Terms', [
            'seo' => SeoService::make('terms'),
        ]);
    }

    public function security(): Response
    {
        return Inertia::render('Marketing/Security', [
            'seo' => SeoService::make('security'),
        ]);
    }

    public function demo(): Response
    {
        return Inertia::render('Marketing/Demo', [
            'seo'    => SeoService::make('demo'),
            'status' => session('demo_status'),
        ]);
    }

    public function demoRequest(StoreDemoRequestRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            Mail::to(config('cpa.support_email', 'support@cpacrm.com'))
                ->queue(new DemoRequestMail(
                    name:     $data['name'],
                    email:    $data['email'],
                    company:  $data['company'],
                    teamSize: $data['size'] ?? null,
                ));
        } catch (\Throwable $e) {
            logger()->error('Demo request mail failed', ['error' => $e->getMessage()]);
        }

        return back()->with('demo_status', 'sent');
    }
}
