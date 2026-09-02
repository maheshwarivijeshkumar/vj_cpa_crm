---
inclusion: always
---

# CPA CRM — Brand Design System & Color Palette

> This palette is the **primary brand system**. Never substitute arbitrary colors. Every UI element must trace back to a token defined here.

---

## Color Palette

### Primary Teal Scale

| Token | Hex | Usage |
|-------|-----|-------|
| `--cpa-very-light` | `#E6F5F4` | Page tint backgrounds, hover fills, subtle badges |
| `--cpa-light` | `#C5E8E5` | Hover states, tag backgrounds, progress fills |
| `--cpa-medium-light` | `#8CD3CF` | Secondary accents, divider highlights, chart fills |
| `--cpa-medium` | `#48BCB9` | Interactive elements, focus rings, step indicators |
| `--cpa-medium-dark` | `#1D9792` | Primary CTA buttons, links, active nav items |
| `--cpa-dark` | `#055E5A` | Sidebar background, section headers, dark buttons |
| `--cpa-very-dark` | `#023E3C` | Deep accents, active sidebar item, hover on dark |
| `--cpa-text-secondary` | `#4D7374` | Muted text, helper text, secondary labels |

### Neutrals

| Token | Hex | Usage |
|-------|-----|-------|
| `--cpa-bg` | `#F4FAFA` | Page / app background |
| `--cpa-white` | `#FEFDFD` | Card backgrounds, modals, drawers |
| `--cpa-border` | `#D4ECEA` | Borders, dividers, table lines |
| `--cpa-text-primary` | `#0D2B2A` | Primary body text, headings |
| `--cpa-text-muted` | `#6B9294` | Placeholder text, captions |

### Semantic Status Colors

| Token | Hex | Usage |
|-------|-----|-------|
| `--cpa-success` | `#16A34A` | Success states, paid badges, completed status |
| `--cpa-success-bg` | `#DCFCE7` | Success badge background |
| `--cpa-warning` | `#D97706` | Warning states, pending review, approaching deadline |
| `--cpa-warning-bg` | `#FEF3C7` | Warning badge background |
| `--cpa-danger` | `#DC2626` | Error states, overdue, destructive actions |
| `--cpa-danger-bg` | `#FEE2E2` | Error badge background |
| `--cpa-info` | `#0EA5E9` | Informational states, notes |
| `--cpa-info-bg` | `#E0F2FE` | Info badge background |

---

## Tailwind CSS 4 Configuration

Add to `resources/css/app.css`:

```css
@import "tailwindcss";

@theme {
  /* CPA Brand Teal Scale */
  --color-cpa-very-light:   #E6F5F4;
  --color-cpa-light:        #C5E8E5;
  --color-cpa-medium-light: #8CD3CF;
  --color-cpa-medium:       #48BCB9;
  --color-cpa-medium-dark:  #1D9792;
  --color-cpa-dark:         #055E5A;
  --color-cpa-very-dark:    #023E3C;
  --color-cpa-text-secondary: #4D7374;

  /* CPA Neutrals */
  --color-cpa-bg:           #F4FAFA;
  --color-cpa-white:        #FEFDFD;
  --color-cpa-border:       #D4ECEA;
  --color-cpa-text-primary: #0D2B2A;
  --color-cpa-text-muted:   #6B9294;

  /* Semantic */
  --color-cpa-success:      #16A34A;
  --color-cpa-success-bg:   #DCFCE7;
  --color-cpa-warning:      #D97706;
  --color-cpa-warning-bg:   #FEF3C7;
  --color-cpa-danger:       #DC2626;
  --color-cpa-danger-bg:    #FEE2E2;
  --color-cpa-info:         #0EA5E9;
  --color-cpa-info-bg:      #E0F2FE;

  /* Typography */
  --font-sans: 'Inter', system-ui, -apple-system, sans-serif;
  --font-mono: 'JetBrains Mono', 'Fira Code', monospace;
}
```

### Tailwind Usage Examples

```html
<!-- Primary CTA button -->
<button class="bg-cpa-medium-dark hover:bg-cpa-dark text-white">
  Save Client
</button>

<!-- Page background -->
<div class="bg-cpa-bg min-h-screen">

<!-- Card -->
<div class="bg-cpa-white border border-cpa-border rounded-xl shadow-sm">

<!-- Sidebar -->
<nav class="bg-cpa-dark text-white">

<!-- Active nav item -->
<a class="bg-cpa-very-dark text-white">

<!-- Muted text -->
<p class="text-cpa-text-muted text-sm">

<!-- Success badge -->
<span class="bg-cpa-success-bg text-cpa-success text-xs font-medium px-2 py-0.5 rounded-full">
  Paid
</span>

<!-- Warning badge -->
<span class="bg-cpa-warning-bg text-cpa-warning text-xs font-medium px-2 py-0.5 rounded-full">
  Pending
</span>

<!-- Danger badge -->
<span class="bg-cpa-danger-bg text-cpa-danger text-xs font-medium px-2 py-0.5 rounded-full">
  Overdue
</span>
```

---

## Typography

### Font

