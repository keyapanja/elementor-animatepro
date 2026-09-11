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
- **Toggle Switch** — a standalone two-state control that drives **other**
  elements on the page: **show one target and hide the other** (a pricing table
  built as two ordinary containers), **toggle a class** on anything a selector
  matches, or **dark mode** (a class on `<html>`, remembered). Rendered as a
  switch with labels either side, or as two pills. Optionally remembers the
  choice, and switches sharing a storage key stay in step — including on the
  same page, where the browser's own `storage` event does not fire.
  *Where this differs from Content Toggle:* that one **contains** its states as
  nested droppable containers; this one contains nothing and points at elements
  elsewhere. Reach for this when the two states are sections you have already
  built, when they are not adjacent, or when the thing being switched isn't
  content at all. The control is a real `<button role="switch">`, so it is
  focusable, operable with Space/Enter and announced correctly.
- **Image Accordion** — horizontal or vertical, hover or click.
- **Flip Box** — two faces, each built from an icon or image, title and text
  (with a button, title link or whole-side link on the back) or a **saved
  template**, that flip left / right / up / down, zoom in or out, or fade — on
  hover or click, with optional 3D depth, speed, easing and perspective.
  Deliberate differences from the usual flip box: **height by construction** —
  both faces share one CSS grid cell, so the box is always as tall as its
  taller side with nothing measuring (the usual absolute stacking clips long
  content and needs JavaScript plus a resize handler); a "fit the side
  showing" mode resizes to the visible face, and a fixed height is there when a
  layout needs one. **One state for every input** — mouse hover, a tap on touch
  screens (where hover does not exist), and the keyboard: the box is
  focusable, Enter or Space flips, Escape flips back. The face that is not
  showing is made `inert`, so focus never lands on an invisible back-side
  button. With Click, a click on the front flips and a click on the back
  follows its link. **Stretch to column height** lines up flip boxes side by
  side in a row. Reduced motion gets a short crossfade instead of a rotation,
  and a template that contains the flip box it sits in is refused rather than
  rendered recursively.
- **Advanced Accordion** — text or saved-template items, one open at a time
  (**Accordion**) or many (**Toggle**), with open/closed icons on either side,
  per-item title icons and default-open state, **linkable item IDs**
  (`/page/#shipping` opens that item and scrolls it clear of a fixed header),
  keep-in-view, URL updates, expand/collapse-all, and optional **FAQPage
  structured data** built from the text items. **Built on native
  `<details>` / `<summary>`**, so it works with no JavaScript at all (accordion
  mode puts the items in one `name` group, which the browser keeps exclusive),
  Chrome's find-in-page can open a closed item holding the match, and keyboard
  and screen-reader behaviour come from the platform rather than hand-built
  ARIA. The script adds height animation, and takes the `name` group over so
  the item closing animates instead of snapping shut; its `toggle` listener
  still keeps the accordion exclusive when something else opens an item.
  *Where this differs from Elementor's own Accordion:* that one makes every
  item a droppable container — use it to build items out of widgets, and this
  one for text, FAQs and reused templates.
- **Price Box** — a pricing card (header + icon, price with discount/period,
  feature list with tooltips, corner/circular ribbon, CTA + footer note).
