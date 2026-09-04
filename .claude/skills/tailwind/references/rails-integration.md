# Rails Integration

How Tailwind actually lives inside a Rails app: tailwindcss-rails, ViewComponent, Stimulus, Turbo, dark mode toggle, asset pipeline.

## Setup (tailwindcss-rails)

### New app

```bash
rails new myapp --css=tailwind
```

This installs `tailwindcss-rails`, creates `app/assets/tailwind/application.css`, adds `bin/dev` running Foreman with a `watch:css` process.

### Existing app

```bash
bundle add tailwindcss-rails
bin/rails tailwindcss:install
```

### Files it adds

- `app/assets/tailwind/application.css` — the theme + imports. This is where `@theme { }` lives.
- `app/views/layouts/application.html.erb` — adds `<%= stylesheet_link_tag "tailwind", "data-turbo-track": "reload" %>` in `<head>`.
- `Procfile.dev` — adds `css: bin/rails tailwindcss:watch`.
- `bin/dev` — boots Foreman.

### Running in dev

```bash
bin/dev
```

This runs `rails server` + `tailwindcss:watch` + whatever else is in `Procfile.dev`. Do not run `rails s` alone — Tailwind classes won't regenerate.

### Version check

```bash
bundle show tailwindcss-ruby
```

- `4.x` → v4 (CSS-first `@theme`)
- `3.x` → v3 (JS config)

Check `package.json` too if the project uses JS-based install instead.

## Propshaft vs Sprockets

tailwindcss-rails works with both. On new Rails 7.1+ apps, **Propshaft** is default. Tailwind outputs `public/assets/builds/tailwind.css` which Propshaft serves.

If the app still uses Sprockets (legacy), no action needed — the output path is the same.

## `stylesheet_link_tag`

Always pair with Turbo tracking so reloads pick up new CSS:

```erb
<%= stylesheet_link_tag "tailwind", "data-turbo-track": "reload" %>
<%= stylesheet_link_tag "application", "data-turbo-track": "reload" %>
```

`application.css` (from asset pipeline) can stay for component-specific CSS that doesn't belong in Tailwind. Keep it small — 90% should be Tailwind utilities.

## Content Paths (critical for v3)

Tailwind only generates classes it sees in scanned files. Miss a path = missing classes in production.

### v4 (auto-detect)

v4 auto-scans most common paths. You usually don't need to configure content. If a class is missing:

```css
/* app/assets/tailwind/application.css */
@import "tailwindcss";
@source "../../../lib/mailers";  /* explicit extra source */
```

### v3 (explicit)

`config/tailwind.config.js`:

```js
module.exports = {
  content: [
    './public/*.html',
    './app/helpers/**/*.rb',
    './app/javascript/**/*.js',
    './app/views/**/*.{erb,haml,html,slim}',
    './app/components/**/*.{rb,erb,haml,html,slim}',  /* ViewComponent */
    './app/mailers/**/*.rb'
  ],
  …
}
```

The `*.{erb,...}` glob catches compound names too (`show.turbo_stream.erb`, `index.html.erb`) because the match is on the final extension. If you split an admin namespace into its own directory, add its glob explicitly (`./app/views/admin/**/*.erb`). Same for any engine, `lib/`, or non-standard mount point.

## ViewComponent

ViewComponent is the idiomatic home for reusable Tailwind UI. Install:

```bash
bundle add view_component
```

### Anatomy

```ruby
# app/components/button_component.rb
class ButtonComponent < ViewComponent::Base
  VARIANTS = {
    primary:   "bg-brand-600 text-white hover:bg-brand-700 focus-visible:ring-brand-500",
    secondary: "bg-zinc-100 text-zinc-900 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-100 dark:hover:bg-zinc-700 focus-visible:ring-zinc-500",
    ghost:     "text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800 focus-visible:ring-zinc-500",
    danger:    "bg-red-600 text-white hover:bg-red-700 focus-visible:ring-red-500"
  }.freeze

  SIZES = {
    sm: "px-3 py-1.5 text-sm",
    md: "px-4 py-2 text-sm",
    lg: "px-5 py-2.5 text-base"
  }.freeze

  BASE = "inline-flex items-center justify-center gap-1.5 rounded-lg font-medium transition " \
         "focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 " \
         "dark:focus-visible:ring-offset-zinc-950 disabled:opacity-50 disabled:pointer-events-none"

  def initialize(variant: :primary, size: :md, as: :button, **opts)
    @variant = variant
    @size    = size
    @as      = as
    @opts    = opts
  end

  def call
    classes = [BASE, VARIANTS.fetch(@variant), SIZES.fetch(@size), @opts.delete(:class)].compact.join(" ")
    if @as == :link
      link_to(content, @opts.delete(:href) || "#", class: classes, **@opts)
    else
      tag.button(content, class: classes, **@opts)
    end
  end
end
```

Usage:

