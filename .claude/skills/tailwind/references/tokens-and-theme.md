# Tokens & Theme

Everything visual flows from tokens. No hardcoded hex, spacing, or font outside this layer.

## Tailwind v4 (recommended)

v4 is CSS-first: the theme lives in your stylesheet via `@theme`, not in JS config.

### File layout (Rails)

```
app/assets/tailwind/application.css
```

```css
@import "tailwindcss";

@theme {
  /* Colors (use oklch for perceptual uniformity and easier dark-mode pairs) */
  --color-brand-50:  oklch(0.97 0.02 250);
  --color-brand-100: oklch(0.93 0.05 250);
  --color-brand-200: oklch(0.87 0.09 250);
  --color-brand-300: oklch(0.79 0.13 250);
  --color-brand-400: oklch(0.70 0.16 250);
  --color-brand-500: oklch(0.62 0.18 250);
  --color-brand-600: oklch(0.54 0.17 250);
  --color-brand-700: oklch(0.45 0.15 250);
  --color-brand-800: oklch(0.37 0.12 250);
  --color-brand-900: oklch(0.28 0.10 250);
  --color-brand-950: oklch(0.18 0.07 250);

  /* Fonts */
  --font-sans:    "Inter Variable", ui-sans-serif, system-ui, sans-serif;
  --font-display: "Fraunces", ui-serif, Georgia, serif;
  --font-mono:    "JetBrains Mono", ui-monospace, monospace;

  /* Radii */
  --radius-card:   1rem;
  --radius-field:  0.5rem;
  --radius-button: 0.5rem;

  /* Shadows (subtle; dark mode uses ring utilities instead) */
  --shadow-card: 0 1px 2px 0 rgb(0 0 0 / 0.05), 0 1px 3px 0 rgb(0 0 0 / 0.1);

  /* Breakpoints (add only if you need non-default) */
  --breakpoint-3xl: 120rem;

  /* Spacing (extend default scale; don't replace) */
  --spacing-18: 4.5rem;
  --spacing-112: 28rem;
  --spacing-128: 32rem;
}

/* Dark mode: class strategy (user-controlled toggle) */
@custom-variant dark (&:where(.dark, .dark *));
```

### What each generates

- `--color-brand-500` → utilities `bg-brand-500`, `text-brand-500`, `border-brand-500`, `ring-brand-500`, `from-brand-500` (gradients), etc.
- `--font-sans` → `font-sans` utility + sets the default sans stack
- `--radius-card` → `rounded-card` utility
- `--shadow-card` → `shadow-card` utility
- `--spacing-18` → `p-18`, `m-18`, `w-18`, `h-18`, `gap-18`, etc.

### Choosing colors: use a tool

Generate full scales (50–950) instead of eyeballing:
- **UI Colors** — https://uicolors.app/create
- **Tailwind Color Generator** — https://www.tints.dev/
- **Radix Colors** — https://www.radix-ui.com/colors (copy OKLCH/HSL values, adapt to `@theme`)

Don't ship a theme with only `500` defined. You need at least `50, 500, 600, 900` for light/dark pairings.

### OKLCH vs HSL vs hex

- **OKLCH** (preferred for v4): perceptually uniform; `L` (lightness) maps intuitively to dark-mode lightening.
- **HSL**: acceptable; use if your design tokens come from a library that outputs HSL.
- **hex**: last resort; loses gamut info. Convert with https://oklch.com/.

## Tailwind v3 (legacy)

If the project is still on v3 (check `Gemfile.lock` for `tailwindcss-ruby` < 4.0):

`tailwind.config.js` (or in `config/tailwind.config.js` for tailwindcss-rails v3):