- **Advanced Pricing Table** — a multi-plan **comparison**, where every plan is
  scored against one shared feature list. **Plan Cards** or a **Comparison
  Table** with a leading column of feature names; a highlighted plan with a
  badge; and a built-in **billing toggle** that swaps every plan between its
  price and an alternate (monthly / yearly), with no request.
  Because Elementor repeaters cannot nest, each plan's values are one per line,
  positionally matched to the feature list — the same approach Data Table uses.
  `yes` / `no` render a tick or a cross; anything else renders as text, so
  "10 GB" or "Unlimited" work too.
  *Where this differs from Price Box:* that widget is a single card, and the way
  to build a table with it is to drop several into columns — which looks right
  until the feature lists differ, and then the rows stop lining up. Here the rows
  align **by construction**: the whole table is one CSS grid and each column is a
  `subgrid`, so a value that wraps onto two lines grows that row in *every* plan
  rather than knocking one column out of step. Use Price Box for a lone plan.
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
- **Filterable Gallery** — hand-picked media with **filter tabs, search, a
  lightbox and Load More**. Three layouts (**Overlay** caption over the image,
  **Card** caption below, **Harmonic** cards with wide feature tiles) over
  **Grid** or **Masonry**. The tabs are **derived from the categories typed on
  the items**, not maintained as a second list: EA's equivalent has a tab
  repeater plus a free-text control name per item that the author keeps in sync
  by hand, where one typo silently drops an item out of every tab. Here a tab
  can never open onto an empty grid, and the counts are exact. **Load More
  batches follow the active filter** — switching to a tab with ten matches shows
  the first batch of those ten, not whichever of them happened to fall in the
  first batch overall — and the **lightbox walks only what is currently
  visible**, so the arrows never wander into filtered-out or not-yet-loaded
  items. Items can carry a video URL, which the lightbox plays instead of the
  image (a file becomes a `<video>`, anything else an embed iframe). The filter
  reflow is `EAPFrontend.flipFilter()` in `core.js`, shared with Portfolio and
  Filterable Posts. Reach for **Image Gallery** when the set needs no filtering,
  and **Portfolio** or **Filterable Posts** when the content already lives in
  the database as posts with terms.
- **Image Box**, **Image Box Slider**, **Image Gallery**, **Image**,
  **Image Comparison**, **Image Hotspot**, **Text Hover Image**.

### Sliders & testimonials
- **Advanced Slider**, **Advanced Testimonial Slider**, **Testimonial Slider**,
  **Testimonial Box**, **Brand Slider**.
