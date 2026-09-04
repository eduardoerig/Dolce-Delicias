---
name: tailwind
description: >
  Implement production-grade Tailwind CSS UIs for Rails apps. Covers Tailwind v4
  (@theme, CSS-first) and v3, design tokens, dark mode, ViewComponent/partial
  patterns, Stimulus for interactivity (no React), and curated component-library
  sources (HyperUI, Flowbite, TailGrids, TW Elements, Material Tailwind, DaisyUI,
  Preline, Meraki, Headless UI). Includes compliance audit. Use when: "tailwind",
  "tailwindcss", "utility classes", "dark mode", "responsive", "landing page",
  "dashboard", "component library", "daisyui", "flowbite", "hyperui", "shadcn"
  (redirect to non-React alternatives).
user-invokable: true
argument-hint: "[audit] [target path or URL]"
---

# Tailwind CSS (Rails)

This skill guides implementation of Tailwind CSS UIs inside Rails apps. The
stack assumed: **Rails + tailwindcss-rails + Propshaft + Stimulus + Turbo**.
No React, no JSX — ERB templates, ViewComponents or partials, Stimulus
controllers for interactivity.

## Philosophy

- **Utility-first** — compose UIs from primitives; extract to ViewComponents/partials only when a pattern repeats 3+ times (rule of three).
- **Token-driven** — all colors/spacing/typography flow from `@theme` (v4) or `tailwind.config.js` (v3). Never hardcode hex outside the theme.
- **Copy, don't depend** — prefer copy-paste blocks from component libraries (HyperUI, Flowbite Blocks, etc.) over runtime UI dependencies. The app owns its markup.
- **Accessible by default** — every interactive pattern has focus-visible states, ARIA, keyboard handling. Use Headless UI patterns (behavior, not styles) as the reference for complex widgets.
- **Dark mode is not optional** — every component ships light + dark from day one.

## When to use this skill vs others

| Situation | Use |
|-----------|-----|
| Implementing Tailwind in a Rails app (ERB, ViewComponent, Stimulus) | **this skill** |
| Standalone single-file HTML demo / mockup with DaisyUI | `ui` skill |
| Interactive config playground (sliders → live preview → prompt output) | `playground` skill |
| Distinctive aesthetic direction (bold, editorial, experimental) | `frontend-design` skill drives visuals; this skill drives implementation |
| Material Design spec adherence (Compose, Flutter, MD3 tokens) | `material-3` skill |

**With `frontend-design`:** it picks the aesthetic, this skill enforces token hygiene, accessibility, and Rails patterns. Tailwind rules win for class structure; frontend-design wins for visual choices within those constraints.

**With `ui`:** that skill targets one-file HTML with Tailwind CDN + DaisyUI. If the target is a Rails app, prefer this skill — CDN builds and inline markup don't survive the asset pipeline.

## Decision Tree

**What are you building?**

```
Landing page section        → references/component-libraries.md § Which library for which need?
Dashboard / app shell       → references/component-libraries.md § Which library for which need?
Form                        → references/component-patterns.md § Forms
Modal / dropdown / tabs     → references/component-patterns.md § Modals, § Dropdown Menu, § Tabs
Single component from spec  → references/component-patterns.md
Theme setup                 → references/tokens-and-theme.md
Rails integration / setup   → references/rails-integration.md
Compliance check            → Run audit (see § Tailwind Audit below)
```

**Tailwind version?**

```
v4 (recommended, 2024+)     → CSS-first: @theme { } in app/assets/tailwind/application.css
v3 (legacy)                 → JS config: tailwind.config.js with theme.extend
Rails 7.1+ default          → tailwindcss-rails ships v4 on new installs since mid-2024
Unsure                      → Check Gemfile.lock for tailwindcss-ruby version
```

**Source of the component?**

```
Marketing/landing           → HyperUI (v4, MIT) or Flowbite Blocks (free tier)
App shell / dashboard       → TailGrids or Preline UI
Material look               → Material Tailwind or Material Minimal
Animated, modern            → Aceternity UI or Sera UI (port JSX → ERB)
Behavior primitives         → Headless UI patterns (port React → Stimulus)
Quick theme-able kit        → DaisyUI (plugin, not copy-paste; all-or-nothing — don't mix with other systems)
Nothing fits                → Build from scratch per references/component-patterns.md
```

## Library Shortlist

