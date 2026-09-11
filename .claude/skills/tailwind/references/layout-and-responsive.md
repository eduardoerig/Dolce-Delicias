# Layout & Responsive

## Breakpoints

Tailwind defaults (mobile-first — unprefixed = all sizes, prefixed = min-width):

| Prefix | Min-width | Target |
|--------|-----------|--------|
| (none) | 0 | Mobile portrait |
| `sm:`  | 640px | Mobile landscape / small tablet |
| `md:`  | 768px | Tablet |
| `lg:`  | 1024px | Laptop |
| `xl:`  | 1280px | Desktop |
| `2xl:` | 1536px | Large desktop |
| `3xl:` | 1920px | Add via `@theme { --breakpoint-3xl: 120rem; }` (v4) |

**Mobile-first discipline**: write the mobile layout first, then add breakpoints. Never `lg:grid-cols-3 grid-cols-1` — write `grid-cols-1 lg:grid-cols-3`.

## The Container Constraint

Content stretched to a 4K monitor is unreadable. Every top-level content area needs a max width:

```erb
<main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
  <%= yield %>
</main>
```

| Width | Token | Use |
|-------|-------|-----|
| `max-w-prose` | ~65ch | Long-form article body |
| `max-w-3xl` | 48rem | Narrow content (blog post) |
| `max-w-5xl` | 64rem | Marketing section, single-column form |
| `max-w-7xl` | 80rem | Dashboard / app shell |
| `max-w-screen-2xl` | 1536px | Max reasonable app width |

Padding scales with viewport: `px-4 sm:px-6 lg:px-8` is the idiomatic pattern.

## Grid Patterns

### Responsive card grid

```erb
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
  <!-- cards -->
</div>
```

### Auto-fit (no breakpoints, content-aware)

```erb
<div class="grid gap-4" style="grid-template-columns: repeat(auto-fit, minmax(16rem, 1fr));">
  <!-- cards; fits as many ~256px columns as possible -->
</div>
```

Or in v4 with arbitrary value (once, ok):

```erb
<div class="grid grid-cols-[repeat(auto-fit,minmax(16rem,1fr))] gap-4">
```

### Sidebar + content

```erb
<div class="grid gap-8 lg:grid-cols-[18rem_1fr]">
  <aside>…</aside>
  <main>…</main>
</div>
```

Mobile: single column. `lg:` and up: 288px sidebar + flexible content.

### Asymmetric hero

```erb
<section class="grid gap-8 lg:grid-cols-12 lg:gap-12">
  <div class="lg:col-span-7">
    <h1 class="text-4xl font-semibold sm:text-5xl lg:text-6xl">Headline</h1>
    <p class="mt-6 text-lg text-zinc-600 dark:text-zinc-400">Subhead.</p>
  </div>
  <div class="lg:col-span-5">
    <%= image_tag "hero.png", class: "w-full rounded-2xl" %>
  </div>
</section>
```

## Container Queries (v4)

Container queries are built into v4 — style based on a parent's size, not the viewport:

```erb
<div class="@container">
  <article class="flex flex-col gap-4 @md:flex-row @lg:gap-6">
    <img class="w-full @md:w-48">
    <div class="flex-1">…</div>
  </article>
</div>
```

`@container` registers the element as a query container. `@md:` (medium = 28rem container width), `@lg:`, etc. apply when the container hits that width.

**Use when**: the same component lives in wide and narrow regions (e.g., a card in a sidebar and in a main grid). Responsive breakpoints are viewport-wide; container queries are local.

## Common Page Shells

### Marketing / landing

```
┌─────────────────────────────────────┐
│ Top bar (sticky, blur on scroll)    │
├─────────────────────────────────────┤
│                                     │
│ Hero (max-w-7xl, asymmetric 7/5)    │
│                                     │
├─────────────────────────────────────┤
│ Feature grid (3 cols → 1 on mobile) │
├─────────────────────────────────────┤
│ Testimonial strip                   │
├─────────────────────────────────────┤
│ Pricing (3 tiers, middle featured)  │
├─────────────────────────────────────┤
│ CTA banner                          │
├─────────────────────────────────────┤
│ Footer (4 link cols → 2 → 1)        │
└─────────────────────────────────────┘
```