```erb
<%= render ButtonComponent.new(variant: :primary) do %>Save<% end %>
<%= render ButtonComponent.new(variant: :danger, size: :sm, type: :submit) do %>Delete<% end %>
<%= render ButtonComponent.new(as: :link, href: new_post_path) do %>New post<% end %>
```

### When to extract a component

Rule of three: copy-paste once, duplicate twice, **extract on third occurrence**. Don't pre-abstract.

### Slots for composition

```ruby
class CardComponent < ViewComponent::Base
  renders_one :header
  renders_one :footer
end
```

```erb
<!-- app/components/card_component.html.erb -->
<article class="overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
  <% if header? %><header class="border-b border-zinc-200 p-6 dark:border-zinc-800"><%= header %></header><% end %>
  <div class="p-6"><%= content %></div>
  <% if footer? %><footer class="border-t border-zinc-200 p-6 dark:border-zinc-800"><%= footer %></footer><% end %>
</article>
```

```erb
<%= render CardComponent.new do |c| %>
  <% c.with_header do %><h3 class="text-lg font-semibold">Title</h3><% end %>
  Body content.
  <% c.with_footer do %><%= render ButtonComponent.new do %>OK<% end %><% end %>
<% end %>
```

### Partials vs ViewComponent

- **Partial** (`_card.html.erb`): quick extraction, no logic, just markup reuse. Locals via `render "card", title: …`.
- **ViewComponent**: when you need variants, computed classes, conditional slots, testability. Preferred for anything with more than "replace three strings."

## Stimulus

Stimulus is Rails' answer to client-side interactivity. Lives in `app/javascript/controllers/`.

### Scaffolding

```bash
bin/rails stimulus:install  # if not already installed
bin/rails g stimulus dropdown  # generates dropdown_controller.js
```

Controllers auto-register via `app/javascript/controllers/index.js` (or eager-load via `@hotwired/stimulus-loading`).

### Core concepts

- **Controller**: ES module extending `Controller`. Attached to DOM via `data-controller="name"`.
- **Target**: named element reference. `data-<ctrl>-target="trigger"` → `this.triggerTarget`.
- **Action**: event handler binding. `data-action="click->dropdown#toggle"`.
- **Value**: typed, reactive attribute. `data-<ctrl>-open-value="false"` → `this.openValue`, plus `openValueChanged()` callback.
- **Class**: themed class name. `data-<ctrl>-active-class="bg-brand-600"` → `this.activeClass`.

### Canonical pattern: disclosure

```erb
<div data-controller="disclosure" data-disclosure-open-value="false">
  <button type="button"
    data-disclosure-target="trigger"
    data-action="click->disclosure#toggle"
    aria-expanded="false">
    Details
  </button>
  <div data-disclosure-target="panel" hidden>
    Hidden content.
  </div>
</div>
```

```js
// app/javascript/controllers/disclosure_controller.js
import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
  static targets = ["trigger", "panel"]
  static values = { open: { type: Boolean, default: false } }

  toggle() { this.openValue = !this.openValue }

  openValueChanged() {
    this.panelTarget.hidden = !this.openValue
    this.triggerTarget.setAttribute("aria-expanded", this.openValue)
  }
}
```

The `openValueChanged` callback fires on connect too, so initial state is handled without extra code.

### Patterns you'll build

Common controllers: `theme`, `dialog`, `menu` (dropdown), `tabs`, `disclosure`, `toast`, `mobile-menu`, `autosubmit` (debounced form submit), `copy-to-clipboard`.

Each lives in its own file — 20–50 lines is typical. If a controller gets past 100 lines, split it.

## Turbo

Tailwind components must work with Turbo's navigation model:

- **Turbo Drive** replaces full-page loads with fetch+swap. Stimulus controllers connect/disconnect across navigations — rely on `connect()`/`disconnect()` lifecycle for setup/teardown, not `DOMContentLoaded`.
- **Turbo Frames** (`<turbo-frame>`) swap page sections. Tailwind classes on a frame persist; inner markup is swapped.
- **Turbo Streams** broadcast DOM updates. Appending a toast, replacing a row, removing an item — no full render needed.

### Gotcha: lost event listeners

Don't `addEventListener` manually in views — Turbo swaps will leak handlers. Always use Stimulus `data-action` or controller `connect()`.

### Gotcha: `<dialog>` + Turbo

Native `<dialog>` keeps its `open` state across Turbo morphs. If a modal is open and Turbo replaces the page, the modal may reappear detached. Close dialogs before navigation:

```js
document.addEventListener("turbo:before-visit", () => {
  document.querySelectorAll("dialog[open]").forEach(d => d.close())
})
```

### View transitions

Rails 8 supports CSS view transitions via meta tag:

```erb
<%= yield :head %>
<meta name="view-transition" content="same-origin">
```

Tailwind's `view-transition-*` utilities (via arbitrary property) let you animate across Turbo navigations. This is bleeding-edge; test browser support.

## Dark Mode Toggle

