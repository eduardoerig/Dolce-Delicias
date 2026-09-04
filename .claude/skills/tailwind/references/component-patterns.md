# Component Patterns

From-scratch patterns for when no library block fits. Every pattern:

- Uses theme tokens (`bg-brand-*`, `text-zinc-*`) — no hardcoded colors
- Ships light + dark
- Has `focus-visible:` rings on all interactive elements
- Uses Stimulus for state, not vanilla JS or Alpine
- Is accessible (ARIA, keyboard)

## Buttons

### Primary / Secondary / Ghost

```erb
<!-- Primary -->
<button type="button"
  class="inline-flex items-center justify-center rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-brand-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-950 disabled:opacity-50 disabled:pointer-events-none">
  Save changes
</button>

<!-- Secondary -->
<button type="button"
  class="inline-flex items-center justify-center rounded-lg bg-zinc-100 px-4 py-2 text-sm font-medium text-zinc-900 transition hover:bg-zinc-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-500 focus-visible:ring-offset-2 dark:bg-zinc-800 dark:text-zinc-100 dark:hover:bg-zinc-700 dark:focus-visible:ring-offset-zinc-950 disabled:opacity-50">
  Cancel
</button>

<!-- Ghost -->
<button type="button"
  class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-500 dark:text-zinc-300 dark:hover:bg-zinc-800 disabled:opacity-50">
  Learn more
</button>
```

Extract to `ButtonComponent` once you use this 3+ times. See `../SKILL.md` § Button.

### Icon button

```erb
<button type="button" aria-label="Close"
  class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-500 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100">
  <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
    <path d="…" />
  </svg>
</button>
```

Always give icon-only buttons an `aria-label`.

### Button group

```erb
<div class="inline-flex rounded-lg shadow-sm" role="group">
  <button type="button" class="rounded-l-lg border border-zinc-300 bg-white px-4 py-2 text-sm font-medium text-zinc-900 hover:bg-zinc-50 focus-visible:z-10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800">Day</button>
  <button type="button" class="-ml-px border border-zinc-300 bg-white px-4 py-2 text-sm font-medium text-zinc-900 hover:bg-zinc-50 focus-visible:z-10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800">Week</button>
  <button type="button" class="-ml-px rounded-r-lg border border-zinc-300 bg-white px-4 py-2 text-sm font-medium text-zinc-900 hover:bg-zinc-50 focus-visible:z-10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800">Month</button>
</div>
```

## Forms

### Text input

```erb
<div>
  <label for="email" class="block text-sm font-medium text-zinc-900 dark:text-zinc-100">Email</label>
  <input id="email" name="email" type="email" autocomplete="email" required
    class="mt-1.5 block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm placeholder:text-zinc-400 focus-visible:border-brand-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/20 aria-[invalid=true]:border-red-500 aria-[invalid=true]:ring-red-500/20 disabled:bg-zinc-50 disabled:text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:focus-visible:border-brand-400 dark:focus-visible:ring-brand-400/30">
  <p class="mt-1.5 text-xs text-zinc-500 dark:text-zinc-400">We'll never share your email.</p>
</div>
```

Error variant — add `aria-invalid="true"` and render a `<p role="alert">` below:

```erb
<input aria-invalid="true" aria-describedby="email-error" …>
<p id="email-error" role="alert" class="mt-1.5 text-xs text-red-600 dark:text-red-400">Enter a valid email.</p>
```

### Rails form_with version

```erb
<%= form_with model: @user, class: "space-y-4" do |f| %>
  <div>
    <%= f.label :email, class: "block text-sm font-medium text-zinc-900 dark:text-zinc-100" %>
    <%= f.email_field :email, required: true, autocomplete: "email",
      class: "mt-1.5 block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm ...",
      aria: { invalid: @user.errors[:email].any? } %>
    <% @user.errors[:email].each do |msg| %>
      <p role="alert" class="mt-1.5 text-xs text-red-600 dark:text-red-400"><%= msg %></p>
    <% end %>
  </div>
<% end %>
```

### Checkbox

```erb
<label class="flex items-start gap-2.5">
  <input type="checkbox" name="terms"
    class="mt-0.5 h-4 w-4 rounded border-zinc-300 text-brand-600 focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-0 dark:border-zinc-600 dark:bg-zinc-900">
  <span class="text-sm text-zinc-700 dark:text-zinc-300">I agree to the <a href="#" class="font-medium text-brand-600 hover:underline dark:text-brand-400">terms</a>.</span>
</label>
```

Install `@tailwindcss/forms` plugin for sensible defaults on native inputs.

### Select