**Inter** (Google Fonts) — only font used for all UI text.

```html
<!-- In app layout head -->
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link
  href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700,800,900&display=swap"
  rel="stylesheet"
/>
```

### Type Scale

| Role | Size | Weight | Tailwind Classes |
|------|------|--------|-----------------|
| Page Title | 30–32px | 600 | `text-3xl font-semibold text-cpa-text-primary` |
| Section Heading | 20–24px | 600 | `text-xl font-semibold text-cpa-text-primary` |
| Card Heading | 16–18px | 600 | `text-base font-semibold text-cpa-text-primary` |
| Body Text | 14–16px | 400 | `text-sm text-cpa-text-primary` |
| Label | 13–14px | 500 | `text-sm font-medium text-cpa-text-primary` |
| Helper / Caption | 12–13px | 400 | `text-xs text-cpa-text-muted` |
| Table Cell | 14px | 400 | `text-sm text-cpa-text-primary` |
| Table Header | 12px | 600 | `text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide` |

---

## Icons

**Library:** Lucide Vue — the only icon library used in this project. Do not mix others.

```typescript
// Import examples
import { Users, FileText, TrendingUp, ChevronRight } from 'lucide-vue-next'
```

### Icon Sizes

| Context | Size | Tailwind |
|---------|------|---------|
| Table row actions | 16px | `size-4` |
| Inputs / Buttons | 18px | `size-[18px]` |
| Navigation items | 20px | `size-5` |
| Card icons | 24px | `size-6` |
| Dashboard stat highlights | 28–32px | `size-7` or `size-8` |

### Rules

- Consistent stroke width: `stroke-[1.5]` (default Lucide)
- Never use icons purely for decoration without purpose
- Always pair standalone icon buttons with `aria-label`
- Color icons with text color tokens — never arbitrary hex

---

## Component Design Tokens

### Buttons

| Variant | Classes |
|---------|---------|
| Primary | `bg-cpa-medium-dark hover:bg-cpa-dark text-white font-medium rounded-lg px-4 py-2 text-sm transition-colors` |
| Secondary | `bg-cpa-very-light hover:bg-cpa-light text-cpa-dark font-medium rounded-lg px-4 py-2 text-sm border border-cpa-border transition-colors` |
| Outline | `border border-cpa-border hover:border-cpa-medium text-cpa-text-primary hover:text-cpa-dark rounded-lg px-4 py-2 text-sm transition-colors` |
| Ghost | `hover:bg-cpa-very-light text-cpa-text-secondary hover:text-cpa-dark rounded-lg px-4 py-2 text-sm transition-colors` |
| Danger | `bg-cpa-danger hover:bg-red-700 text-white font-medium rounded-lg px-4 py-2 text-sm transition-colors` |
| Danger Ghost | `hover:bg-cpa-danger-bg text-cpa-danger rounded-lg px-4 py-2 text-sm transition-colors` |

### Inputs

```html
<!-- Default state -->
<input class="
  w-full rounded-lg border border-cpa-border bg-cpa-white
  px-3 py-2 text-sm text-cpa-text-primary
  placeholder:text-cpa-text-muted
  focus:outline-none focus:ring-2 focus:ring-cpa-medium focus:border-cpa-medium
  transition-colors
" />

<!-- Error state -->
<input class="
  w-full rounded-lg border border-cpa-danger bg-cpa-white
  px-3 py-2 text-sm text-cpa-text-primary
  placeholder:text-cpa-danger
  focus:outline-none focus:ring-2 focus:ring-cpa-danger focus:border-cpa-danger
" aria-invalid="true" />
```

### Cards

```html
<div class="bg-cpa-white border border-cpa-border rounded-xl shadow-sm p-6">
```

### Sidebar

```html
<!-- Sidebar shell -->
<aside class="bg-cpa-dark w-64 min-h-screen flex flex-col">

<!-- Nav item — inactive -->
<a class="flex items-center gap-3 px-4 py-2.5 text-sm text-white/70 hover:text-white hover:bg-cpa-very-dark rounded-lg mx-2 transition-colors">

<!-- Nav item — active -->
<a class="flex items-center gap-3 px-4 py-2.5 text-sm text-white bg-cpa-very-dark rounded-lg mx-2">

<!-- Section label -->
<p class="px-4 py-2 text-xs font-semibold text-white/40 uppercase tracking-wider">
```

### Data Table

```html
<!-- Table wrapper -->
<div class="bg-cpa-white border border-cpa-border rounded-xl overflow-hidden shadow-sm">

<!-- Table head -->
<thead class="bg-cpa-bg border-b border-cpa-border">
<th class="px-4 py-3 text-left text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">

<!-- Table row -->
<tr class="border-b border-cpa-border hover:bg-cpa-very-light transition-colors">
<td class="px-4 py-3 text-sm text-cpa-text-primary">
```

### Stat Cards (Dashboard)