### Full implementation

```erb
<!-- app/views/layouts/application.html.erb -->
<!DOCTYPE html>
<html lang="en" data-controller="theme">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <!-- Prevent FOUC: set class before Tailwind loads -->
    <script>
      (function() {
        var stored = localStorage.getItem("theme")
        var prefersDark = matchMedia("(prefers-color-scheme: dark)").matches
        if (stored === "dark" || (!stored && prefersDark)) {
          document.documentElement.classList.add("dark")
        }
      })()
    </script>

    <%= csrf_meta_tags %>
    <%= csp_meta_tag %>
    <%= stylesheet_link_tag "tailwind", "data-turbo-track": "reload" %>
    <%= javascript_importmap_tags %>
  </head>
  <body class="min-h-screen bg-white text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
    <%= yield %>
  </body>
</html>
```

```erb
<!-- app/views/shared/_theme_toggle.html.erb -->
<button type="button"
  data-action="theme#toggle"
  class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-zinc-500 hover:bg-zinc-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 dark:text-zinc-400 dark:hover:bg-zinc-800"
  aria-label="Toggle theme">
  <svg class="hidden h-5 w-5 dark:inline" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
    <path d="M10 2a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 2zM10 15a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 15zM10 7a3 3 0 100 6 3 3 0 000-6z"/>
  </svg>
  <svg class="h-5 w-5 dark:hidden" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
    <path d="M7.455 2.004a.75.75 0 01.26.77 7 7 0 009.958 7.967.75.75 0 011.067.853A8.5 8.5 0 116.647 1.921a.75.75 0 01.808.083z"/>
  </svg>
</button>
```

```js
// app/javascript/controllers/theme_controller.js
import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
  toggle() {
    const isDark = this.element.classList.toggle("dark")
    localStorage.setItem("theme", isDark ? "dark" : "light")
  }
}
```

The inline `<script>` in `<head>` runs before body paints — prevents the "light theme flash" on first paint for users who prefer dark. CSP note: this inline script needs a nonce or CSP allowance in strict setups.

## DaisyUI

DaisyUI is a Tailwind plugin that adds semantic component classes (`btn`, `card`, `input`, `modal`) and a theme system.

### Install

```bash
npm install -D daisyui@latest
# or if using bundled tailwindcss-rails, add to the built config
```

### v4 integration

```css
/* app/assets/tailwind/application.css */
@import "tailwindcss";
@plugin "daisyui" {
  themes: light --default, dark --prefersdark;
}
```

### v3 integration

```js
// config/tailwind.config.js
module.exports = {
  plugins: [require('daisyui')],
  daisyui: {
    themes: ["light", "dark"]
  }
}
```

### Usage

```erb
<button class="btn btn-primary">Save</button>
<div class="card bg-base-100 shadow-xl">
  <div class="card-body">
    <h2 class="card-title">Title</h2>
    <p>Body</p>
  </div>
</div>
```

### When to use DaisyUI vs raw utilities

**Use DaisyUI** when:
- You want fast, themed consistency without designing every primitive
- The app has simple theming needs (light + dark, maybe a brand theme)
- Team prefers semantic class names over utility soup

**Skip DaisyUI** when:
- You're already designing every component carefully (DaisyUI becomes a cage)
- You need precise visual control (DaisyUI's `btn` has opinions you'd fight)
- You're using other component libraries (Flowbite, Preline) that clash

Don't mix. One themed system per app.

## Asset Pipeline Gotchas

- **Deploy forgets `tailwindcss:build`**: the `assets:precompile` Rake task triggers it automatically via `tailwindcss-rails`. If you see missing styles in production, check that `Rakefile` hooks are intact.
- **Heroku/Fly slug size**: the built CSS is tiny; don't worry.
- **CDN asset URLs**: `@font-face` paths in CSS need `asset_path` helpers — or use Google Fonts `<link>` in layout head instead.
- **Custom font file hosting**: put files in `app/assets/fonts/`, reference via `@font-face` in `application.css` (outside `@theme`), Propshaft resolves.

## Mailers

Email clients (Gmail, Outlook) strip `<link>` stylesheets and drop modern CSS. Tailwind utility classes won't render unless inlined. Options, in order of increasing investment:

1. **Keep mail simple** (default): one column, generous padding, minimal styling. Use Rails' built-in `mailer.html.erb` layout. For 90% of transactional email this is the right call.
2. **Inline via premailer**: add `premailer-rails`. Build a mailer-specific Tailwind CSS (`bin/rails tailwindcss:build` with `--input app/assets/tailwind/mailer.css --content 'app/views/**/*_mailer/**/*.{erb,html}'`). Include it via `<style>` in `mailer.html.erb`; premailer inlines it on send.
3. **Email-specific HTML**: tables, `bgcolor=`, inline `style=`. Reach here only for marketing email with strict design requirements.

Default to option 1. Only invest in a mailer Tailwind build if the designer has opinions.
