# Elementor AnimatePro

A WordPress plugin that adds a suite of custom, fully-styleable
[Elementor](https://elementor.com/) widgets under its own **AnimatePro**
category, plus an admin panel to turn individual widgets on or off.

Every widget is designed so that **everything is editable** from the Elementor
**Style** tab — typography (size/color/family), backgrounds, borders, and
margin/padding — and each widget loads its **own CSS/JS only on pages that use
it**.

## Requirements

- WordPress 6.4+
- PHP 7.4+
- [Elementor](https://wordpress.org/plugins/elementor/) (free)
- The **Content Toggle** and **Animated Off-Canvas** widgets use Elementor's
  *Nested Elements* feature (active by default) for their droppable content; they
  are only registered when that base is available.
- The **Stacked Cards** widget loads GSAP + ScrollTrigger from a CDN (jsDelivr)
  to drive its scroll animation.

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
- **Stacked Cards** — a scroll-driven stacking card deck (GSAP + ScrollTrigger):
  cards pin and stack as you scroll, with configurable peek, scale-down, dimming
  and optional alternating tilt. Cards size to their content (or a fixed height),
  the deck auto-centres in the viewport, respects reduced-motion, and can be
  disabled below a chosen breakpoint. Each card is a structured repeater item
  (icon / subtitle / title / description / button / image, with per-card image
  side and colours).

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
- **Social Share** — an inline row and/or a floating bar; share to Facebook, X,
  LinkedIn, WhatsApp, Telegram, Pinterest, Reddit, Tumblr, VK and email, plus
  **copy-link**, **print** and **native** (Web Share API) actions. Official
  brand colours are on by default (toggleable), and the shared URL/title are
  resolved server-side.

### Header & footer
- **Site Logo** — shows the site's Customizer logo, a custom image, or the site
  title as a text fallback; links home, with width/height, object-fit, alignment
  and hover-opacity controls.
- **Nav Menu** — renders any WordPress menu with desktop dropdown submenus
  (hover / focus) and a mobile hamburger that opens either a **dropdown** or a
  **full-screen overlay** (a setting). Full-screen mode covers the whole viewport
  with configurable menu position and item alignment, plus a fully-styleable
  close button (placement, size, colours, offset). Includes pointer/underline
  hover effects, submenu accordions on mobile, Escape-to-close and scroll-lock.
- **Mega Menu** — a menu bar whose top-level items open large multi-column
  panels. Author each panel as one ordered list (item → columns → links → promo,
  where order = layout) **or** render a saved Elementor template per item. Panels
  can **match the container** (capped by a Max Width and aligned to the menu
  item), span the **full viewport**, or **drop under the item**; they open on
  hover or click, and collapse to an accordion drawer or full-screen overlay on
  mobile.
- **Animated Off-Canvas** — a trigger button opens a panel that **slides in from
  a screen edge** (left / right / top / bottom) over a dimming overlay. Fill the
  panel with **droppable content** (any Elementor widgets, edited live) **or** a
  **saved template**. Slide or fade animation, configurable width/height,
  overlay colour + click-to-close, a styleable close button, plus open-on-load
  and open-via-`#hash`. Esc-to-close, scroll-lock and focus handling included.

### Dynamic (theme building)
These widgets output the **current post's** data, for use on single post / page
layouts (or any post context). In the Elementor editor, when there is no real
post in context, they fall back to a sample / placeholder so they are never
blank on the canvas.
- **Post Title** — the current post title in a chosen HTML tag (h1–h6 / p / span
  / div), optionally linked to the post permalink or a custom URL, with a
  fallback text and full typography / colour / hover styling.
- **Post Featured Image** — the current post's featured image at a chosen
  registered size, optionally linked (post / media file / custom) with a
  caption; width / height / object-fit, border / radius / shadow and hover
  effects (zoom / lift / grayscale / fade). Falls back to a chosen image when
  the post has none.
- **Post Excerpt** — the manual excerpt (falling back to an auto-trim) or an
  auto-trim of the content, capped to a word count, with an optional inline or
  block **Read More** link.
- **Post Content** — the full post content run through the `the_content` filters.
  Guards against infinite recursion when the post is itself built with Elementor.
- **Post Meta Info** — a configurable inline row (or stack) of meta: author (with
  avatar), published / modified date, categories, tags, comment count and
  reading time, each with an optional prefix and icon, joined by a chosen
  separator, with author / term archive links.
- **Post Comments** — the native comment list and reply form (threaded), with
  full styling for the heading, comment items and form fields / submit button.
  The editor shows a styled sample so the layout stays designable.
- **Post Reactions** — a row of emoji reactions (👍 ❤️ 🎉 😮 😢 and more) with
  live counts. Visitors react **without logging in**; one reaction per visitor is
  tracked in the browser (click again to remove, a different one to switch) and
  counts persist to post meta via a nonce-protected AJAX endpoint.
- **Post Pagination** — three modes: **Post Navigation** (previous / next single
  posts, with optional titles and same-category restriction), **Numbered**
  archive page links, or **In-Post Pages** for `<!--nextpage-->` content — all
  fully styleable pill links.
- **Posts** — a query-driven **grid / list / overlay** of posts (the blog-grid
  builder). Query source: **Latest** (post type + taxonomy include/exclude +
  order + offset + exclude-current/sticky), **Manual** (hand-picked) or **Current
  Query** (the current archive / search / blog page). Toggle each card element
  (image, category badge, title, meta, excerpt, read-more) and choose pagination:
  **none**, **numbered**, **load-more** or **infinite scroll** (Load More /
  Infinite are AJAX and apply to the Latest source). Query building and card
  markup live in a shared, Elementor-free class so AJAX-paged cards match the
  first render exactly.
- **Post Rating** — a read-only star rating for the current post, from a **manual**
  editorial score, a numeric **custom field**, or the **visitor average** (the
  aggregate the Post Rating Form collects). Fractional (half-star) precision via a
  clipped fill overlay, star / heart / custom icon, and optional numeric value +
  vote count.
- **Post Rating Form** — an interactive click-to-rate input: visitors click a star
  (1–max) to rate the current post **without logging in**. The vote is saved to the
  shared rating store via nonce-protected AJAX and the average updates live; one
  vote per visitor (localStorage + a per-IP-per-post guard), after which the stars
  lock with a thank-you. Feeds the **Post Rating** widget's Visitor Average.
- **Advanced Posts** — the Posts grid plus **AJAX filter tabs** and two extra
  layouts. Pick a taxonomy and an "All + terms" tab bar filters the grid in place
  (each tab re-queries via the shared endpoint and swaps the cards). Five layouts:
  **Grid**, **List**, **Overlay**, **Featured** (first card large — or a full-width
  text hero when the lead post has no featured image) and **Masonry**.
  Reuses the Posts engine for the query, cards and styling; pagination is Load More
  or Infinite scroll.
- **Posts Timeline** — the Posts loop laid down a vertical timeline: a line with
  dated nodes and **year dividers**, in an **Alternating** (centered zig-zag) or
  **One-sided** (left rail) arrangement (alternating collapses to one-sided on
  mobile). Reuses the Posts engine for the query, cards and card styling, and adds
  a Timeline style section (line, dot, date pill, year band). Latest query, no
  pagination.
- **Posts Read Later** — a no-login "save for later": visitors bookmark posts to
  their own browser (localStorage, no account). One widget, two **Modes** — a
  **Save button** toggle for the current post (Save ↔ Saved), or a **Reading List**
  of the visitor's saved posts as cards (reusing the Posts card markup), each with
  a remove ×, an empty state and an optional "Clear all". The list is filled
  client-side via a nonce-protected AJAX endpoint returning the same
  server-rendered cards (publish-only); the button, list and saved-count stay in
  sync across the page.
- **Video Story** — a query-driven grid of portrait video "story" cards (poster +
  gradient + category badge + title + byline + play & duration). **Hovering** plays
  the card's video as a **muted background preview**; **clicking the play button**
  unmutes it, **hides the overlays** and shows native controls (a close × restores
  the card). Source is any post type and defaults to the bundled **Video Story**
  post type (enable it on the Extensions page; each story stores a self-hosted
  video in `_eap_video_url` plus a featured-image poster), with a widget-wide
  **Fallback Video** for anything missing. Self-hosted MP4 / WebM.
- **Posts Slider** — the Posts loop as a **Swiper carousel** (the first of the
  Slider group). Same query controls, and the slides reuse the Posts card markup
  and styling, so **Card** and **Overlay** layouts come straight from the Posts
  widget. Per-breakpoint slides-per-view (desktop / tablet / mobile), space
  between, speed, loop, centered slides, autoplay (delay + resume-after-
  interaction), styleable **arrows** and **pagination** (bullets / fraction /
  progress bar). Uses Elementor's bundled Swiper.
- **Breaking News Slider** — a news **ticker**: a pinned label ("Breaking News",
  with an optional icon and a **pulsing live dot**) beside headlines that
  auto-advance one at a time, **horizontally** or **vertically**. Label can sit
  left or right; each headline links to its post with an optional thumbnail,
  category badge and date; long headlines ellipsise rather than breaking the bar.
  Autoplay with delay + **pause on hover**, loop, speed, and optional arrows
  (which rotate to up/down in vertical mode). Uses Elementor's bundled Swiper.
- **Category Slider** — a carousel of **taxonomy terms** (not posts): each slide
  is a category card with an image, name and post count, linking to the term
  archive. **Overlay** (text on the image) or **Card** (text below) layout. Since
  core terms have no image field, the image resolves in order: a **term-meta key**
  you name (an attachment ID or URL, so ACF / theme category-image fields work) →
  the **newest post in that category's featured image** (zero setup) → a
  widget-wide **Fallback Image** → a gradient placeholder. Full term query
  (taxonomy, count, order, hide-empty, include/exclude) plus the usual slider
  options, arrows and pagination.
- **Video Box Slider** — a carousel of hand-authored **video boxes** (poster +
  play button + title/description, with optional badge and duration). Supports
  **YouTube, Vimeo and self-hosted** MP4/WebM; clicking play opens the video in a
  **lightbox** (Esc / backdrop / × to close) or swaps it in **inline**. The
  provider and video ID are parsed server-side, so the browser only assembles an
  embed URL. YouTube boxes use YouTube's own thumbnail when no poster is set, and
  the slider's autoplay pauses whenever a video starts.

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

**Current version:** 1.20.51
