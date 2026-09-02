<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Menu, X, ArrowRight } from '@lucide/vue'

const page     = usePage()
const menuOpen = ref(false)

const currentUrl = computed(() => page.url)

function isActive(href: string): boolean {
    if (href === '/') return currentUrl.value === '/'
    return currentUrl.value.startsWith(href)
}

// ── Desktop nav links (left of CTA) ──────────────────────────────────────────
const navLinks = [
    { href: '/',         label: 'Home'     },
    { href: '/features', label: 'Features' },
    { href: '/pricing',  label: 'Pricing'  },
    { href: '/demo',     label: 'Demo'     },
    { href: '/about',    label: 'About'    },
    { href: '/contact',  label: 'Contact'  },
]

const year = new Date().getFullYear()
</script>

<template>
    <div class="flex flex-col min-h-screen bg-white">

        <!-- ── Navbar ──────────────────────────────────────────────────────── -->
        <header class="mkt-nav">
            <div class="mkt-nav-inner">

                <!-- Logo -->
                <Link href="/" class="mkt-logo" aria-label="VJ CPA CRM home">
                    <div class="mkt-logo-mark">
                        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <rect x="9" y="3" width="6" height="4" rx="1" stroke="currentColor" stroke-width="2"/>
                            <path d="M9 12h6M9 16h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <span class="mkt-logo-name">VJ CPA CRM</span>
                </Link>

                <!-- Desktop nav links -->
                <nav class="mkt-nav-links" aria-label="Main navigation">
                    <Link
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        class="mkt-nav-link"
                        :class="{ active: isActive(link.href) }"
                        :aria-current="isActive(link.href) ? 'page' : undefined"
                    >
                        {{ link.label }}
                    </Link>
                </nav>

                <!-- Desktop CTAs -->
                <div class="mkt-cta">
                    <Link href="/login" class="mkt-btn-ghost">Sign in</Link>
                    <Link href="/register" class="mkt-btn-primary">
                        Start free trial
                        <ArrowRight :size="13" aria-hidden="true" />
                    </Link>
                </div>

                <!-- Mobile hamburger -->
                <button
                    class="mkt-hamburger"
                    :aria-expanded="menuOpen"
                    aria-label="Toggle navigation menu"
                    @click="menuOpen = !menuOpen"
                >
                    <component :is="menuOpen ? X : Menu" :size="20" aria-hidden="true" />
                </button>
            </div>

            <!-- Mobile menu -->
            <Transition
                enter-active-class="transition duration-150 ease-out"
                enter-from-class="opacity-0 -translate-y-1"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="menuOpen" class="mkt-mobile-menu">
                    <Link
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        class="mkt-mobile-link"
                        :class="{ active: isActive(link.href) }"
                        @click="menuOpen = false"
                    >
                        {{ link.label }}
                    </Link>
                    <div class="border-t border-gray-100 mt-2 pt-3 flex flex-col gap-2 px-4 pb-4">
                        <Link href="/login"    class="mkt-btn-ghost w-full justify-center" @click="menuOpen = false">Sign in</Link>
                        <Link href="/register" class="mkt-btn-primary w-full justify-center" @click="menuOpen = false">Start free trial</Link>
                    </div>
                </div>
            </Transition>
        </header>

        <!-- ── Page content ────────────────────────────────────────────────── -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- ── Footer ──────────────────────────────────────────────────────── -->
        <footer class="mkt-footer" aria-label="Site footer">
            <!-- Main footer grid -->
            <div class="mkt-footer-inner">

                <!-- Brand column -->
                <div class="mkt-footer-brand">
                    <Link href="/" class="mkt-logo mb-4" aria-label="VJ CPA CRM home">
                        <div class="mkt-logo-mark" style="background:#1D9792">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" stroke="white" stroke-width="2" stroke-linecap="round"/>
                                <rect x="9" y="3" width="6" height="4" rx="1" stroke="white" stroke-width="2"/>
                                <path d="M9 12h6M9 16h4" stroke="white" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <span style="color:#fff;font-size:15px;font-weight:700;letter-spacing:-.3px;">VJ CPA CRM</span>
                    </Link>
                    <p class="mkt-footer-tagline">
                        The modern practice management platform built for CPA firms across Canada and beyond.
                    </p>

                    <!-- Social icons -->
                    <div class="mkt-social-row" aria-label="Social media links">
                        <!-- Facebook -->
                        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="mkt-social-icon" aria-label="Facebook">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                            </svg>
                        </a>
                        <!-- LinkedIn -->
                        <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer" class="mkt-social-icon" aria-label="LinkedIn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
                                <rect x="2" y="9" width="4" height="12"/>
                                <circle cx="4" cy="4" r="2"/>
                            </svg>
                        </a>
                        <!-- Instagram -->
                        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="mkt-social-icon" aria-label="Instagram">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                            </svg>
                        </a>
                        <!-- Twitter / X -->
                        <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" class="mkt-social-icon" aria-label="Twitter / X">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Link columns -->
                <div class="mkt-footer-links">
                    <div class="mkt-footer-col">
                        <h3 class="mkt-footer-col-title">Product</h3>
                        <Link href="/features" class="mkt-footer-link">Features</Link>
                        <Link href="/pricing"  class="mkt-footer-link">Pricing</Link>
                        <Link href="/demo"     class="mkt-footer-link">Demo</Link>
                        <Link href="/register" class="mkt-footer-link">Start free trial</Link>
                        <Link href="/login"    class="mkt-footer-link">Sign in</Link>
                    </div>

                    <div class="mkt-footer-col">
                        <h3 class="mkt-footer-col-title">Company</h3>
                        <Link href="/about"   class="mkt-footer-link">About</Link>
                        <Link href="/blog"    class="mkt-footer-link">Blog</Link>
                        <Link href="/contact" class="mkt-footer-link">Contact</Link>
                    </div>

                    <div class="mkt-footer-col">
                        <h3 class="mkt-footer-col-title">Legal</h3>
                        <Link href="/privacy"  class="mkt-footer-link">Privacy Policy</Link>
                        <Link href="/terms"    class="mkt-footer-link">Terms of Service</Link>
                        <Link href="/security" class="mkt-footer-link">Security</Link>
                    </div>

                    <div class="mkt-footer-col">
                        <h3 class="mkt-footer-col-title">Contact</h3>
                        <a href="mailto:support@cpacrm.com" class="mkt-footer-link">support@cpacrm.com</a>
                        <a href="tel:+14165550100"           class="mkt-footer-link">+1 (416) 555-0100</a>
                        <p class="mkt-footer-address">Brampton, ON, Canada</p>
                    </div>
                </div>
            </div>

            <!-- Copyright row — full width, separate from grid -->
            <div class="mkt-footer-copyright">
                <p>© {{ year }} VJ CPA CRM. All rights reserved. A product of YOO Technologies Inc.</p>
                <div class="mkt-footer-copyright-links">
                    <Link href="/privacy"  class="mkt-footer-copyright-link">Privacy</Link>
                    <span aria-hidden="true">·</span>
                    <Link href="/terms"    class="mkt-footer-copyright-link">Terms</Link>
                    <span aria-hidden="true">·</span>
                    <Link href="/security" class="mkt-footer-copyright-link">Security</Link>
                </div>
            </div>
        </footer>

    </div>