80% of Rails needs come from these five. Reach for the full catalog only when they don't fit.

1. **HyperUI** — marketing, e-commerce, simple app UI (MIT, plain HTML, v4-ready)
2. **Flowbite Blocks** — dashboards, pricing, complex SaaS sections (free tier + pro)
3. **Preline UI** — large coherent kit when you need dozens of components (MIT, v4)
4. **DaisyUI** — plugin-based, themed primitives (`btn`, `card`); trade utility purity for speed
5. **Headless UI** — behavior reference for every interactive widget; port React → Stimulus

Full catalog (20+ libraries), license notes, and WebFetch recipes: `references/component-libraries.md`

## Workflow: Copy a Block

1. **Identify the need** — "hero with CTA", "pricing table", "sidebar shell".
2. **Pick the library** from the matrix based on aesthetic and project fit.
3. **Fetch the page** — `WebFetch(url, "extract the HTML for the {component name} block")`. Ask for the exact markup, not a description.
4. **Port to ERB** — replace hardcoded copy with `<%= %>`, extract repeating parts into locals, wrap in a ViewComponent or partial if reused.
5. **Reconcile theme** — swap library-specific utilities (e.g., DaisyUI `btn-primary`, Flowbite blue-600) for your own theme tokens. Never ship a block that ignores your `@theme`.
6. **Add Stimulus** for interactivity — never ship raw `onclick` or vanilla JS. See `references/rails-integration.md`.
7. **Test dark mode** — every copied block must work in both modes. If the source only shows light, add dark variants.

## Tailwind v4 Theme (Load-bearing snippet)

`app/assets/tailwind/application.css`:

```css
@import "tailwindcss";

@theme {
  --color-brand-50:  oklch(0.97 0.02 250);
  --color-brand-500: oklch(0.62 0.18 250);
  --color-brand-900: oklch(0.28 0.10 250);

  --font-sans:    "Inter Variable", ui-sans-serif, system-ui, sans-serif;
  --font-display: "Fraunces", ui-serif, Georgia, serif;

  --radius-card: 1rem;

  --breakpoint-3xl: 120rem;
}

@custom-variant dark (&:where(.dark, .dark *));
```

- `oklch()` for colors — perceptually uniform, handles dark mode gracefully.
- `@custom-variant dark` uses a `.dark` class on `<html>` (class strategy, not media query — the toggle is user-controlled).
- Scales (`--color-brand-50` … `900`) generate utilities `bg-brand-500`, `text-brand-900`, etc.

Full theming guide: `references/tokens-and-theme.md`

## Dark Mode (Load-bearing snippet)

Class strategy. Critical: the `dark` class must be set **before first paint** to avoid a white flash. That means an inline `<script>` in `<head>` — before Tailwind loads — not a Stimulus `connect()` callback (which runs after paint).

```erb
<!-- app/views/layouts/application.html.erb -->
<html lang="en" data-controller="theme">
  <head>
    <script>
      (function() {
        var stored = localStorage.getItem("theme")
        var prefersDark = matchMedia("(prefers-color-scheme: dark)").matches
        if (stored === "dark" || (!stored && prefersDark)) {
          document.documentElement.classList.add("dark")
        }
      })()
    </script>
    <%= stylesheet_link_tag "tailwind", "data-turbo-track": "reload" %>
  </head>
  <body class="bg-white text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100">
    <button type="button" data-action="theme#toggle" aria-label="Toggle theme">
      <span class="hidden dark:inline">☀</span>
      <span class="dark:hidden">☾</span>
    </button>
    <%= yield %>
  </body>
</html>
```

The Stimulus controller only handles the toggle click; initial state is set before Stimulus boots. Full implementation (toggle controller, CSP notes): `references/rails-integration.md` § Dark Mode Toggle.

## Component Conventions

When building from scratch (no library fit), every component must:

1. **Respect tokens** — use `bg-brand-500`, not `bg-[#3b82f6]`.
2. **Ship both modes** — every color utility pairs with a `dark:` counterpart.
3. **Handle all interactive states** — `hover:`, `focus-visible:`, `active:`, `disabled:`, `aria-[invalid]:`.
4. **Be keyboard navigable** — visible `focus-visible:ring-2` on every interactive element.
5. **Meet contrast** — WCAG AA: 4.5:1 text, 3:1 large text / UI borders.
6. **Scale responsively** — mobile-first; add `sm: md: lg:` breakpoints deliberately, not reflexively.

