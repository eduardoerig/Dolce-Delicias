# tailwind-skill

A Claude Code skill for building production-grade Tailwind CSS UIs inside Rails apps. Opinionated about the stack — **Rails + tailwindcss-rails + Propshaft + Stimulus + Turbo**. No React, no JSX.

## What it does

- Guides component-by-component implementation against a consistent philosophy (utility-first, token-driven, accessible, dark mode from day one).
- Curates 20+ component libraries (HyperUI, Flowbite, Preline, DaisyUI, Headless UI, and others) with licensing notes and copy-paste workflows.
- Covers Tailwind v4 (CSS-first `@theme`) and v3 (`tailwind.config.js`).
- Ships canonical Rails patterns: ViewComponent, Stimulus controllers, Turbo gotchas, dark-mode toggle without FOUC.
- Includes a compliance audit mode (`/tailwind audit`) that scores ten categories and generates a report.

## Structure

```
SKILL.md                          — entry point, philosophy, decision tree, audit procedure
references/
  tokens-and-theme.md             — v4 @theme, v3 config, OKLCH, dark-mode pairings
  component-libraries.md          — full catalog, WebFetch recipes, React→ERB porting
  component-patterns.md           — buttons, forms, cards, modals, menu, tabs, nav, tables
  layout-and-responsive.md        — breakpoints, container queries, page shells, z-index
  rails-integration.md            — tailwindcss-rails, ViewComponent, Stimulus, dark-mode toggle
  anti-patterns.md                — 20 common mistakes with bad/good/grep
```

## Install

Drop the `tailwind-skill/` directory into your Claude Code skills folder (typically `~/.claude/skills/` or a project-level `.claude/skills/`). Claude Code auto-loads skills from these locations.

## Invoke

- Mention any trigger in conversation: "tailwind", "dark mode", "responsive", "landing page", "dashboard", "daisyui", etc.
- Run audit mode explicitly: `/tailwind audit [path or URL]`

## When not to use this skill

- Standalone single-file HTML demo → use a general UI skill.
- React / JSX projects → this skill assumes ERB + Stimulus.
- Material Design spec adherence (Compose, Flutter) → use a Material skill.

## License

MIT.