```erb
<select name="country"
  class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus-visible:border-brand-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
  <option>United States</option>
  <option>Canada</option>
</select>
```

For searchable selects / combobox → Stimulus + native `<datalist>` for simple, or port Headless UI Combobox pattern.

### Textarea

```erb
<textarea rows="4" name="message"
  class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm placeholder:text-zinc-400 focus-visible:border-brand-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"></textarea>
```

### Switch (toggle)

```erb
<label class="inline-flex cursor-pointer items-center gap-3">
  <input type="checkbox" name="notifications" class="peer sr-only">
  <span class="relative h-6 w-11 rounded-full bg-zinc-300 transition peer-checked:bg-brand-600 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-500 peer-focus-visible:ring-offset-2 dark:bg-zinc-700 dark:peer-focus-visible:ring-offset-zinc-950">
    <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span>
  </span>
  <span class="text-sm text-zinc-700 dark:text-zinc-300">Email notifications</span>
</label>
```

## Cards

### Basic card

```erb
<article class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
  <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Card title</h3>
  <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Supporting text.</p>
</article>
```

### Card with media

```erb
<article class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm transition hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900">
  <img src="<%= @post.cover_url %>" alt="" class="aspect-video w-full object-cover">
  <div class="p-6">
    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100"><%= @post.title %></h3>
    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400"><%= @post.excerpt %></p>
    <div class="mt-4 flex items-center gap-3 text-xs text-zinc-500 dark:text-zinc-500">
      <%= time_tag @post.published_at, @post.published_at.strftime("%b %-d, %Y") %>
      <span aria-hidden="true">·</span>
      <span><%= @post.read_time %> min read</span>
    </div>
  </div>
</article>
```

### Clickable card (whole-area link)

Don't nest `<a>` inside interactive elements. Use a "card link" pattern:

```erb
<article class="group relative rounded-2xl border border-zinc-200 bg-white p-6 transition hover:border-brand-500 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-brand-400">
  <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
    <%= link_to @post.title, @post,
      class: "before:absolute before:inset-0 before:content-[''] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 rounded-2xl" %>
  </h3>
  <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400"><%= @post.excerpt %></p>
</article>
```

The `before:absolute before:inset-0` expands the anchor's click target to the whole card while keeping the DOM semantic.

## Modals / Dialogs

Use the native `<dialog>` element — it handles focus trap, escape-to-close, and backdrop for free.

```erb
<button type="button" data-action="click->dialog#open" data-dialog-id="confirm"
  class="…">Delete</button>

<dialog id="confirm" data-controller="dialog"
  class="backdrop:bg-zinc-900/50 backdrop:backdrop-blur-sm rounded-2xl border border-zinc-200 bg-white p-6 shadow-xl max-w-md dark:border-zinc-800 dark:bg-zinc-900 open:animate-in open:fade-in">
  <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Delete item?</h2>
  <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">This can't be undone.</p>
  <div class="mt-6 flex justify-end gap-2">
    <button type="button" data-action="click->dialog#close" class="…">Cancel</button>
    <button type="button" data-action="click->dialog#confirm" class="bg-red-600 hover:bg-red-700 …">Delete</button>
  </div>
</dialog>
```

```js
// app/javascript/controllers/dialog_controller.js
import { Controller } from "@hotwired/stimulus"
export default class extends Controller {
  open(event) {
    const id = event.currentTarget.dataset.dialogId
    const dialog = id ? document.getElementById(id) : this.element
    dialog.showModal()
  }
  close() { this.element.close() }
  confirm() {
    this.dispatch("confirm")
    this.close()
  }
}
```

## Dropdown Menu

Port of Headless UI `Menu`. Menu items intentionally use `focus:bg-*` (not `focus-visible:`) because arrow-key navigation focuses them programmatically — `focus-visible:` would fail to style them on some browsers. The panel uses `z-20` to sit above sticky nav (`z-10`).

```erb
<div data-controller="menu" class="relative inline-block">
  <button type="button"
    data-menu-target="trigger"
    data-action="click->menu#toggle keydown->menu#key"
    aria-haspopup="true" aria-expanded="false"
    class="inline-flex items-center gap-1 rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 dark:text-zinc-300 dark:hover:bg-zinc-800">
    Options
    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"/></svg>
  </button>
  <div data-menu-target="panel" hidden role="menu"
    class="absolute right-0 z-20 mt-2 w-48 origin-top-right rounded-lg border border-zinc-200 bg-white p-1 shadow-lg focus:outline-none dark:border-zinc-800 dark:bg-zinc-900">
    <a href="#" role="menuitem" class="block rounded-md px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-100 focus:bg-zinc-100 focus:outline-none dark:text-zinc-300 dark:hover:bg-zinc-800 dark:focus:bg-zinc-800">Edit</a>
    <a href="#" role="menuitem" class="block rounded-md px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-100 focus:bg-zinc-100 focus:outline-none dark:text-zinc-300 dark:hover:bg-zinc-800 dark:focus:bg-zinc-800">Duplicate</a>
    <a href="#" role="menuitem" class="block rounded-md px-3 py-2 text-sm text-red-600 hover:bg-red-50 focus:bg-red-50 focus:outline-none dark:text-red-400 dark:hover:bg-red-950/40 dark:focus:bg-red-950/40">Delete</a>
  </div>
</div>
```