From-scratch patterns: `references/component-patterns.md`

## Common Patterns

### App Shell (Rails + Stimulus)

```erb
<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950">
  <aside class="fixed inset-y-0 left-0 w-64 border-r border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hidden md:block">
    <%= render "shared/sidebar" %>
  </aside>
  <div class="md:pl-64">
    <header class="sticky top-0 z-10 border-b border-zinc-200 dark:border-zinc-800 bg-white/80 dark:bg-zinc-900/80 backdrop-blur">
      <%= render "shared/top_bar" %>
    </header>
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
      <%= yield %>
    </main>
  </div>
</div>
```

### Card Grid

```erb
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
  <% @items.each do |item| %>
    <article class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 shadow-sm transition hover:shadow-md">
      <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100"><%= item.title %></h3>
      <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400"><%= item.summary %></p>
      <%= link_to "Open", item, class: "mt-4 inline-flex items-center gap-1 text-sm font-medium text-brand-600 hover:text-brand-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 rounded" %>
    </article>
  <% end %>
</div>
```

### Button

Canonical ViewComponent with variants (primary/secondary/ghost/danger) and sizes: `references/rails-integration.md` § ViewComponent.

More patterns: `references/component-patterns.md`, `references/layout-and-responsive.md`

## Anti-Patterns

**Never do these:**

- **Hardcode colors outside `@theme`** — no `bg-[#3b82f6]`, no inline `style="color: #333"`. Add a token.
- **Overuse `@apply`** — `@apply` is a crutch that erases utility-first benefits. Use it only for genuinely global resets (e.g., `.prose` tweaks). Prefer a ViewComponent.
- **Arbitrary values for everything** — `w-[237px]` every few lines means your scale is wrong. Use scale values (`w-60`) or extend `--spacing-*`.
- **Ship without dark mode** — every committed component works in light + dark, or it isn't done.
- **Ignore `focus-visible:`** — removing default focus rings without replacing them is an accessibility bug.
- **Mix DaisyUI theme classes with raw utilities for the same concern** — pick one per component. Don't `<button class="btn btn-primary bg-red-500">`.
- **Ship React/JSX blocks verbatim** — copy-paste from React libraries means porting. Resolve className→class, inline arrow-handlers→Stimulus actions, conditional rendering→ERB.
- **Use JS libraries for things CSS already does** — modals, dropdowns, tabs all have Tailwind-only patterns. Reach for Stimulus only for state.
- **Stretch content to full width on wide screens** — constrain with `max-w-7xl mx-auto` or container queries.
- **Skip responsive review** — test at 375, 768, 1024, 1440 before marking done.

Full list: `references/anti-patterns.md`

## Tailwind Audit

When invoked with `audit` (e.g., `/tailwind audit`), or asked to review Tailwind compliance, inspect the target and score each category 0–10.

### Audit Procedure

1. **Identify the target** — URL (browser/devtools), file paths (read ERB + CSS), or running Rails app (hit the route, inspect the HTML).
2. **Inspect** each category:

| Category | What to check |
|----------|--------------|
| **Theme tokens** | `@theme` block exists (v4) or `theme.extend` (v3); no hardcoded hex in ERB/CSS outside the theme; brand colors have full scales |
| **Dark mode** | Every interactive page works in `.dark`; every text/bg utility pairs with `dark:`; theme toggle uses class strategy |
| **Accessibility** | `focus-visible:ring-*` on all interactive; contrast ratios meet WCAG AA (4.5:1 text, 3:1 UI); ARIA on modals/menus/tabs; keyboard navigation |
| **Responsive** | Mobile-first; tested at 375/768/1024/1440; no horizontal scroll on mobile; text legible on narrow viewports |
| **Component extraction** | Repeating class strings (3+ occurrences) extracted to ViewComponent/partial; no copy-paste soup |
| **Utility hygiene** | No `@apply` abuse; no arbitrary-value soup (`w-[237px]`); no inline `style=`; consistent scale use |
| **Interactivity** | Stimulus for stateful widgets (not `onclick`); Turbo-compatible; no vanilla `addEventListener` in views |
| **Layout** | `max-w-*` constraints on wide screens; grid/flex used idiomatically; no `float:` |
| **Asset pipeline** | `stylesheet_link_tag "tailwind"` present; tailwindcss-rails `watch:css` running in dev; purge/content paths include all ERB + JS |
| **Library hygiene** | If using DaisyUI/Flowbite, consistent use; no mixing of theme systems; copied blocks retokened to match `@theme` |

