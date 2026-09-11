# Component Libraries

Copy-paste blocks instead of runtime dependencies. The app owns its markup.

## The Matrix

| Library | Best for | Framework | License | Dark mode |
|---------|----------|-----------|---------|-----------|
| [HyperUI](https://hyperui.dev) | Marketing, e-commerce, app UI — v4-ready | Plain HTML | MIT | Built-in |
| [Flowbite Blocks](https://flowbite.com/blocks) | Full SaaS sections, CTAs, pricing | HTML (JS optional) | Free tier + Pro | Built-in |
| [TailGrids](https://tailgrids.com/components) | Dashboards, SaaS, AI | HTML + React | Free + Pro | Built-in |
| [TW Elements](https://tw-elements.com) | Material-style | HTML + own JS | MIT | Built-in |
| [Material Tailwind](https://www.material-tailwind.com) | Material components | HTML + React | MIT | Supported |
| [Material Minimal](https://material-minimal.com) | Minimal Material look | HTML | Free | Supported |
| [DaisyUI](https://daisyui.com) | Themed classes (`btn`, `card`) | Plugin | MIT | Themes |
| [Preline UI](https://preline.co) | Large kit, v4, WCAG-leaning | HTML + small JS | MIT | Built-in |
| [Meraki UI](https://merakiui.com) | RTL, multilingual | HTML | MIT | Built-in |
| [Mamba UI](https://mambaui.com) | Portfolios, landing | HTML/Vue/JSX | MIT | Some |
| [TailBlocks](https://tailblocks.cc) | Quick landing sections | HTML | MIT | Built-in |
| [WickedBlocks](https://wickedblocks.dev) | Marketing blocks | HTML | MIT | Some |
| [Tailkit](https://tailkit.com) | Admin + marketing | HTML/Vue/React | Free + Pro | Some |
| [Kometa](https://kitwind.io/products/kometa) | Startup sections | HTML/React/Vue | Free | Some |
| [Float UI](https://floatui.com) | App + marketing | React/Vue/Svelte/HTML | MIT | Some |
| [Sailboat UI](https://sailboatui.com) | Landing + admin | HTML | MIT | Some |
| [Ripple UI](https://rippleui.com) | SaaS dashboards | HTML/JSX | MIT | Supported |
| [Kutty UI](https://kutty.netlify.app) | App layouts | Plugin | MIT | Supported |
| [Headless UI](https://headlessui.com) | Behavior primitives | React/Vue | MIT | N/A (unstyled) |
| [Aceternity UI](https://ui.aceternity.com) | Animated trendy | React | MIT | Supported |
| [Sera UI](https://seraui.com) | Animated React | React | MIT | Supported |
| [HeroUI](https://heroui.com) | Large React kit | React | MIT | Supported |

## Which library for which need?

```
Hero / landing section       → HyperUI, Flowbite Blocks, TailBlocks, WickedBlocks
Pricing table                → Flowbite Blocks, TailGrids, HyperUI
Feature grid                 → HyperUI, Kometa, Mamba UI
Dashboard shell              → TailGrids, Preline UI, Ripple UI, Tailkit
Sidebar navigation           → Preline UI, TailGrids, Kutty
Data table                   → Flowbite, TailGrids, Preline UI
Forms with validation        → Flowbite, Preline UI (+ Headless UI patterns)
Modal / dialog               → HyperUI, Flowbite, Preline (patterns; port JS to Stimulus)
Dropdown / menu              → Headless UI (port to Stimulus), Preline
Tabs                         → Headless UI (port to Stimulus), Flowbite
Toast / notification         → Flowbite, Preline, TailGrids
Empty states                 → Kutty, HyperUI
RTL / multilingual           → Meraki UI
Material look                → Material Tailwind, Material Minimal, TW Elements
Fancy animations             → Aceternity, Sera UI (port React → Stimulus + CSS)
Themed quick-start           → DaisyUI (trade utility purity for speed)
```

## Copy-Paste Workflow

### 1. WebFetch the block

Ask for exact HTML, not a description:

```
WebFetch(
  "https://hyperui.dev/components/marketing/banners/001",
  "Extract the complete HTML source for this banner component.
   Include all utility classes exactly as written. Do not summarize."
)
```

For libraries with index pages:

```
WebFetch(
  "https://hyperui.dev/components/marketing/banners",
  "List all banner components on this page with their URLs and a one-line description of each."
)
```

### 2. Port to ERB

Typical replacements:

| Source | ERB |
|--------|-----|
| Hardcoded copy | `<%= t('.heading') %>` or `<%= @model.title %>` |
| `<a href="#">` | `<%= link_to ..., path %>` |
| `<form action="#">` | `<%= form_with model: @x do |f| %>` |
| `<button onclick="...">` | `<button data-action="controller#action">` |
| `class="..."` (React) | same in ERB (no `className`) |
| Inline icons (SVG) | Extract to `app/assets/images/icons/*.svg` + `image_tag` or partial |
| Alpine.js `x-data` | Port to Stimulus controller |
| React state (`useState`) | Stimulus `data-*-value` + targets |

### 3. Retoken

Every copied block uses the source's theme (e.g., `bg-blue-600`). Swap for your tokens:

```
bg-blue-600       → bg-brand-600
text-blue-700     → text-brand-700
hover:bg-blue-700 → hover:bg-brand-700
focus:ring-blue-500 → focus-visible:ring-brand-500
```

`focus:` → `focus-visible:` is a common fix — library blocks often use the older `focus:` variant.

### 4. Add dark mode if missing

Some libraries (TailBlocks, Kometa) skip dark mode or do it half-heartedly. Use the pairing table in `tokens-and-theme.md` § Dark-mode color pairings.

### 5. Extract to ViewComponent/partial when reused

Three occurrences → component. See `../SKILL.md` § Button for a canonical example.

## Porting React → ERB + Stimulus

React libraries (Aceternity, Sera UI, HeroUI, Headless UI) need translation, not copy-paste.

### Patterns

**Conditional rendering:**
```jsx
{isOpen && <Menu />}
```
→
```erb
<% if @menu_open %>
  <%= render MenuComponent.new %>
<% end %>
```
Or client-side with Stimulus:
```erb
<div data-controller="disclosure" data-disclosure-open-value="false">
  <button data-action="disclosure#toggle" aria-expanded="false" data-disclosure-target="trigger">Open</button>
  <div data-disclosure-target="panel" hidden>…</div>
</div>
```

**State (`useState`):**
```jsx
const [count, setCount] = useState(0)
```
→ Stimulus value:
```js
static values = { count: { type: Number, default: 0 } }
increment() { this.countValue++ }
countValueChanged() { this.displayTarget.textContent = this.countValue }
```

**Event handlers:**
```jsx
onClick={() => setOpen(!open)}
```
→
```erb
<button data-action="click->disclosure#toggle">…</button>
```

**Headless UI behavior:**
Headless UI is a behavior reference, not a dependency. Read how they handle ARIA/keyboard, implement in Stimulus. Example: a dropdown menu needs:
- `aria-expanded` on trigger
- `aria-controls` pointing to panel id
- `role="menu"` on panel, `role="menuitem"` on items
- Arrow keys move focus
- Escape closes
- Click outside closes
- Focus returns to trigger on close

See `component-patterns.md` § Dropdown Menu for the Stimulus implementation.

## License Notes

**MIT** (HyperUI, Flowbite free, DaisyUI, Preline, Meraki, most others): free for personal/commercial, no attribution required in output. Keep the `LICENSE` in vendored code if you copy files wholesale — for class strings in your own markup, no attribution needed.

**Freemium** (Flowbite Pro, TailGrids Pro, Tailkit Pro): free tier is usually enough. Don't copy Pro-only blocks without a license.

**DaisyUI** specifically is a plugin, not copy-paste. Adding it changes `btn`, `card`, etc. to semantic classes. See `rails-integration.md` § DaisyUI.

## Browsing Tips (WebFetch)

Library index pages are JS-heavy and often render poorly to WebFetch. Workarounds:

- **Fetch component detail pages directly** (the URL after you click through in a browser). These are usually static.
- For HyperUI: URLs follow `hyperui.dev/components/{category}/{name}/NNN`. Category listings at `hyperui.dev/components/{category}`.
- For Flowbite: `flowbite.com/blocks/{category}/{component}/` and preview tabs.
- For TailGrids: `tailgrids.com/components/{component}` (no blocks index needed for most).
- If WebFetch returns mostly navigation/boilerplate, ask the model: "extract only the code blocks from this page, in their entirety, as a list" — usually the code is there but buried.
- If all else fails, have the user paste the HTML into the conversation.

## Snapshot: what you'll actually need most

For a Rails app, 80% of needs come from:

1. **HyperUI** — marketing sections, e-commerce, simple app UI
2. **Flowbite Blocks** — dashboard patterns, forms, pricing, complex sections
3. **Preline UI** — when you need a large coherent kit
4. **DaisyUI** — when the project wants themed consistency over utility purity
5. **Headless UI patterns** — for every interactive widget (port to Stimulus)

Start there. Reach for niche libraries only for specific aesthetics (Aceternity for animations, Material Tailwind for Material look, Meraki for RTL).