```erb
<div class="min-h-screen bg-white dark:bg-zinc-950">
  <%= render "shared/top_bar" %>
  <main>
    <%= render "marketing/hero" %>
    <%= render "marketing/features" %>
    <%= render "marketing/testimonials" %>
    <%= render "marketing/pricing" %>
    <%= render "marketing/cta" %>
  </main>
  <%= render "shared/footer" %>
</div>
```

### Dashboard (sidebar + top bar)

```
┌──────────┬──────────────────────────┐
│          │ Top bar                  │
│ Sidebar  ├──────────────────────────┤
│ (256px)  │                          │
│          │ Content                  │
│          │ (max-w-7xl, px-4…lg:px-8)│
│          │                          │
└──────────┴──────────────────────────┘
```

```erb
<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950">
  <%= render "shared/sidebar" %>  <!-- fixed, hidden md:flex -->
  <div class="md:pl-64">
    <%= render "shared/top_bar" %>
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
      <%= yield %>
    </main>
  </div>
</div>
```

On mobile (<768px), hide the fixed sidebar and show a drawer via hamburger menu. See `component-patterns.md` § Mobile menu.

### Settings page (left nav + form)

```erb
<div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
  <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">Settings</h1>
  <div class="mt-6 grid gap-8 lg:grid-cols-[14rem_1fr]">
    <nav class="space-y-1">
      <%= link_to "Profile",  profile_path,  class: "…" %>
      <%= link_to "Billing",  billing_path,  class: "…" %>
      <%= link_to "Security", security_path, class: "…" %>
    </nav>
    <section class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
      <%= yield %>
    </section>
  </div>
</div>
```

### Auth (centered narrow)

```erb
<div class="flex min-h-screen items-center justify-center bg-zinc-50 px-4 dark:bg-zinc-950">
  <div class="w-full max-w-md rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
    <h1 class="text-center text-2xl font-semibold text-zinc-900 dark:text-zinc-100">Sign in</h1>
    <%= render "form" %>
  </div>
</div>
```

## Sticky Headers & Backdrop

```erb
<header class="sticky top-0 z-20 border-b border-zinc-200/80 bg-white/80 backdrop-blur dark:border-zinc-800/80 dark:bg-zinc-950/80">
  …
</header>
```

`backdrop-blur` + semi-transparent bg = the "Apple-style" sticky header. Make sure the `z-*` is high enough to overlap below content.

## Stacking Contexts (z-index)

Use a small, consistent scale — don't invent `z-9999`:

| Layer | Token |
|-------|-------|
| Base content | (none) |
| Sticky nav, sidebar | `z-10` |
| Dropdown, popover | `z-20` |
| Tooltip | `z-30` |
| Modal backdrop | `z-40` |
| Modal content, toast | `z-50` |

If you need more, you've likely built nested modal soup — rethink.

## Aspect ratios

Use `aspect-*` instead of manual padding-hack:

```erb
<div class="aspect-video">  <!-- 16/9 -->
  <iframe class="h-full w-full">…</iframe>
</div>
<img class="aspect-square w-full object-cover">  <!-- 1/1 -->
<div class="aspect-[4/3]">…</div>  <!-- custom once, ok -->
```

## Overflow & Scroll

Long content needs scroll affordances:

```erb
<!-- horizontal scroll (carousel, tab bar) -->
<div class="flex gap-4 overflow-x-auto px-4 [scrollbar-width:thin]">
  <!-- items; each shrink-0 so they don't squish -->
  <article class="w-64 shrink-0">…</article>
</div>

<!-- scrollable list with max height -->
<div class="max-h-96 overflow-y-auto rounded-lg border border-zinc-200 dark:border-zinc-800">
  <!-- items -->
</div>
```

## Responsive Review Checklist

Before marking a layout done, resize to each and verify:

- **375px** (iPhone SE): no horizontal scroll, text readable, tap targets 44px+
- **768px** (iPad portrait): layout shifts from single → two-column
- **1024px** (laptop): sidebar visible, content not stretched
- **1440px** (desktop): `max-w-*` engaged, comfortable line length
- **Dark mode** at each: contrast holds, no invisible elements

Bonus checks:
- Zoom text to 200% — content still usable (accessibility)
- `prefers-reduced-motion` — animations respected (`motion-safe:` / `motion-reduce:` variants)
