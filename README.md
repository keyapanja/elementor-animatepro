# Elementor AnimatePro

A WordPress plugin that adds a suite of custom, fully-styleable
[Elementor](https://elementor.com/) widgets under its own **EAP Elements**
category, plus an admin panel to turn individual widgets on or off.

Every widget is designed so that **everything is editable** from the Elementor
**Style** tab — typography (size/color/family), backgrounds, borders, and
margin/padding — and each widget loads its **own CSS/JS only on pages that use
it**.

## Requirements

- WordPress 6.4+
- PHP 7.4+
- [Elementor](https://wordpress.org/plugins/elementor/) (free)
- The **Content Toggle** widget uses Elementor's *Nested Elements* feature
  (active by default); it is only registered when that base is available.

## Widgets

### Content & layout
- **Countdown** — flip / boxed / minimal / circle / panel layouts, with a fixed
  **due-date** mode or an **evergreen** per-visitor timer (remembered by
  cookie/session), plus an on-expire action (message / redirect).
- **Content Toggle** — a nested two-state switch (e.g. Monthly / Yearly) where
  each state is a droppable Elementor container; supports `#hash` deep-linking
  to open a specific option.
- **Image Accordion** — horizontal or vertical, hover or click.
- **Price Box** — a pricing card (header + icon, price with discount/period,
  feature list with tooltips, corner/circular ribbon, CTA + footer note).
- **Data Table** — build in-panel (columns + rows) or paste **CSV/TSV**, with
  optional **sort / search / pagination** and responsive horizontal-scroll or
  stacked-card modes.
- **Feature List** — icon/image + title + description items with an optional
  **connector line**, plus per-item colour overrides.
- **Multi Buttons** — a row of individually-styled buttons (separate or joined).

### Media
- **Sticky Video** — YouTube / Vimeo / self-hosted; **floats to a corner while
  playing** when scrolled out of view (single synced player, closable).
- **Image Box**, **Image Box Slider**, **Image Gallery**, **Image**,
  **Image Comparison**, **Image Hotspot**, **Text Hover Image**.

### Sliders & testimonials
- **Advanced Slider**, **Advanced Testimonial Slider**, **Testimonial Slider**,
  **Testimonial Box**, **Brand Slider**.

### Text & UI
- **Animated Text**, **Advanced Animated Text**, **Advanced Button**,
  **Icon Box**, **Social Icons**, **Progress Bar**, **Timeline**,
  **Services Tabs**, **One Page Nav**, **Team**, **Parallax Sections**.

## Admin

The plugin's admin page lets you enable or disable each widget individually
(stored in the `eap_widget_states` option). Disabled widgets are simply not
registered with Elementor, so they add no overhead.

## Development

- Widgets extend `EAP_Widget_Base` and are registered in
  `EAP_Elementor::register_widgets()` (the single source of truth).
- Each widget declares its assets via `get_style_depends()` /
  `get_script_depends()`; shared runtime lives in `assets/js/core.js`
  (`window.EAPFrontend`) and `assets/css/core.css`.
- Assets are cache-busted by the `EAP_VERSION` constant — bump it (plugin header
  **and** the `define()` in `elementor-animatepro.php`) whenever you change
  CSS/JS.

---

**Current version:** 1.20.20