</template>

<style scoped>
/* ── Navbar ─────────────────────────────────────────────────────────────────── */
.mkt-nav {
    position: sticky;
    top: 0;
    z-index: 50;
    background: rgba(255,255,255,0.97);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid #f0fafa;
    box-shadow: 0 1px 3px rgba(2,62,60,.04);
}
.mkt-nav-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
    height: 64px;
    display: flex;
    align-items: center;
    gap: 0;
}

/* Logo */
.mkt-logo {
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    flex-shrink: 0;
}
.mkt-logo-mark {
    width: 34px;
    height: 34px;
    background: #055E5A;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    flex-shrink: 0;
}
.mkt-logo-name {
    font-size: 15px;
    font-weight: 700;
    color: #0D2B2A;
    letter-spacing: -0.3px;
    white-space: nowrap;
}

/* Nav links — bottom-border active style (not background pill) */
.mkt-nav-links {
    display: flex;
    align-items: stretch;
    height: 64px;
    gap: 0;
    margin-left: 1.75rem;
    flex: 1;
}
.mkt-nav-link {
    display: inline-flex;
    align-items: center;
    padding: 0 12px;
    font-size: 13.5px;
    font-weight: 500;
    color: #4B5563;
    text-decoration: none;
    border-bottom: 2px solid transparent;
    transition: color .12s, border-color .12s;
    white-space: nowrap;
}
.mkt-nav-link:hover {
    color: #1D9792;
}
.mkt-nav-link.active {
    color: #1D9792;
    font-weight: 600;
    border-bottom-color: #1D9792;
}