```js
module.exports = {
  content: [
    './public/*.html',
    './app/helpers/**/*.rb',
    './app/javascript/**/*.js',
    './app/views/**/*.{erb,haml,html,slim}',
    './app/components/**/*.{rb,erb,haml,html,slim}'
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        brand: {
          50:  '#f0f5ff',
          500: '#3b6fe3',
          600: '#2f5bc2',
          900: '#1a2d5e'
        }
      },
      fontFamily: {
        sans:    ['Inter Variable', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        display: ['Fraunces', 'ui-serif', 'Georgia', 'serif']
      },
      borderRadius: { card: '1rem' },
      boxShadow: { card: '0 1px 2px 0 rgb(0 0 0 / 0.05), 0 1px 3px 0 rgb(0 0 0 / 0.1)' }
    }
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography')
  ]
}
```

**Content paths matter**: Tailwind only generates classes it sees. Miss a path and classes get purged in production. Include `app/components/**` if using ViewComponent.

## Dark Mode

### Strategy: class (not media query)

Class strategy gives users a toggle. Media-query strategy locks them to the OS setting.

- **v4**: `@custom-variant dark (&:where(.dark, .dark *));`
- **v3**: `darkMode: 'class'` in config

**`@custom-variant` vs `@variant`**: `@custom-variant` *defines* a variant (use it once, in your theme). `@variant` *applies* one inside a CSS block — useful inside `@layer components` when you need `@variant dark { ... }` syntax instead of the `dark:` utility prefix. Most apps only need `@custom-variant`.

### Usage in markup

```erb
<div class="bg-white text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100">
  <h1 class="text-2xl font-semibold">Hello</h1>
  <p class="mt-2 text-zinc-600 dark:text-zinc-400">Subdued text.</p>
</div>
```

Every color utility needs a `dark:` counterpart, or the component is incomplete.

### Toggle (Stimulus)

See `rails-integration.md` § Dark Mode Toggle.

### Dark-mode color pairings (cheat sheet)

| Use | Light | Dark |
|-----|-------|------|
| Page bg | `bg-white` or `bg-zinc-50` | `dark:bg-zinc-950` |
| Card bg | `bg-white` | `dark:bg-zinc-900` |
| Raised surface | `bg-zinc-50` | `dark:bg-zinc-800` |
| Border | `border-zinc-200` | `dark:border-zinc-800` |
| Heading | `text-zinc-900` | `dark:text-zinc-100` |
| Body | `text-zinc-700` | `dark:text-zinc-300` |
| Muted | `text-zinc-500` | `dark:text-zinc-400` |
| Disabled | `text-zinc-400` | `dark:text-zinc-600` |
| Focus ring | `ring-brand-500` | `dark:ring-brand-400` |

Use `zinc` or `neutral` or `slate` — pick one neutral family for the app. Don't mix.

## CSS Variables (beyond Tailwind)

Some things don't fit tokens: dynamic user-chosen accent, per-theme per-component overrides. Use raw CSS custom properties layered on top of Tailwind:

```css
/* app/assets/tailwind/application.css */
@layer base {
  :root {
    --accent: var(--color-brand-600);
  }
  .dark {
    --accent: var(--color-brand-400);
  }
  [data-user-accent="emerald"] { --accent: var(--color-emerald-500); }
  [data-user-accent="rose"]    { --accent: var(--color-rose-500); }
}
```

Use in markup: `style="color: var(--accent)"` or extend the theme: `--color-accent: var(--accent);` → `bg-accent` utility.

## Typography (prose)

For long-form content (articles, blog posts), use `@tailwindcss/typography`:

```erb
<article class="prose prose-zinc dark:prose-invert max-w-none">
  <%= @article.body %>
</article>
```

Customize in theme:

```css
@theme {
  --prose-body: var(--color-zinc-700);
  --prose-headings: var(--color-zinc-900);
  --prose-links: var(--color-brand-600);
}
```

## Testing tokens

After changing `@theme`, verify in devtools:
1. Inspect any element — computed styles show `--color-brand-500: oklch(...)`.
2. Toggle `.dark` on `<html>` in devtools — all `dark:` variants flip.
3. Run `bin/rails tailwindcss:build` (or watch `watch:css`) — no build errors.