```js
// app/javascript/controllers/menu_controller.js
import { Controller } from "@hotwired/stimulus"
export default class extends Controller {
  static targets = ["trigger", "panel"]
  connect() { this.boundOutside = this.outside.bind(this) }
  toggle() { this.panelTarget.hidden ? this.open() : this.close() }
  open() {
    this.panelTarget.hidden = false
    this.triggerTarget.setAttribute("aria-expanded", "true")
    document.addEventListener("click", this.boundOutside)
    this.panelTarget.querySelector("[role=menuitem]")?.focus()
  }
  close() {
    this.panelTarget.hidden = true
    this.triggerTarget.setAttribute("aria-expanded", "false")
    document.removeEventListener("click", this.boundOutside)
  }
  outside(e) { if (!this.element.contains(e.target)) this.close() }
  key(e) {
    if (e.key === "Escape") { this.close(); this.triggerTarget.focus() }
    if (e.key === "ArrowDown" && this.panelTarget.hidden) { e.preventDefault(); this.open() }
  }
  disconnect() { document.removeEventListener("click", this.boundOutside) }
}
```

## Tabs

```erb
<div data-controller="tabs" data-tabs-active-value="overview">
  <div role="tablist" class="flex gap-1 border-b border-zinc-200 dark:border-zinc-800">
    <% %w[overview activity settings].each do |tab| %>
      <button type="button" role="tab"
        id="tab-<%= tab %>" aria-controls="panel-<%= tab %>"
        data-tabs-target="tab" data-tab-name="<%= tab %>"
        data-action="click->tabs#select"
        class="border-b-2 border-transparent px-4 py-2.5 text-sm font-medium text-zinc-600 hover:text-zinc-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 aria-selected:border-brand-500 aria-selected:text-brand-600 dark:text-zinc-400 dark:hover:text-zinc-100 dark:aria-selected:text-brand-400"
        aria-selected="<%= tab == 'overview' %>">
        <%= tab.titleize %>
      </button>
    <% end %>
  </div>
  <% %w[overview activity settings].each do |tab| %>
    <div role="tabpanel" id="panel-<%= tab %>" aria-labelledby="tab-<%= tab %>"
      data-tabs-target="panel" data-panel-name="<%= tab %>"
      class="mt-6 aria-[hidden=true]:hidden"
      aria-hidden="<%= tab != 'overview' %>">
      <%= render "users/#{tab}" %>
    </div>
  <% end %>
</div>
```

```js
// app/javascript/controllers/tabs_controller.js
import { Controller } from "@hotwired/stimulus"
export default class extends Controller {
  static targets = ["tab", "panel"]
  static values = { active: String }
  select(e) { this.activeValue = e.currentTarget.dataset.tabName }
  activeValueChanged() {
    this.tabTargets.forEach(t => t.setAttribute("aria-selected", t.dataset.tabName === this.activeValue))
    this.panelTargets.forEach(p => p.setAttribute("aria-hidden", p.dataset.panelName !== this.activeValue))
  }
}
```

## Navigation

### Top bar

```erb
<header class="border-b border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
  <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
    <%= link_to root_path, class: "flex items-center gap-2" do %>
      <%= image_tag "logo.svg", class: "h-8 w-auto", alt: "Acme" %>
      <span class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Acme</span>
    <% end %>
    <nav class="hidden gap-1 md:flex">
      <% %w[Dashboard Projects Team].each do |label| %>
        <%= link_to label, "#", class: "rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 dark:text-zinc-300 dark:hover:bg-zinc-800 aria-[current=page]:bg-zinc-100 aria-[current=page]:text-zinc-900 dark:aria-[current=page]:bg-zinc-800 dark:aria-[current=page]:text-zinc-100",
          aria: { current: current_page?("#") ? "page" : nil } %>
      <% end %>
    </nav>
    <div class="flex items-center gap-2">
      <%= render "shared/theme_toggle" %>
      <%= render "shared/user_menu" %>
    </div>
  </div>
</header>
```

