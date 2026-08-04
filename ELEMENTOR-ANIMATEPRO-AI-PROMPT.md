# Elementor AnimatePro Continuation Prompt

Use this as the starting prompt for another AI tool to continue the plugin work from the current codebase.

## Role

You are continuing development of a custom WordPress Elementor addon plugin named `Elementor AnimatePro`.

Your job is to continue improving the plugin from its current local codebase state without undoing existing working functionality.

## Project Root

- Workspace root:
  - `C:\Users\abc\Local Sites\plugin-dev\app\public`
- Plugin root:
  - `C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro`

## Main Plugin Facts

- Plugin name: `Elementor AnimatePro`
- Text domain: `elementor-animatepro`
- Prefix: `EAP_`
- Author: `KP`
- Main plugin file:
  - `C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\elementor-animatepro.php`
- Current version at handoff:
  - `1.19.2`

## Core Files

- Main bootstrap:
  - `C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\elementor-animatepro.php`
- Admin dashboard / widget management:
  - `C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\class-eap-admin.php`
- Asset registration:
  - `C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\class-eap-assets.php`
- Elementor widget registration:
  - `C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\class-eap-elementor.php`
- Elementor editor badge logic:
  - `C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\assets\js\editor.js`
  - `C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\assets\css\editor.css`

## Asset Architecture

The plugin has already been refactored to a modular asset model.

Use this model going forward:

- shared core:
  - `assets/css/core.css`
  - `assets/js/core.js`
- per-widget CSS:
  - `assets/css/widgets/*.css`
- per-widget JS:
  - `assets/js/widgets/*.js`

Do not rebuild the old “single giant frontend bundle” approach.

## Current Built / Active Widgets

These are treated as built in the admin dashboard:

- `image-box`
- `image-box-slider`
- `image-hotspot`
- `social-icons`
- `image`
- `image-gallery`
- `image-comparison`
- `parallax-sections`
- `text-hover-image`
- `brand-slider`
- `icon-box`
- `testimonial-box`
- `testimonial-slider`
- `progress-bar`
- `team`
- `advanced-button`
- `animated-text`
- `advanced-animated-text`

These are the widgets currently registered in Elementor:

- Image Box
- Image Box Slider
- Icon Box
- Image Hotspot
- Social Icons
- Image
- Image Gallery
- Image Comparison
- Progress Bar
- Team
- Parallax Sections
- Text Hover Image
- Brand Slider
- Testimonial Box
- Testimonial Slider
- Advanced Button
- Animated Text
- Advanced Animated Text

## Important Dashboard Cleanup Already Done

The following were intentionally removed from the widget dashboard:

- Animations category
- Form Widgets category
- Video Widgets category
- Notification widget entry

Do not re-add them unless explicitly requested.

## EAP Badge Behavior

There is a custom badge/tag system for showing `EAP` on our widgets in the Elementor editor panel and structure/sidebar.

Relevant files:

- `assets/js/editor.js`
- `assets/css/editor.css`

Intent:

- show `EAP` only for our plugin widgets
- do not tag default Elementor widgets
- support normal panel, search results, and structure sidebar where possible

This area has been fragile before, so re-test carefully after any edits.

## Do Not Touch Without Permission

### Parallax Sections widget

The user explicitly said:

- do not change the Parallax widget unless asked

This widget had many iterations and difficult fixes around pinning, blank space, and exit behavior.

Only touch it if directly requested.

## High-Risk Areas To Re-Test First

Before doing major new work, verify these areas in a browser/editor:

1. Animated Text
- frontend visibility
- split animations
- directions
- viewport triggering

2. Team widget in slider mode
- all layouts in slider mode
- nav/pagination
- duplicated slides in loop mode
- Numbered Hover cursor-follow behavior

3. Testimonial Slider
- loop/autoplay
- duplicate slides
- floating avatar clipping
- nav and pagination positions
- half-star rendering

4. Brand Slider
- continuous marquee effect
- separator spacing
- hover color behavior

## Widget Status Notes

### Image Box
- multiple layouts already exist
- includes Standard, Vertical, Interactive, Classic, Pointer
- pointer tooltip follows cursor
- overlay options exist for relevant layouts
- spacing was previously fragile, re-check before editing

### Image Box Slider
- slider version of Image Box layouts
- nav/pagination and responsive slides-per-view implemented
- had prior blank duplicate slide issues

### Image Hotspot
- hotspot repeater
- default/text/icon hotspot layouts
- hover/click tooltip triggers
- icon rendering was previously broken and then fixed

### Social Icons
- stable/simple
- hover effects added
- open in new window default checked

### Image
- starter animation widget version exists
- reveal/scale/slide/skew/flip implemented

### Image Gallery
- Basic, Masonry, Zigzag
- hover icon, entrance animation, lightbox support

### Image Comparison
- before/after comparison
- left-right or top-bottom
- label styling
- custom handle icon
- max-height option added

### Parallax Sections
- pinned stacked sections
- shared/per-section background
- image/video support
- transition and content animation controls
- frozen unless user explicitly asks

### Text Hover Image
- hover image follows cursor
- before/hover/after text structure

### Brand Slider
- text/image marquee-like widget
- separator icon support
- had many loop/seamlessness fixes

### Icon Box
- multiple layouts already built
- hover effect icon layout
- icon-on-hover layout
- animated icon layout

### Testimonial Box
- layouts:
  - Background Overlay
  - Floating Avatar
  - Stars + Separator
  - Quote Focus
  - User First
- half-star rendering should show true half fill

### Testimonial Slider
- slider version of testimonial layouts
- nav/pagination styling and controls exist
- this widget has had many bugfixes and needs careful QA

### Progress Bar
- Flat and Circular layouts
- bar/ring fill and number count should be synchronized
- animates on viewport entry

### Team
- layouts currently available:
  - Spotlight Strip
  - Minimal Circle
  - Classic Social Card
  - Numbered Hover
  - Hover Social Overlay
- removed layout:
  - Clean Link Card
- supports Grid and Slider display modes
- social links are per-member fixed fields now, not nested repeater
- Minimal Circle should always behave as a 50% circular crop

### Advanced Button
- unified replacement for old button widgets
- multiple button styles already implemented
- likely needs visual QA if touched

### Animated Text
- rebuilt from scratch
- still considered risky
- confirm frontend output before extending

### Advanced Animated Text
- typewriter, rainbow, shadow, changing text, neon, glitch, shine/sweep
- starter animation support added

## Existing Non-Active Files

There are many old widget files still present under `includes/widgets`.
Do not assume file presence means active registration.
Source of truth is:

- `includes/class-eap-elementor.php`
- `includes/class-eap-admin.php`

## Working Style Instructions

- Preserve current plugin identity
- Keep code modular
- Keep per-widget asset loading
- Avoid adding large shared bundles
- Do not reintroduce removed dashboard categories/widgets unless requested
- Be cautious with widgets that had repeated CSS/JS bugfix history
- Prefer improving the current implementation instead of rewriting working sections

## Recommended Next Action Order

1. Verify Animated Text on frontend
2. Verify Team widget in slider mode across all layouts
3. Verify Testimonial Slider loop/autoplay/floating avatar
4. Verify Brand Slider continuity
5. Then continue adding or improving widgets

## Extra Reference

For the full long-form handoff, read:

- `C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\ELEMENTOR-ANIMATEPRO-HANDOFF.md`
