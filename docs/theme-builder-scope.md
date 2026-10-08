# Theme Builder — Scope Draft

Status: **approved 2026-10-07**. Phase 1 is being built now. The decisions that
were open are recorded in section 7.

---

## 0. Where we stand today

- The **Theme Builder admin tab exists but is a placeholder** — one heading and a
  sentence saying the section will be designed next.
- There is a 534-line `includes/class-eap-theme-builder.php` holding a
  header/footer prototype (an `eap_template` post type, a conditions meta box,
  header injection on `wp_body_open`, and a stylesheet that hides the theme's own
  header by guessing at selectors like `#masthead`, `.site-header`,
  `[role="banner"]`). **It is dead code** — the only file that loads it is
  `class-eap-plugin.php`, which nothing requires. At runtime the `eap_template`
  post type does not exist.
  - Recommendation: mine it for ideas, don't build on it. Its two weakest points
    — injecting a header into whatever markup the theme emitted, and hiding the
    theme's header with a guessed selector list — are the exact parts worth
    replacing.
  - Two widgets (Off-Canvas, Parallax Sections) already list `eap_template` in
    their template pickers, so they will start seeing templates the moment the
    post type goes live.
- **Building blocks already shipped:** 91 widgets, including Site Logo, Nav Menu,
  Mega Menu and Animated Off-Canvas for headers, and 22 dynamic widgets (Post
  Title, Featured Image, Excerpt, Content, Meta Info, Comments, Pagination,
  Social Share, Archive Title, Breadcrumbs, Author Box, Post Rating, Posts, Loop
  Grid, Loop Carousel and more).

---

## 1. Template types

Each is a layout built in Elementor and assigned to parts of the site by
conditions.

| # | Type | What it replaces |
|---|------|------------------|
| 1 | **Header** | The theme's header, site-wide or per section |
| 2 | **Footer** | The theme's footer |
| 3 | **Single** | The layout of one piece of content — post, page, portfolio, video story, any post type |
| 4 | **Archive** | Category, tag, custom taxonomy, post-type archive, author, date, and the blog page |
| 5 | **Search Results** | The search results page |
| 6 | **404** | The not-found page |
| 7 | **Loop Item** | The repeating card used by Loop Grid / Loop Carousel. These point at plain Elementor library templates today; making it a real type gives it its own editor context and a preview against a real post |

**Also approved, for later phases:**

| # | Type | Notes |
|---|------|-------|
| 8 | **Popup** | Its own subsystem: triggers, timing, close rules, display-once logic. Phase 4 |
| 9 | **Single Product** | WooCommerce product page. Phase 5 |
| 10 | **Product Archive** | WooCommerce shop and category listings. Phase 5 |

Off-canvas and Mega Menu content already work off Elementor's library and are
left alone.

---

## 2. Display conditions

- Rule rows, each one **Include** or **Exclude**, combined on a template.
- Scopes:
  - **Entire site**
  - **Singular** — all singular, by post type, a specific entry, by taxonomy
    term, by author, children of a given page
  - **Archive** — all archives, a post type's archive, a taxonomy, a specific
    term (with or without its children), author archive, date archive, the blog
    page
  - **Special** — front page, search results, 404
- **Specificity ranking** so the most specific template wins when several match:
  entire site < post type < taxonomy < single entry. Exclude always beats
  Include.
- A plain-English summary of the rules shown on the template list
  ("Everywhere except Portfolio").
- **Conflict warning** when two templates of the same type claim the same ground.
- **Enable / disable** a template without deleting it.

---

## 3. Admin UI — the Theme Builder page

Built on the existing admin shell (topbar, cards, toggles, search), so it looks
like the Widgets and Extensions pages.

- **Type overview** — a card per template type with a count and "Add New".
- **Template list per type** — name, condition summary, status, last modified.
- **Row actions** — Edit (opens Elementor), Edit Conditions (modal), Duplicate,
  Rename, Enable/Disable, Delete.
- **Create flow** — pick a type, name it, land straight in the Elementor editor.
- **Search and filter**, plus an empty state that explains what each type does.
- **Import / Export** as JSON, template and conditions together. *Optional.*
- **Starter layouts** — a few ready-made headers and footers to begin from.
  *Optional, later.*