### Sidebar (desktop)

```erb
<aside class="fixed inset-y-0 left-0 hidden w-64 border-r border-zinc-200 bg-white p-4 md:flex md:flex-col dark:border-zinc-800 dark:bg-zinc-900">
  <div class="mb-6 flex items-center gap-2 px-2">
    <%= image_tag "logo.svg", class: "h-8 w-auto" %>
    <span class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Acme</span>
  </div>
  <nav class="flex-1 space-y-1">
    <% nav_items.each do |item| %>
      <%= link_to item[:path],
        class: "flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 dark:text-zinc-300 dark:hover:bg-zinc-800 aria-[current=page]:bg-brand-50 aria-[current=page]:text-brand-700 dark:aria-[current=page]:bg-brand-950/40 dark:aria-[current=page]:text-brand-300",
        aria: { current: current_page?(item[:path]) ? "page" : nil } do %>
        <%= render "icons/#{item[:icon]}", class: "h-5 w-5" %>
        <%= item[:label] %>
      <% end %>
    <% end %>
  </nav>
</aside>
```

### Mobile menu

Use `<dialog>` again for the drawer, or a Stimulus-driven panel:

```erb
<button type="button" data-action="click->mobile-menu#open" class="md:hidden …" aria-label="Open menu">
  <svg …><!-- hamburger --></svg>
</button>
<div data-controller="mobile-menu" data-mobile-menu-target="panel" hidden
  class="fixed inset-0 z-50 md:hidden">
  <div class="absolute inset-0 bg-zinc-900/50 backdrop-blur-sm" data-action="click->mobile-menu#close"></div>
  <nav class="absolute inset-y-0 left-0 w-72 max-w-[80%] bg-white p-4 shadow-xl dark:bg-zinc-900">
    <!-- nav items -->
  </nav>
</div>
```

## Badges & Pills

```erb
<!-- Success -->
<span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">Active</span>

<!-- Warning -->
<span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">Pending</span>

<!-- Neutral -->
<span class="inline-flex items-center rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-medium text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300">Draft</span>

<!-- With dot -->
<span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">
  <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
  Online
</span>
```

## Tables

```erb
<div class="overflow-x-auto rounded-2xl border border-zinc-200 dark:border-zinc-800">
  <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-800">
    <thead class="bg-zinc-50 dark:bg-zinc-900">
      <tr>
        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Name</th>
        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Email</th>
        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Role</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-800 dark:bg-zinc-950">
      <% @users.each do |u| %>
        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900">
          <td class="px-4 py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100"><%= u.name %></td>
          <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400"><%= u.email %></td>
          <td class="px-4 py-3 text-sm text-zinc-600 dark:text-zinc-400"><%= u.role %></td>
        </tr>
      <% end %>
    </tbody>
  </table>
</div>
```

## Alerts / Flash

```erb
<% flash.each do |level, message| %>
  <div role="alert" class="rounded-lg border p-4 text-sm
    <%= level.to_s == 'notice' ? 'border-emerald-200 bg-emerald-50 text-emerald-900 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-200' : '' %>
    <%= level.to_s == 'alert'  ? 'border-red-200 bg-red-50 text-red-900 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-200' : '' %>">
    <%= message %>
  </div>
<% end %>
```

Or better — extract a `FlashComponent` with a `variant:` argument; see `../SKILL.md` Button pattern.

## Toasts (dismiss + auto-hide)

Use Turbo Streams to append; Stimulus to auto-dismiss:

```erb
<div id="toasts" class="fixed bottom-4 right-4 z-50 flex flex-col gap-2"></div>

<!-- toast.html.erb partial (rendered into stream) -->
<div data-controller="toast" data-toast-ttl-value="5000"
  class="flex items-start gap-3 rounded-lg border border-zinc-200 bg-white p-4 shadow-lg dark:border-zinc-800 dark:bg-zinc-900">
  <span class="mt-0.5 h-2 w-2 shrink-0 rounded-full bg-emerald-500"></span>
  <div class="flex-1 text-sm text-zinc-900 dark:text-zinc-100"><%= @message %></div>
  <button type="button" data-action="click->toast#dismiss" class="…" aria-label="Dismiss">×</button>
</div>
```

```js
// app/javascript/controllers/toast_controller.js
import { Controller } from "@hotwired/stimulus"
export default class extends Controller {
  static values = { ttl: { type: Number, default: 5000 } }
  connect() { this.timer = setTimeout(() => this.dismiss(), this.ttlValue) }
  dismiss() { this.element.remove() }
  disconnect() { clearTimeout(this.timer) }
}
```
