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
- **Filterable Slider** — the Advanced Posts **filter tabs** on top of a Posts
  Slider carousel: pick a taxonomy and an "All + terms" tab bar re-queries and
  rebuilds the slides in place. Filtering rides the **same AJAX endpoint** as
  Advanced Posts (via the shared `filter_terms` field) rather than hiding
  pre-rendered slides — so each tab returns the latest posts *of that term*
  instead of leaving a tab empty. Card / Overlay layouts and all card styling
  come from the Posts widget; full slider options, arrows, pagination and an
  empty-state message.

### Advanced
- **Loop Grid** — a query-driven grid that repeats **a saved Elementor template**
  once per post, instead of the plugin's fixed card markup. Design the item
  yourself out of the **Dynamic** widgets above (Post Title, Post Featured Image,
  Post Excerpt, Post Meta Info, …) and each iteration renders it with that post as
  the current post, so those widgets resolve per item. Full query controls,
  responsive columns and gaps, item background/border/radius/shadow/hover, and
  optional numbered pagination. Guards against a template that contains a Loop
  Grid recursing forever.
- **Loop Carousel** — the same per-post template rendering as **Loop Grid**, but
  as a Swiper carousel: each slide is your saved template rendered with that
  post's context. Same query controls, plus per-breakpoint slides, space
  between, speed, loop, centered slides, autoplay (delay + pause on hover),
  arrows and pagination. Both loop widgets share a template-ID-keyed recursion
  guard, so a template that points back at either one can't loop forever.

## Extensions

Extensions are features that attach to *existing* widgets rather than adding new
ones. They're toggled on the plugin's **Extensions** page (stored in the
`eap_extension_states` option) and are simply not registered when off.

- **Image Masking** — adds an **Image Masking** section to the Advanced tab of
  every element, masking its images with a **clip-path shape** (20 presets, or
  your own `polygon()` / `circle()` / `path()` value) or an uploaded **mask
  image** with size / position / repeat — each with a separate **Hover** state
  and a transition. Pure CSS: no extra stylesheet or script, and both the
  `-webkit-` and unprefixed properties are emitted for Safari. It only ever
  styles `img`, so it is inert on elements without one.
- **Advanced Tooltip** — adds an **Advanced Tooltip** section to the Advanced tab
  of **every element** — widgets, containers, sections and columns alike. The
  tooltip content can be **text, an icon, an image or a shortcode**; choose its
  position (top / bottom / left / right), trigger (hover or click), arrow,
  distance and max width, and style the background, text, typography, padding,
  radius, border, shadow and z-index. The content is rendered server-side onto a
  `data-eap-tooltip` attribute and the tooltip node is built from it, so one
  mechanism works for every element type; showing/hiding is CSS.
- **Conditional Display** — adds a **Conditional Display** section to the Advanced
  tab of **every element**, and shows or hides it per visitor. Choose **Show** or
  **Hide**, whether to match **all** conditions or **any**, then add as many
  conditions as you like: **Login Status, User Role, Specific User, Page Type,
  Post Type, Specific Post/Page, Taxonomy Term, Date & Time** (after / before /
  between), **Day of Week, Browser, Operating System, Device, Query String, URL /
  Referrer** and **Country** — each invertible with *is* / *is not*.
  The element's markup is suppressed entirely rather than hidden with CSS, so
  restricted content never reaches the page source. Conditions are skipped in the
  editor so hidden elements stay editable.
  *Country* reads a header a geo-aware proxy already set (Cloudflare, CloudFront,
  or `GEOIP_COUNTRY_CODE`) — **no external lookup is made and no visitor IP
  leaves the site**; supply your own via the `eap_conditional_display_country`
  filter.
  **Caching:** conditions are evaluated server-side, so exclude pages that use
  visitor-specific conditions from full-page caching, or every visitor gets
  whichever variant was cached.
- **Interactive Animations** — adds an **Interactive Animations** section to the
  Advanced tab of **every element** and animates it on interaction. Trigger:
  **Scroll** (progress linked to the element's travel through the viewport, with
  a configurable start/end range and optional play-once), **Hover**, **Mouse
  Move** (parallax, tracking the viewport or just the element) or **Click**
  (toggle). Combine **Translate X/Y, Rotate, Scale, Opacity** and **Blur**, with
  duration, delay and easing. Every control writes a CSS variable and the
  stylesheet composes them, so Hover needs no JavaScript at all and Scroll costs
  a single custom-property write per frame from one shared rAF pass. Respects
  `prefers-reduced-motion` by resetting to the resting state.
- **Hover Interaction** — adds a **Hover Interaction** section to the Advanced tab
  of **every element**, with a **Normal** and a **Hover** value for each effect:
  **Opacity**, **Filter** (blur, contrast, grayscale, invert, saturate, sepia),
  **Offset** (top/left) and **Transform** (rotate X/Y/Z, scale X/Y, skew X/Y),
  plus duration, delay, easing and perspective. **Cursor Tilt** leans the element
  in 3D towards the pointer (hover only, adds to the rotation above, reversible).
  **Show Hover State in Editor** holds the hover state open on the canvas so it
  can be tuned without keeping the mouse still.
  Each control writes a CSS variable and the stylesheet composes them once, so
  hover itself needs no JavaScript — the tilt is the only scripted part, and it
  binds by document-level delegation. Elementor's own `--e-transform-*` variables
  are folded into the same composition, so its Transform controls and this
  extension stack instead of overwriting one another. Respects
  `prefers-reduced-motion` (keeps the hover result, drops the travel) and skips
  the tilt on touch pointers.
  *Note:* `scaleZ` only affects 3D-transformed descendants and `skewZ` does not
  exist in CSS, so neither is offered.
- **Video Story Post Type** — registers the bundled Video Story post type used by
  the Video Story widget (see above).

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
- **Extensions that need a class on the element must use `prefix_class`, not
  `add_render_attribute( '_wrapper', … )`.** On the editor canvas the element
  wrapper is created by Backbone, and its `className()` is only
  `elementor-element elementor-element-edit-mode <uniqueID>` — anything PHP adds
  to `_wrapper` is absent until the page is reloaded. `prefix_class` is applied
  by *both* `element-base.php` (front end) and the editor view (which swaps the
  class live as the control changes), so it is the only mechanism that behaves
  the same in both. Corollary: state carried this way is readable at event time,
  so prefer document-level delegation over a scan at render time — toggling a
  control in the editor changes the class without re-rendering the element.
  `elementor-element-edit-mode` is also a reliable "canvas only" hook for
  editor-only styling.
- **Slider widgets must declare Elementor's Swiper stylesheet.** Elementor only
  *registers* `e-swiper`; it is enqueued when a widget asks for it via
  `get_style_depends()`. Wrap a slider's dependencies in
  `eap_with_swiper_style()` (on `EAP_Widget_Base`) — without it the slider gets
  no Swiper CSS on a page that contains no Elementor carousel, and every slide
  stacks full-width. The `swiper` *script* is added separately, guarded by
  `wp_script_is( 'swiper', 'registered' )`.

---

**Current version:** 1.20.62
