# Anti-Patterns

Things that look fine in isolation and rot a codebase. Each has a why and a fix.

## 1. Hardcoded colors outside the theme

**Bad:**
```erb
<div class="bg-[#3b82f6] text-white">…</div>
<span style="color: #1f2937">…</span>
```

**Why bad:** Breaks dark mode, ignores the theme, can't be changed centrally, can't be audited.

**Fix:** Add the color to `@theme`, use the token.
```css
/* application.css */
@theme { --color-brand-500: oklch(0.62 0.18 250); }
```
```erb
<div class="bg-brand-500 text-white">…</div>
```

**Grep to find:**
```
grep -rn '#[0-9a-fA-F]\{3,8\}\b' app/views app/components app/assets --include='*.erb' --include='*.css'
grep -rn 'style="' app/views app/components
```

## 2. `@apply` overuse

**Bad:**
```css
.my-button {
  @apply bg-brand-600 text-white px-4 py-2 rounded-lg hover:bg-brand-700 focus-visible:ring-2 focus-visible:ring-brand-500 transition disabled:opacity-50;
}
```

**Why bad:** Defeats utility-first (you can't see state without opening CSS). Scattered abstractions. Hard to override. Pairs poorly with ViewComponent variants.

**Fix:** Extract a `ButtonComponent` (or partial) with classes inline. See `rails-integration.md` § ViewComponent.

**Acceptable `@apply`:** genuinely global resets — `.prose` tweaks, `html { @apply antialiased; }`, third-party overrides you can't reach otherwise. If you're writing `@apply` inside a class named after a component, stop and make a ViewComponent instead.

## 3. Arbitrary values everywhere

**Bad:**
```erb
<div class="w-[237px] h-[81px] mt-[13px] text-[#3b4252] text-[15.5px] p-[18px]">
```

**Why bad:** Your design scale exists for a reason. Every arbitrary value is an escape hatch — fine once, a pattern when constant. Produces inconsistent rhythm. Makes re-themeing impossible.

**Fix:** Map to scale values (`w-60` = 240px, `mt-3.5` = 14px). If you genuinely need a non-scale value often, extend the scale in `@theme`:
```css
@theme { --spacing-18: 4.5rem; --font-size-md-15: 0.96875rem; }
```

**Grep to find:**
```
grep -rnE '\b(w|h|p|m|text|bg|border|gap|rounded)-\[[^]]+\]' app/views app/components
```

Audit the output: some will be legit (exotic aspect ratios, one-off z-index), most will point to a missing scale extension or a screen-grab-from-Figma sloppiness.

## 4. Missing `dark:` variants

**Bad:**
```erb
<div class="bg-white text-zinc-900 border border-zinc-200 p-4">
  <h3 class="text-zinc-900">Title</h3>
  <p class="text-zinc-600">Body</p>
</div>
```

**Why bad:** Users in dark mode see white boxes glaring on the dark page. Half the site dark, half light = broken experience.

**Fix:** Every color utility pairs with a `dark:` counterpart.
```erb
<div class="bg-white text-zinc-900 border border-zinc-200 p-4 dark:bg-zinc-900 dark:text-zinc-100 dark:border-zinc-800">
  <h3 class="text-zinc-900 dark:text-zinc-100">Title</h3>
  <p class="text-zinc-600 dark:text-zinc-400">Body</p>
</div>
```

See `tokens-and-theme.md` § Dark-mode color pairings.

**Grep to find:**
```
# crude heuristic: text-zinc-900 without dark:
grep -rn 'text-zinc-900' app/views app/components | grep -v 'dark:text-zinc'
grep -rn 'bg-white' app/views app/components | grep -v 'dark:bg-'
```

## 5. Missing `focus-visible:`

**Bad:**
```erb
<button class="bg-brand-600 text-white hover:bg-brand-700 focus:outline-none">…</button>
```

**Why bad:** Keyboard users can't see which element has focus. Accessibility regression. You removed the default ring and replaced it with nothing.

**Fix:**
```erb
<button class="bg-brand-600 text-white hover:bg-brand-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-950">…</button>
```

**Grep to find:**
```
grep -rn 'focus:outline-none' app/views app/components | grep -v 'focus-visible:ring'
```

## 6. className soup / copy-paste proliferation

**Bad:** The same 150-character class string appearing on 12 buttons across the app.

**Why bad:** Change the design → find-and-replace across 12 files → miss two → inconsistency.

**Fix:** Rule of three. Third copy → ViewComponent.

**Grep to find common-structure duplicates:**
```
# very rough; adapt to patterns you notice
grep -rh 'inline-flex items-center justify-center rounded-lg' app/views app/components | sort | uniq -c | sort -rn | head
```

If a substring appears 10+ times, that's a component waiting to exist.

## 7. Inline `onclick` / vanilla JS in views

**Bad:**
```erb
<button onclick="document.getElementById('modal').showModal()">Open</button>
<script>document.querySelector('.toggle').addEventListener('click', () => …)</script>
```

**Why bad:** Turbo navigation breaks these. Event listeners leak. No lifecycle management. Can't test.

**Fix:** Stimulus.
```erb
<button data-action="click->dialog#open" data-dialog-id="modal">Open</button>
```

See `rails-integration.md` § Stimulus.

**Grep to find:**
```
grep -rnE 'on(click|change|submit|input|focus|blur|load)=' app/views app/components
grep -rn 'addEventListener' app/views
```

## 8. Non-mobile-first breakpoints

**Bad:**
```erb
<div class="lg:grid-cols-3 md:grid-cols-2 grid-cols-1 grid">
```

**Why bad:** Breakpoint utilities apply at min-width. Reading right-to-left makes layouts mentally confusing. CSS specificity is left-to-right, but cognitive load increases when written backwards.

**Fix:** Mobile-first, ascending.
```erb
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
```

## 9. Stretching content on wide screens

**Bad:**
```erb
<main>
  <article><!-- no max width --></article>
</main>
```

**Why bad:** 3000px text lines are unreadable. Users scanning horizontally lose their place.

**Fix:**
```erb
<main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
  <article class="prose max-w-3xl">…</article>
</main>
```

See `layout-and-responsive.md` § The Container Constraint.

## 10. Mixing themed systems

**Bad:**
```erb
<button class="btn btn-primary bg-red-500 hover:bg-red-600">Delete</button>
```

`btn btn-primary` is DaisyUI. Overriding with raw utilities fights the theme system and leaves an unpredictable result.

**Fix:** Pick one per component. Either all DaisyUI (`<button class="btn btn-error">`) or all utilities (`<button class="bg-red-600 hover:bg-red-700 …">`). App-wide, be consistent — don't use DaisyUI for some components and raw for others without a reason.

## 11. z-index inflation

**Bad:**
```erb
<div class="z-999">…</div>
<div class="z-[9999]">…</div>
<div class="z-[99999]">…</div>
```

**Why bad:** Each new modal layer racing higher. Stacking context bugs become invisible.

**Fix:** A small consistent scale.

| Layer | Token |
|-------|-------|
| Sticky nav, sidebar | `z-10` |
| Dropdown, popover | `z-20` |
| Tooltip | `z-30` |
| Modal backdrop | `z-40` |
| Modal content, toast | `z-50` |

Anything needing higher is a sign of nested modals — restructure. Keep this table in sync with `layout-and-responsive.md` § Stacking Contexts.

## 12. Tailwind classes that don't exist

**Bad:**
```erb
<div class="bg-brandd-500 text-center">  <!-- typo: brandd -->
```

**Why bad:** Silently drops the class. No error. Page looks wrong, nobody notices until QA.

**Fix:** Use the Tailwind VS Code extension (autocomplete + error highlighting). Run `tailwindcss:build` locally and scan for warnings. Configure ESLint `tailwindcss/no-unknown-class` if using JS tooling.

## 13. Ignoring reduced motion

**Bad:**
```erb
<div class="animate-bounce">…</div>  <!-- always bounces, even for users with prefers-reduced-motion -->
```

**Why bad:** Vestibular-disorder users get nauseated. Accessibility requirement.

**Fix:** `motion-safe:` variant.
```erb
<div class="motion-safe:animate-bounce">…</div>
```

Or ship reduced-motion fallbacks: `motion-reduce:animate-none`.

## 14. `tailwindcss-rails` content paths missing new directories

**Bad:** Added `app/admin/views/` or `app/frontend/components/`. Classes inside don't render.

**Why bad:** Tailwind purges unreferenced classes. In production, half your admin panel is unstyled.

**Fix:** v3 — add the glob to `content:` in `tailwind.config.js`. v4 — usually auto-detected, but add `@source "../../../path"` if missing.

## 15. Blocking on libraries for primitives

**Bad:** Installing a big UI library (`flowbite` JS package, `@preline/ui`) just to get one modal. Then the library adds 200KB and styles conflict with your tokens.

**Fix:** Copy the markup from the library's docs (MIT = free), own it. Port JS to Stimulus. You never need runtime UI dependencies in a Rails app — Stimulus does everything.

## 16. Flash of unstyled dark mode

**Bad:**
```erb
<html>
  <head><%= stylesheet_link_tag "tailwind" %></head>
  <body>
    <script>
      if (localStorage.getItem("theme") === "dark") document.documentElement.classList.add("dark")
    </script>
```

**Why bad:** Script runs after CSS loads but the `dark` class is set after first paint. Dark-mode users see a white flash on every navigation.

**Fix:** Move the class-setting script **before** the stylesheet, in `<head>`, as the very first thing. See `rails-integration.md` § Dark Mode Toggle.

## 17. Placeholders instead of labels

**Bad:**
```erb
<input type="email" placeholder="Email">  <!-- no label -->
```

**Why bad:** Placeholder disappears on input; screen readers may skip; low contrast by default; not accessible.

**Fix:** Always a `<label>`. Use `sr-only` to visually hide if the design demands.
```erb
<label for="email" class="sr-only">Email</label>
<input id="email" type="email" placeholder="you@example.com" class="…">
```

## 18. Shipping without testing at 375px

The most common breakage: horizontal scroll on mobile from a too-wide element. Test at 375px every time. If that feels tedious, it means you're not reaching for your design tokens — `max-w-*`, `flex-wrap`, `break-words` handle this.

## 19. "Semantic" components that aren't

**Bad:**
```ruby
class ButtonComponent
  def initialize(text:, color:, size:, rounded:, shadow:, ...)
```

17 options, reimplementing Tailwind in Ruby. No constraint = no system.

**Fix:** Enum variants. `variant: :primary | :secondary | :ghost | :danger`, `size: :sm | :md | :lg`. If a specific use case needs something else, add a new variant — don't open the bikeshed.

## 20. Keeping unused CSS

Old component CSS files, `@apply` leftovers from a refactor, unused custom classes. Tailwind will happily ship them because CSS can't be purged from your own files.

**Fix:** Regularly grep for custom class names across the codebase. If nothing uses `.old-button`, delete it. Same for abandoned `@theme` tokens.