---

## 4. Editor integration

- **Register real Elementor document types.** Free Elementor exposes
  `elementor/documents/register` and `register_document_type()`, so each template
  can open with its own context and name rather than as a generic page.
- **A sensible default page template per type** — headers and footers on a
  canvas, single/archive/search/404 full width without theme chrome.
- **Display Conditions inside the editor's publish flow**, so conditions can be
  set without going back to the admin page.
- **Preview Settings** — pick which real post or term the template previews
  against while editing. Essential for Single, Archive and Loop Item; without it
  you are editing against a blank page.
- **Admin-bar "Edit Template"** link on the front end when a template is driving
  the page being viewed.

---

## 5. Runtime / rendering engine

- **One resolver**: for the current request, find the winning header, footer and
  body template, resolved once and cached for the request.
- **Body takeover** through `template_include` for Single, Archive, Search
  and 404.
- **Header and footer rendered as our own locations**, not injected into the
  theme's markup with its chrome hidden by CSS. Two levels:
  - **Theme-independent** — we own the whole page template. Works on any theme.
  - **Theme-hook** — for themes that declare support, use their own slots.
- **Assets** — enqueue each template document's generated Elementor CSS plus the
  widgets' own styles.
- **Guards** — skip in admin, REST, feeds and the Elementor editor/preview, and a
  recursion guard so a template cannot render itself.
- **Body classes and wrapper markup** for styling hooks.

---

## 6. Dynamic-content gaps to close

Most of what Single and Archive templates need already exists. The real holes:

- **Archive loop on the main query.** Only the `Posts` widget can run the current
  archive query today. `Loop Grid`, `Loop Carousel`, `Advanced Posts` and
  `Filterable Posts` cannot — they all run their own query. An Archive template
  needs at least Loop Grid able to render the main query with its pagination.
- **Archive Description** (the term description) — no widget today.
- **Post Navigation** (previous / next post). Post Pagination covers numbered
  archive pages and in-post page splits, not post-to-post links.
- **Search Form**, and a "results for X, N found" widget for the Search template.
- **Dynamic Tags** — binding post title, featured image, a custom field or an
  author into *any* control, so a plain Heading can show the post title and a
  Button can take a custom field's URL. Free Elementor ships the whole
  dynamic-tags API, so this is open to us. **Approved for phase 4.**

404 and Search otherwise need no new widgets.

---

## 7. Decisions (settled)

| Question | Decision |
|----------|----------|
| Popup | **In**, phase 4 |
| WooCommerce | **In**, phase 5 |
| Theme replacement | **Full takeover** |
| Dynamic Tags | **Later**, not phase 1 |
| Where templates live | **Own post type** (`eap_template`) |
| Import/Export, starter layouts | **Later** |

**One note on how the takeover is implemented.** Taking the whole page template
would mean rendering the content area ourselves, which in phase 1 — before
Single and Archive templates exist — would downgrade every theme's post layout
to a bare loop. So the takeover is aimed at the header and footer slots
specifically: on `get_header` we print our own document head and header, then
load the theme's `header.php` inside a discarded buffer. Because WordPress loads
these with `require_once`, its own load right after ours does nothing and the
theme's header never reaches the page. The theme's content area is untouched.
Same for the footer. This replaces the theme's header and footer on any classic
theme without the fragile part of the old prototype — guessing at CSS selectors
to hide whatever the theme rendered.

---

## 8. Build order

| Phase | Contents |
|-------|----------|
| **1 — Foundation** | Post type, document types, conditions model and resolver, admin UI, **Header and Footer end to end** |
| **2 — Content templates** | Single, Archive, Preview Settings, and the loop-on-main-query gap |
| **3 — The rest** | Search, 404, Loop Item, and the missing widgets (Archive Description, Post Navigation, Search Form) |
| **4 — Popup + extras** | Popup with its triggers, Import/Export, starter layouts, Dynamic Tags |
| **5 — WooCommerce** | Single Product and Product Archive |

Phase 1 alone is a usable product: custom headers and footers with real
conditions.