- **Nested Slider** — a slider whose slides are **Elementor containers**, so
  a slide holds anything you can build: a hero, a card grid, a form, another
  widget. Slides per view, slides per step and gap per breakpoint (taken from
  the site's active Elementor breakpoints), slide or fade, centred slides, auto
  height, loop, autoplay with pause-on-hover and stop-after-interaction,
  arrows over or beside the slides, and dots, a fraction or a progress bar.
  Runs on Elementor's bundled Swiper on the page; **in the editor Swiper does
  not run** — its transforms and loop clones would sit inside Elementor's
  editor views and duplicate droppable containers — so the slides sit side by
  side in a scroll-snap strip, each one visible and droppable. That strip is
  also what a visitor gets if the script never loads, so no slide is ever
  unreachable. **Loop clones are started again**: Swiper copies slides with
  `cloneNode()`, which copies our "already set up" flags too, so a widget of
  ours inside a copy would stay dead; the script clears those flags and runs
  our modules and Elementor's handlers over each clone. Autoplay has a **pause
  button** (moving content needs one under WCAG 2.2.2), holds still while focus
  is inside a slide, and is off for visitors who ask for reduced motion.
- **Vertical Marquee** — columns of cards, images or text that scroll
  continuously, up, down, or alternating column by column: the "wall of
  testimonials" effect. Speed is in **pixels per second** — each column is
  measured and timed from its own height, so a column with more in it moves at
  the same pace instead of faster. The loop is **seamless**: each column is its
  list plus an identical copy, animated by exactly one list height, and a short
  column is topped up first so the seam never shows as a gap. Columns are dealt
  out in the browser, because how many there are is a responsive setting the
  server cannot resolve per device; items are shared out across the columns, or
  every column shows them all. Screen readers get **each item once** — the loop
  copies are hidden from them and taken out of the tab order. Hover and
  keyboard focus pause it, there is a **pause button** (moving content needs
  one under WCAG 2.2.2), reduced motion turns it into columns you scroll
  yourself, and without the script it is a plain scrollable list.

### Text & UI
- **Animated Text**, **Advanced Animated Text**, **Advanced Button**,
  **Icon Box**, **Social Icons**, **Progress Bar**, **Timeline**,
  **Services Tabs**, **One Page Nav**, **Team**, **Parallax Sections**.
- **Scroll Elements** — a sticky section nav beside its own scrolling content,
  with the current section highlighted as you read and smooth scrolling when a
  nav item is clicked. Write the sections in the widget (title, optional short
  nav label, icon, rich content); the nav, the anchors and the wiring are
  generated. Nav on the **left, right or above**, sticky with a configurable
  offset, and a scroll offset so a fixed header never covers the heading landed
  on. Stacks to one column below a chosen width.
  *Where this differs from One Page Nav:* that widget is a **nav only** — you
  build the sections yourself, give each a CSS selector, and type those
  selectors in, so it points at content it does not own. This one owns the
  content, so the nav can never point at a section that was renamed or deleted
  and there is nothing to keep in sync. Use One Page Nav for sections built
  elsewhere on the page; use this for a self-contained document like terms, a
  privacy policy or a spec.
- **Breadcrumbs** — a Home → current-page trail for every context WordPress
  has: page hierarchies, posts through their category ancestors, custom post
  types through their archive and hierarchical taxonomy, attachments, term
  archives with ancestors, post type archives, author, day/month/year, search
  and 404. Rendered as a real `<nav><ol>` with `aria-current="page"` on the last
  crumb, plus optional **Schema.org BreadcrumbList JSON-LD** (off by default when
  Yoast or Rank Math is active, since they emit their own). Prefix as a home
  icon or text, text or icon separator, editable archive labels, optional
  quoting, and trimming of long current titles. The **archive crumb** can be
  switched off or pointed at a **custom link and label** — a landing page rather
  than the raw archive, or one for a post type that has no archive at all.
  Deliberate departures from the usual snippet: the post type archive comes
  from `get_post_type_archive_link()` (right under plain permalinks, honours
  `has_archive`), products get the Shop page, the category shown for a post is
  the SEO plugin's **primary term** when set and otherwise the **deepest** one,
  and hierarchical custom taxonomies get the same ancestor walk as categories.
  The trail is built as data first and rendered second, which is what lets one
  trail feed both the list and the JSON-LD.
- **Table of Contents** — discovers the H1–H6 already on the page and lists
  them, nested by level, with smooth scrolling, the current heading highlighted
  as the reader moves, a minimise button, and an optional **floating box**
  pinned to either side behind a tab. Choose the levels, scope the scan to a
  container selector, exclude selectors, pick numbers (1, 1.1, 1.2 — pure CSS
  counters), bullets or no markers, flat or nested view, and **collapse
  sub-items** so a heading's children show only while the reader is inside
  it. Headings get ids generated from their text (existing ids are kept,
  duplicates de-duplicated) plus a `scroll-margin-top` matching the scroll
  offset, so a hard reload onto `#heading` also clears a fixed header. Hides
  itself on the front end below a minimum heading count; in the editor it shows
  a placeholder instead so it stays configurable.
  *Where this differs from One Page Nav and Scroll Elements:* One Page Nav
  points at sections you name by typing a selector per item; Scroll Elements
  owns its sections. This widget authors nothing — it reads what is there and
  follows it. Its scroll-spy is `EAPFrontend.scrollSpy()` in `core.js`, now
  shared with Scroll Elements.
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
- **Author Box** — the current post's author as a full profile card: avatar,
  name (linked to their archive or website), an optional tagline read from a
  user-meta key you name (WordPress profiles have no job-title field), the bio
  from their profile, post count, social links and a button. Or pin it to a
  chosen user for an "About the editor" block; on an author archive it shows
  that author. Avatar left, right or on top per breakpoint, in circle, rounded
  or square. **Social links come from the user's profile, not the widget** —
  the contact fields SEO and profile plugins add, plus the Website — matched to
  brand icons by field name, so X, LinkedIn, GitHub, Mastodon, YouTube and the
  like light up without setup, and an unrecognised network still gets a
  generic link icon instead of vanishing. A field may hold a full URL or a
  bare domain (`x.com/you` gets `https://`); an `@handle` is skipped rather
  than turned into a dead link, and any scheme other than `http(s)` / `mailto`
  — `javascript:` included — never reaches the page. Icons go through
  Elementor's own renderer, so they work with its inline-SVG icon mode on or
  off.
  *Where this differs from Post Meta Info and Team:* Post Meta Info shows the
  author as one inline item in a meta row; Team is people you type in. This
  resolves the author from the post.
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
- **Filterable Posts** — the instant counterpart to Advanced Posts. It loads one
  pool of posts and filters **in the browser**, with the surviving cards sliding
  into their new positions (a FLIP transition, so the browser animates one
  transform per card rather than being asked to move `top`/`left`). No round trip
  per tab. Layouts: **Grid** and **Masonry**, with an optional **search box**.
  Because it filters what it already has, the tabs are built **from the terms the
  loaded posts actually carry** — so a tab can never open onto an empty grid (the
  failure mode a re-querying filter has when a term has no post inside the loaded
  range), and the counts shown on each tab are exact rather than term totals.
  Choose it for a curated set that should feel instant; choose Advanced Posts to
  page through a large archive.
- **Featured Posts** — an **editorial hero block**: one or two lead stories given
  real visual weight, with the rest as supporting items. Arrangements: **Hero +
  Side List** (hero either side, supporting items as a stacked list or small
  cards), **Two Co-Leads**, and **Mosaic** (hero beside a 2-up grid). The hero
  can be **Overlay** (text on a scrim over the image) or a card, and hero and
  supporting items have **separate element toggles**, so the hero can carry an
  excerpt and read-more while the list stays compact.
  Source is what makes it "featured": **Sticky Posts** (the posts you marked
  "stick to the top of the blog", falling back to the latest so the block is
  never empty), a **Meta Flag** (any custom field that is set and truthy — pairs
  with an ACF true/false field), **Hand-picked**, or Latest. Sticky, Meta and
  Hand-picked all resolve to an ID list and go through the shared publish-only
  path, so none of them can surface a draft or private post.
  Both sections render through `EAP_Posts_Query::render_card()` with *different*
  display specs and their own `.eap-posts--{layout}` modifier, so there is no
  duplicated card markup. Stacks to one column below a configurable width, hero
  first. CSS only, no JavaScript.
  *Not to be confused with* Advanced Posts' **Featured layout**, which is a wide
  lead card above a uniform grid — use that one if that is the shape you want.
- **Archive Title** — the heading for whatever archive is being viewed: category,
  tag, taxonomy, author, date, post-type archive, **search results**, the blog
  page or a **404** (the last three are cases WordPress's own archive title does
  not cover — they all fall through to a bare "Archives" there). The **prefix**
  ("Category:", "Tag:", "Author:"…) can be kept, hidden or replaced, and it is a
  **separate element from the title**, so the two can be given different
  typography and colour, or the prefix put on its own line. Optionally shows the
  **result count** and the archive **description** (term description, author bio
  or post-type description), with a word trim and max width.
  It does not reimplement WordPress's logic: `get_the_archive_title()` computes
  the title and prefix separately and passes both to its filter, so the widget
  captures them there instead of parsing the composed string. In the editor,
  where there is no archive context, it previews against a real category rather
  than rendering empty. CSS only, no JavaScript.
- **Current Date** — today's date and/or the current time. Presets for date and
  time (previewed with live values in the dropdown), or any **PHP date format**,
  in the **site's timezone or any IANA zone** — so a contact page can show
  another office's local time. Optional before/after text, icon and HTML tag.
  It refreshes **in the browser**, because a server-rendered "current date" is
  wrong the moment the page is cached — on a fully-cached site the visitor can
  be shown yesterday. PHP renders the value (correct without JavaScript, and it
  is what the `datetime` attribute carries) and the script re-renders on load,
  then ticks at an interval derived from the format: every second only when the
  format actually shows seconds, otherwise slowly enough to just catch midnight.
  The script formats with the *same PHP format string*, so the two renderers
  cannot drift; timezone-correct fields come from `Intl.DateTimeFormat` rather
  than date arithmetic, which is what makes an arbitrary zone and its DST right
  without shipping a timezone database.
- **Portfolio** — image-led project tiles for the bundled **Portfolio post type**
  (or any post type), in a **Grid** or **Masonry** layout. Each tile is an image
  with an overlay — title, categories, and optionally the **client** and
  **completed date** from the project fields — revealed on hover or shown
  always, plus action buttons for a **lightbox** and an outbound link. Tiles can
  link to the project page, to the **Project URL** on the project, or nowhere.
  **Filter tabs** are derived from the categories the loaded projects actually
  carry, so a tab can never open onto an empty grid and the counts are exact.
  Defaults to **manual ordering** (the Order field), since portfolios are curated
  more often than chronological. The filter reflow is
  `EAPFrontend.flipFilter()` in `core.js`, shared with Filterable Posts rather
  than reimplemented.
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
- **Category Showcase** — the same taxonomy terms as a **static showcase grid**,
  the counterpart to Category Slider. Three layouts: **Grid**, **Masonry** (CSS
  columns, so tiles keep their natural image heights) and **Featured**, where the
  first term spans the full width as a lead tile with its own height. **Overlay**
  or **Card** style, with post count, optional term description, content
  alignment, and Zoom / Lift hover effects. Term images resolve through the same
  four-step chain as Category Slider — the two share
  `EAP_Widget_Base::eap_get_term_image()`, so images configured for one work in
  the other. A term that resolves no image at all still reads as a deliberate
  tile: it gets the gradient placeholder with its content centred rather than an
  empty box. CSS only, no JavaScript.
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
  position (top / bottom / left / right), trigger (hover or click), and an
  **animation** — Shift Away, Shift Toward, Scale, Fade or Perspective — with
  **duration** and **delay out** (how long the bubble lingers, so a link inside
  it stays reachable). The **arrow** can be **sharp or round**. Set distance and
  max width, and style the background, text, typography, padding, radius,
  border, shadow and z-index. The content is rendered server-side onto a
  `data-eap-tooltip` attribute and the tooltip node is built from it, so one
  mechanism works for every element type; showing/hiding is CSS.
  The transform is composed from CSS variables — placement supplies the centring
  and shift direction, the animation describes only the hidden state, and one
  rule returns everything to rest — so 4 positions x 5 animations cost five short
  rules instead of twenty pairs.
  *Known limitation:* the tooltip is a child of the element, so an ancestor with
  `overflow: hidden` will clip it. Moving it to a body-level portal would put it
  outside Elementor's `{{WRAPPER}}`-scoped CSS, so it is not a one-line change.
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
- **Custom Cursor** — replaces the pointer while it is over an element. Adds a
  **Custom Cursor** section to the Advanced tab of **every element**, with a
  **Normal** and a **Pointer** state (the latter used over links, buttons and
  fields inside the element), each of which can be **Default, Circle, Icon,
  Image** or **SVG Code** with its own size, colour, background, border, radius
  and opacity. **Enable Trail** adds one of eight motion effects — Ink Trail,
  Trail Particles, Phantom Smoke, Spirit Echo, Glow Blocks, Chroma Orbs, Frost
  Sparkles, Dot Comet — with a trail colour and size. Plus follow speed, blend
  mode, z-index and an option to keep the real cursor visible.
  One shared cursor node is created per page and adopts whichever element the
  pointer is over, so many enabled elements still cost one node and one
  animation frame loop — which runs only while a cursor is on screen. Pasted SVG
  is sanitised through a `wp_kses` allowlist (no scripts, event handlers or
  links). Skipped entirely under `prefers-reduced-motion` and on touch pointers.
  *Note:* front end only by design — the editor canvas keeps the real pointer so
  the element stays draggable and resizable.
- **Portfolio Post Type** — registers the bundled `eap_portfolio` post type used
  by the Portfolio widget, with its **own Project Categories taxonomy** (kept
  separate from the blog's categories, so project types don't leak into the
  blog's archives) and a **Project Details** box holding the client, project URL
  and completed date. Supports page attributes, so projects can be hand-ordered.
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

**Current version:** 1.20.84