/* CTA area */
.mkt-cta {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-left: auto;
    flex-shrink: 0;
}
.mkt-btn-ghost {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 7px 14px;
    border-radius: 8px;
    font-size: 13.5px;
    font-weight: 500;
    color: #374151;
    text-decoration: none;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: background .12s, color .12s;
}
.mkt-btn-ghost:hover { background: #F3F4F6; color: #055E5A; }

.mkt-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13.5px;
    font-weight: 600;
    color: #fff;
    background: #1D9792;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: background .12s;
    white-space: nowrap;
}
.mkt-btn-primary:hover { background: #055E5A; }

/* Hamburger */
.mkt-hamburger {
    display: none;
    margin-left: auto;
    width: 36px;
    height: 36px;
    align-items: center;
    justify-content: center;
    border: none;
    background: transparent;
    border-radius: 7px;
    cursor: pointer;
    color: #374151;
    transition: background .12s;
}
.mkt-hamburger:hover { background: #F3F4F6; }

/* Mobile menu */
.mkt-mobile-menu {
    display: flex;
    flex-direction: column;
    gap: 2px;
    padding: 8px 12px 0;
    border-top: 1px solid #F0FAF9;
    background: #fff;
}
.mkt-mobile-link {
    padding: 10px 12px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    text-decoration: none;
    transition: background .1s, color .1s;
}
.mkt-mobile-link:hover,
.mkt-mobile-link.active {
    background: #E6F5F4;
    color: #055E5A;
}
.mkt-mobile-link.active { font-weight: 600; }

@media (max-width: 768px) {
    .mkt-nav-links, .mkt-cta { display: none; }
    .mkt-hamburger { display: flex; }
}

/* ── Footer ─────────────────────────────────────────────────────────────────── */
.mkt-footer {
    background: #0D2B2A;
}

.mkt-footer-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 3.5rem 1.5rem 2.5rem;
    display: grid;
    grid-template-columns: 1fr 2.2fr;
    gap: 3rem;
}

/* Brand */
.mkt-footer-brand { display: flex; flex-direction: column; }
.mkt-footer-tagline {
    font-size: 13.5px;
    color: rgba(255,255,255,.5);
    line-height: 1.65;
    max-width: 280px;
    margin-bottom: 1.25rem;
}

/* Social row */
.mkt-social-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: auto;
}
.mkt-social-icon {
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    background: rgba(255,255,255,.07);
    color: rgba(255,255,255,.55);
    text-decoration: none;
    transition: background .12s, color .12s;
}
.mkt-social-icon:hover {
    background: rgba(255,255,255,.14);
    color: #fff;
}

/* Link columns */
.mkt-footer-links {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 2rem;
}
.mkt-footer-col {
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.mkt-footer-col-title {
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: rgba(255,255,255,.38);
    margin-bottom: 6px;
}
.mkt-footer-link {
    font-size: 13.5px;
    color: rgba(255,255,255,.62);
    text-decoration: none;
    transition: color .12s;
    padding: 1.5px 0;
}
.mkt-footer-link:hover { color: #fff; }
.mkt-footer-address {
    font-size: 12px;
    color: rgba(255,255,255,.35);
    margin-top: 6px;
    line-height: 1.5;
}

/* Copyright row — full-width, bottom of footer */
.mkt-footer-copyright {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1.25rem 1.5rem;
    border-top: 1px solid rgba(255,255,255,.07);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: .75rem;
}
.mkt-footer-copyright p {
    font-size: 12px;
    color: rgba(255,255,255,.3);
}
.mkt-footer-copyright-links {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: rgba(255,255,255,.25);
}
.mkt-footer-copyright-link {
    color: rgba(255,255,255,.4);
    text-decoration: none;
    transition: color .12s;
}
.mkt-footer-copyright-link:hover { color: rgba(255,255,255,.75); }

/* Responsive */
@media (max-width: 900px) {
    .mkt-footer-inner {
        grid-template-columns: 1fr;
        padding-bottom: 2rem;
    }
    .mkt-footer-links { grid-template-columns: repeat(2, 1fr); }
    .mkt-footer-copyright { flex-direction: column; text-align: center; gap: .5rem; }
}
@media (max-width: 480px) {
    .mkt-footer-links { grid-template-columns: 1fr 1fr; }
}
</style>