3. **Generate the report:**

```
# Tailwind Compliance Audit

Target: [URL or file path]
Date: [date]
Tailwind version: [v4 / v3]
Overall Score: [X/100]

## Scores by Category
| Category             | Score | Status |
|----------------------|-------|--------|
| Theme tokens         | X/10  | [pass/warn/fail] |
| Dark mode            | X/10  | [pass/warn/fail] |
| Accessibility        | X/10  | [pass/warn/fail] |
| Responsive           | X/10  | [pass/warn/fail] |
| Component extraction | X/10  | [pass/warn/fail] |
| Utility hygiene      | X/10  | [pass/warn/fail] |
| Interactivity        | X/10  | [pass/warn/fail] |
| Layout               | X/10  | [pass/warn/fail] |
| Asset pipeline       | X/10  | [pass/warn/fail] |
| Library hygiene      | X/10  | [pass/warn/fail] |

## Critical Issues
[Score 0–3 items with file:line and fixes]

## Warnings
[Score 4–6 items with recommendations]

## Passing
[Score 7–10 items with notes]

## Recommended Fixes (Priority Order)
1. [Most impactful first]
```

### Audit Methods

**Live URL**: inspect computed styles, check CSS variables in devtools, resize viewport, check `prefers-color-scheme` both states.

**Source**: read ERB + `app/assets/tailwind/application.css`; grep for hardcoded colors and arbitrary values.

**Quick greps** (adapt to project root — these are screeners, not oracles; false positives expected):

```
# Hardcoded hex in ERB and CSS
grep -rn '#[0-9a-fA-F]\{3,8\}\b' app/views app/components app/assets --include='*.erb' --include='*.css' --include='*.html'

# Arbitrary-value abuse — all bracket utilities
grep -rnE '\b(w|h|p|m|text|bg|border|gap|rounded)-\[[^]]+\]' app/views app/components

# Missing dark variants (crude: flags *lines* not *elements*; review each hit)
grep -rn 'class=".*bg-\(white\|zinc-50\|gray-50\)' app/views app/components | grep -v 'dark:bg-'

# Inline styles
grep -rn 'style="' app/views app/components

# @apply usage outside global resets
grep -rn '@apply' app/assets

# onclick / vanilla JS in views
grep -rnE 'on(click|change|submit|input|focus|blur)=' app/views app/components

# focus:outline-none without focus-visible:ring replacement
grep -rn 'focus:outline-none' app/views app/components | grep -v 'focus-visible:ring'
```

**Dark-mode grep caveats**: grep matches lines, not elements. `<div class="bg-white text-zinc-900">` followed on the same line by `<span class="dark:bg-zinc-900">` reads as "has dark:", but the div still lacks it. For a real audit, walk the ERB or render the page and diff computed `background-color` in dark mode.

**Catch invalid class names**: run the Tailwind CLI build with `--content` broadened and watch for warnings. Hook into CI:

```
npx tailwindcss -i app/assets/tailwind/application.css -o /tmp/tw.css 2>&1 | grep -iE 'warn|unknown' && exit 1 || true
```

### Scoring Guide

- **9–10**: Fully compliant; tokens clean, dark mode complete, accessible
- **7–8**: Mostly compliant; minor issues (stray hex, a few missing `dark:`)
- **4–6**: Partial; noticeable gaps in dark mode or accessibility
- **1–3**: Major violations; no theme discipline, broken in dark mode
- **0**: Absent

Status: **pass** (7+), **warn** (4–6), **fail** (0–3)

## Reference Documents

- `references/tokens-and-theme.md` — v4 `@theme`, v3 config, tokens, dark mode, CSS variables
- `references/component-libraries.md` — library catalog, URLs, WebFetch recipes, porting notes
- `references/component-patterns.md` — buttons, forms, cards, modals, nav (from scratch)
- `references/layout-and-responsive.md` — breakpoints, container queries, grids, page shells
- `references/rails-integration.md` — tailwindcss-rails, ViewComponent, Stimulus, Turbo, dark-mode toggle
- `references/anti-patterns.md` — hardcoded colors, `@apply` abuse, missing `dark:`, className soup