```html
<div class="bg-cpa-white border border-cpa-border rounded-xl p-6 shadow-sm">
  <div class="flex items-center justify-between">
    <div>
      <p class="text-xs font-semibold text-cpa-text-secondary uppercase tracking-wide">
        Active Clients
      </p>
      <p class="mt-1 text-3xl font-bold text-cpa-text-primary">247</p>
      <p class="mt-1 text-xs text-cpa-success flex items-center gap-1">
        <TrendingUp class="size-3" /> +12 this month
      </p>
    </div>
    <div class="bg-cpa-very-light rounded-xl p-3">
      <Users class="size-6 text-cpa-medium-dark" />
    </div>
  </div>
</div>
```

---

## Status Badge System

Map filing/engagement/task statuses to badge colors:

```typescript
// lib/statusColors.ts
export const statusBadgeClasses: Record<string, string> = {
  // Green — complete / paid / active
  active:     'bg-cpa-success-bg text-cpa-success',
  paid:       'bg-cpa-success-bg text-cpa-success',
  completed:  'bg-cpa-success-bg text-cpa-success',
  accepted:   'bg-cpa-success-bg text-cpa-success',
  submitted:  'bg-cpa-success-bg text-cpa-success',

  // Blue/Teal — in progress
  draft:          'bg-cpa-very-light text-cpa-dark',
  in_progress:    'bg-cpa-light text-cpa-dark',
  under_review:   'bg-cpa-light text-cpa-dark',

  // Yellow — pending / waiting
  pending:        'bg-cpa-warning-bg text-cpa-warning',
  sent:           'bg-cpa-warning-bg text-cpa-warning',
  waiting:        'bg-cpa-warning-bg text-cpa-warning',
  partially_paid: 'bg-cpa-warning-bg text-cpa-warning',

  // Red — overdue / failed / rejected
  overdue:   'bg-cpa-danger-bg text-cpa-danger',
  rejected:  'bg-cpa-danger-bg text-cpa-danger',
  failed:    'bg-cpa-danger-bg text-cpa-danger',
  cancelled: 'bg-cpa-danger-bg text-cpa-danger',
  voided:    'bg-cpa-danger-bg text-cpa-danger',

  // Gray — archived / inactive
  archived:  'bg-gray-100 text-gray-500',
  inactive:  'bg-gray-100 text-gray-500',
}
```

```vue
<!-- StatusBadge.vue -->
<template>
  <span
    :class="[
      'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
      statusBadgeClasses[status] ?? 'bg-gray-100 text-gray-500'
    ]"
  >
    {{ label }}
  </span>
</template>
```

---

## Layout Spacing

Use 4px / 8px base rhythm consistently:

| Token | Value | Tailwind |
|-------|-------|---------|
| XS | 4px | `gap-1`, `p-1` |
| SM | 8px | `gap-2`, `p-2` |
| MD | 16px | `gap-4`, `p-4` |
| LG | 24px | `gap-6`, `p-6` |
| XL | 32px | `gap-8`, `p-8` |
| 2XL | 48px | `gap-12`, `p-12` |

Page-level padding: `px-6 py-6` (desktop), `px-4 py-4` (mobile).

---

## Design Personality Rules

### Always

- Clean whitespace — let content breathe
- Consistent border radius: `rounded-lg` (8px) for inputs/buttons, `rounded-xl` (12px) for cards/panels
- Subtle shadows: `shadow-sm` on cards, `shadow-md` on modals/drawers
- Smooth transitions: `transition-colors duration-150` on interactive elements
- High contrast text on all backgrounds (WCAG AA minimum 4.5:1)

### Never

- Neon or oversaturated colors
- Heavy drop shadows
- Excessive gradients (gradient only as subtle background accent, never on text or key UI)
- Over-rounded "bubble" or cartoon-style components
- Decorative animations — motion only where it improves orientation or feedback
- Mixed icon styles from different libraries
- Arbitrary hex colors not in the palette

---

## Accessibility Requirements

- All text: minimum 4.5:1 contrast ratio against background
- Large text (18px+ / 14px+ bold): minimum 3:1 contrast
- Focus rings: `focus:ring-2 focus:ring-cpa-medium` — always visible
- Error states: never communicate by color alone — also use icon + message text
- Reduced motion: `motion-reduce:transition-none` on animated elements
- All interactive icons: `aria-label` when no visible text present
- Form fields: always have an associated `<label>` (not placeholder-only)

---

## Chart Palette (Recharts / Chart.js)

Use the teal scale as the primary data series palette:

```typescript
export const chartColors = {
  primary:   '#1D9792', // cpa-medium-dark
  secondary: '#48BCB9', // cpa-medium
  tertiary:  '#8CD3CF', // cpa-medium-light
  accent:    '#C5E8E5', // cpa-light
  success:   '#16A34A',
  warning:   '#D97706',
  danger:    '#DC2626',
  grid:      '#D4ECEA', // cpa-border
  text:      '#4D7374', // cpa-text-secondary
}
```

Charts must:
- Have clear axis labels and tooltips
- Use `chartColors.grid` for gridlines
- Use `chartColors.text` for axis tick labels
- Support responsive sizing (`ResponsiveContainer` / `maintainAspectRatio: false`)
- Avoid visual overload — max 4–5 series per chart
