# Elementor AnimatePro Handoff Summary

This file is a structured handoff summary of the current plugin state so another AI or developer can continue the work from the current codebase.

## 1. Plugin Identity And Current Version

- Plugin name: `Elementor AnimatePro`
- Text domain: `elementor-animatepro`
- Prefix / namespace style: `EAP_`
- Author: `KP`
- Main plugin file:
  - [C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\elementor-animatepro.php](C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\elementor-animatepro.php)
- Current plugin version:
  - `1.19.2`
- Current packaged zip:
  - [C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro.zip](C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro.zip)

## 2. Current Architecture

### Core Architecture

- Main bootstrap:
  - [C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\elementor-animatepro.php](C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\elementor-animatepro.php)
- Admin UI / widget dashboard:
  - [C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\class-eap-admin.php](C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\class-eap-admin.php)
- Asset registration:
  - [C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\class-eap-assets.php](C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\class-eap-assets.php)
- Elementor widget registration:
  - [C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\class-eap-elementor.php](C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\class-eap-elementor.php)

### Asset Model

The plugin was refactored away from a single huge frontend bundle.

Current model:

- Shared core:
  - CSS: `assets/css/core.css`
  - JS: `assets/js/core.js`
- Per-widget CSS:
  - `assets/css/widgets/*.css`
- Per-widget JS:
  - `assets/js/widgets/*.js`
- Editor assets:
  - `assets/css/editor.css`
  - `assets/js/editor.js`

Current asset registration is in:

- [C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\class-eap-assets.php](C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\class-eap-assets.php)

### Important Performance Note

- Only built/registered widgets are active in Elementor.
- Asset loading is per-widget for the widgets that were refactored.
- There is still shared runtime/core logic, but not the old “load everything for every page” behavior.

## 3. Widgets Currently Marked As Built / Active In Dashboard

Built widget keys in admin:

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

Source:

- [C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\class-eap-admin.php](C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\class-eap-admin.php)

## 4. Widgets Currently Registered In Elementor

Currently registered in the active Elementor class:

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

Source:

- [C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\class-eap-elementor.php](C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\class-eap-elementor.php)

## 5. Admin Dashboard / Widget Dashboard Current State

### Dashboard Structure

Admin plugin UI exists with:

- Dashboard
- Widgets
- Extensions
- Theme Builder

### Widget Dashboard Cleanup Already Done

Removed from dashboard sections:

- `Animations` category removed
- `Form Widgets` category removed
- `Video Widgets` category removed
- `Notification` removed from widget dashboard list

These removals were dashboard-list removals, not necessarily code deletion of all old widget files.

## 6. EAP Tag / Badge Behavior In Elementor Editor

There is custom editor-side badge logic in:

- [C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\assets\js\editor.js](C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\assets\js\editor.js)
- [C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\assets\css\editor.css](C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\assets\css\editor.css)

Intent:

- show `EAP` tag on our widgets in Elementor panel
- avoid tagging default Elementor widgets
- also tag structure sidebar where possible

History:

- This was problematic multiple times.
- Current state was last adjusted to target real EAP widgets more safely.
- It may still need visual verification in Elementor search results and structure sidebar.

## 7. General Animation System Approach Used Across Widgets

Most built widgets use a common pattern:

- viewport-based visibility logic
- animations start when the element enters the screen
- many widgets also support reverse/reset when leaving screen
- JS visibility/runtime lives in per-widget scripts plus shared core runtime

Important note:

- This was heavily iterated.
- Some widgets may still need polish for exact animation timing or reverse behavior.

## 8. Widget-By-Widget Summary

### A. Image Box

Files:

- [C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\widgets\class-eap-widget-image-box.php](C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\widgets\class-eap-widget-image-box.php)
- [C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\assets\css\widgets\image-box.css](C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\assets\css\widgets\image-box.css)
- [C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\assets\js\widgets\image-box.js](C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\assets\js\widgets\image-box.js)

Built features:

- Multiple layouts:
  - Standard
  - Vertical
  - Interactive
  - Classic
  - Pointer
- Shared options:
  - image unfold directions
  - image hover effects
  - text styling
  - image size controls
  - image border radius
- Pointer layout:
  - tooltip follows cursor
  - heading + description
  - tooltip sizing and radius
- Vertical layout:
  - hover icon
  - icon box size
  - icon size
  - border radius
  - icon position
  - transform options on hover
- Classic / Interactive:
  - overlay color controls

Known history / likely areas to re-check:

- Outer spacing/padding around image was fixed multiple times.
- Pointer tooltip width and sizing were fixed.
- Worth verifying all spacing controls still behave as intended.

### B. Image Box Slider

Files:

- [C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\widgets\class-eap-widget-image-box-slider.php](C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\widgets\class-eap-widget-image-box-slider.php)
- `assets/css/widgets/image-box-slider.css`
- `assets/js/widgets/image-box-slider.js`

Built features:

- Slider version of the Image Box layouts
- Slider controls:
  - slides per view responsive
  - nav/pagination
  - icon selection for nav
  - icon size / container size
  - pagination styling
- Shares many layout features from Image Box
- Hover icon transform controls on vertical image layout

Known issues/history:

- Blank-slide / duplicate-slide issues were fixed several times
- Pointer tooltip clipping was fixed
- Still worth testing autoplay + loop thoroughly

### C. Image Hotspot

Files:

- `class-eap-widget-image-hotspot.php`
- `assets/css/widgets/image-hotspot.css`
- `assets/js/widgets/image-hotspot.js`

Built features:

- image + hotspot repeater
- hotspot layouts:
  - default
  - text
  - icon
- tooltip trigger:
  - hover
  - click
- tooltip content and position controls
- dot/icon styles
- pulse/animation controls
- image entrance motion added
- hover/normal state styling for outer circle + icon

Known history:

- icon rendering was broken many times, then fixed
- SVG icon sizing/path overflow was specifically fixed
- should still be rechecked on frontend with multiple icon libraries

### D. Social Icons

Files:

- `class-eap-widget-social-icons.php`
- `assets/css/widgets/social-icons.css`

Built features:

- default social icons widget behavior
- hover transform effects
- hover color/background changes
- `Open in new window` default checked

Likely stable, relatively simple.

### E. Image

Files:

- `class-eap-widget-image.php`
- `assets/css/widgets/image.css`
- `assets/js/widgets/image.js`

Built features:

- image widget with starter animations
- animation types include:
  - reveal
  - scale
  - slide
  - skew reveal
  - flip
- direction options vary by animation type
- duration / delay / easing / fade / scale-from etc

History:

- animation option set was rebuilt to match requested screenshots
- frontend visibility/init was fixed multiple times
- should still be checked on frontend when used in production

### F. Image Gallery

Files:

- `class-eap-widget-image-gallery.php`
- `assets/css/widgets/image-gallery.css`
- `assets/js/widgets/image-gallery.js`

Built features:

- layouts:
  - Basic
  - Masonry
  - Zigzag
- gallery repeater
- hover icon
- hover effects
- spacing / border radius
- image entrance animation
- lightbox enable/disable using Elementor-like behavior
- scroll-smooth style options were mentioned in earlier designs

History:

- lightbox option added later
- image entrance animation integrated
- worth rechecking zigzag layout and lightbox frontend behavior

### G. Image Comparison

Files:

- `class-eap-widget-image-comparison.php`
- `assets/css/widgets/image-comparison.css`
- `assets/js/widgets/image-comparison.js`

Built features:

- before/after image comparison
- left/right or top/bottom compare direction
- before/after labels
- label styling
- handle styling
- custom handle icon selection
- max height option added

Likely current gap:

- should visually verify vertical mode and max-height cropping behavior

### H. Parallax Sections

Files:

- `class-eap-widget-parallax-sections.php`
- `assets/css/widgets/parallax-sections.css`
- `assets/js/widgets/parallax-sections.js`

Built features:

- stacked/pinned parallax sections
- shared or per-section background
- image/video background support
- section repeater with content
- height option
- transition animation options
- content animation options
- pinning behavior
- fallback background color
- content source modes discussed:
  - direct content
  - template/page/shortcode search support was requested

History:

- this widget had the most iteration
- major issues addressed:
  - blank space under parallax
  - pinning top behavior
  - transition into next section
  - shared background behavior
  - border radius issue
- user explicitly said:
  - “Do not make any changes to this parallax widget until I say.”
- So this widget should be considered frozen unless requested.

### I. Text Hover Image

Files:

- `class-eap-widget-text-hover-image.php`
- `assets/css/widgets/text-hover-image.css`
- `assets/js/widgets/text-hover-image.js`

Built features:

- text split into before/hover/after text
- image follows cursor on hover
- image size and position controls
- typography controls for normal and hover text

History:

- smoothing was improved after initial glitch
- should recheck cursor-follow smoothness in real site conditions

### J. Brand Slider

Files:

- `class-eap-widget-brand-slider.php`
- `assets/css/widgets/brand-slider.css`
- `assets/js/widgets/brand-slider.js`

Built features:

- text or image based content
- separator icon
- continuous/marquee-like behavior
- navigation/pagination controls
- text styles, separator size/color
- hover color for text added
- image size option added
- animation speed changed to seconds at user request

History:

- marquee/continuous loop behavior was problematic and iterated multiple times
- separator positioning between items was also fixed
- should be considered usable but deserves frontend retest for seamless looping

### K. Icon Box

Files:

- `class-eap-widget-icon-box.php`
- `assets/css/widgets/icon-box.css`
- likely JS integrated only if needed

Built features:

- layouts:
  - Simple Icon Box
  - Animated Icon Box
  - Hover Effect Icon Box
  - Icon On Hover
- supports:
  - icon hover transforms
  - box hover transforms
  - title/description hover styling
  - read more text
  - accent icon system
  - expanding cover background effect
- Hover Effect layout:
  - expanding circle-like fill effect from accent icon
  - separate accent icon bg and full-box cover bg
- Icon On Hover:
  - accent icon shown on hover
  - configurable position
  - opacity/size controls

History:

- several refinements on expanding accent-cover effect
- separate hover bg colors were added
- likely worth visual re-check for exact polish

### L. Testimonial Box

Files:

- `class-eap-widget-testimonial.php`
- `assets/css/widgets/testimonial.css`

Built features:

- layouts:
  - Background Overlay
  - Floating Avatar
  - Stars + Separator
  - Quote Focus
  - User First
- controls:
  - background image
  - overlay gradient color
  - overlay start
  - avatar sizing / overlap
  - star rating with 0.5 increments
  - quote icon choices
  - separator style
  - min height, padding, border, radius, shadow
  - hover box effects
  - user info alignment
- half-star rendering fixed later to show a true half fill

History:

- background overlay designation background had to be removed
- hover text color options were removed
- hover box effects were repaired
- alignment logic was refined
- likely stable, but always worth checking overlay-start and half-star rendering after CSS cache clears

### M. Testimonial Slider

Files:

- `class-eap-widget-testimonial-slider.php`
- `assets/css/widgets/testimonial-slider.css`
- `assets/js/widgets/testimonial-slider.js`

Built features:

- slider version of testimonial layouts
- all testimonial box layouts adapted for slides
- responsive slides per view
- nav/pagination
- nav icon size / container size / offsets
- pagination position and type
- overlay start per slide fixed
- outside/inside nav/pagination styling
- loop/autoplay logic hardened
- half-star rendering aligned with testimonial box
- renamed `Breakout Avatar` to `Floating Avatar`

History / remaining caution:

- this widget had many CSS/loop/nav fixes
- arrow icon sizing and color inheritance were fixed
- inside pagination centering and outside positioning were fixed
- floating avatar top clipping was repeatedly adjusted
- current status: usable but still a widget that should be visually re-tested whenever touched

### N. Progress Bar

Files:

- `class-eap-widget-progress-bar.php`
- `assets/css/widgets/progress-bar.css`
- `assets/js/widgets/progress-bar.js`

Built features:

- layouts:
  - Flat
  - Circular
- flat:
  - title
  - percent inside/outside
  - bar height
  - color controls
- circular:
  - size
  - ring width
  - colors
  - centered percentage
- animation:
  - starts when entering viewport
  - number counting synchronized with fill timing

Likely stable.

### O. Team

Files:

- `class-eap-widget-team.php`
- `assets/css/widgets/team.css`
- `assets/js/widgets/team.js`

Built features:

- layouts currently available:
  - Spotlight Strip
  - Minimal Circle
  - Classic Social Card
  - Numbered Hover
  - Hover Social Overlay
- Removed layout:
  - Clean Link Card
- Social data model:
  - per-member fixed social link slots, not nested repeater anymore
  - up to 5 icon + link pairs per member
- Layout-specific improvements:
  - Minimal Circle:
    - fixed circular image behavior
    - forced 50% radius
    - border radius control hidden in this layout
  - Spotlight Strip:
    - white overlay default
    - socials on right
    - overlay row full width
  - Classic Social Card:
    - text alignment control
    - icons align with text
  - Numbered Hover:
    - hover button follows cursor within image
    - no snap-back-to-center on mouse leave
  - Hover Social Overlay:
    - corner icon removed
    - socials above overlay visually
    - position presets
    - vertical/horizontal direction
    - social icon border radius control

### Team Widget Display Mode

This was just implemented:

- `Display` switch:
  - Grid
  - Slider
- Grid:
  - existing grid layouts
- Slider:
  - slides per view responsive
  - space between
  - loop
  - autoplay
  - pause on hover
  - touch move
  - speed
  - arrows / pagination
  - nav/pagination style controls
- `team.js` contains slider init plus numbered-hover cursor logic

Important note:

- Team slider mode was implemented late and has not been comprehensively browser-verified across all team layouts in slider mode.
- That is one of the first things another AI should retest.

### P. Advanced Button

Files:

- `class-eap-widget-animated-button.php`
- `assets/css/widgets/advanced-button.css` or equivalent naming
- possibly JS only if needed

Built features:

- unified Advanced Button widget replacing separate Button widgets
- layouts:
  - Classic
  - Circle
  - Underline
  - dual-text hover-change button
  - icon motion button
  - surface fill button
- later changes:
  - underline layout was eventually removed for now
  - classic cross-hover was removed
  - surface fill timing/padding/min-height repeatedly refined

History:

- surface fill was heavily tweaked
- should be visually re-verified
- confirm whether underline layout is still disabled in code

### Q. Animated Text

Files:

- `class-eap-widget-animated-text.php`
- `assets/css/widgets/animated-text.css`
- `assets/js/widgets/animated-text.js`

Current direction:

- The widget was rebuilt from scratch after earlier versions became unstable.
- User explicitly asked to drop some transform/scrub experiments and return to a simpler rebuild.

Intended features:

- fade
- slide
- split
- zoom
- unfold
- reveal
- blur
- split by word / character / line
- directions for relevant effects

Important caution:

- This widget had repeated frontend visibility/rendering issues.
- The user explicitly said at one point the text was not visible on frontend.
- It was rebuilt, but this is still a high-risk widget that needs immediate frontend verification.

### R. Advanced Animated Text

Files:

- `class-eap-widget-advanced-animated-text.php`
- `assets/css/widgets/advanced-animated-text.css`
- `assets/js/widgets/advanced-animated-text.js`

Built effects requested:

- Typewriter
- Rainbow
- Shadow
- Changing text animation
- Glowing neon
- Glitch
- Shine/Sweep
- starter animation support added afterward

Status:

- user said “everything is fine” except needing starter animation, which was added
- likely more stable than Animated Text, but still should be checked on frontend

## 9. Widgets / Files Present In Repo But Not Part Of Current Built / Active Scope

There are many widget files still present under `includes/widgets`, including:

- accordion
- animated-heading
- brand-marquee
- counter
- cursor-preview-list
- curve-swipe
- horizontal-gallery
- horizontal-text
- logo
- menu
- offcanvas
- portfolio-grid
- pricing-table
- table-of-contents
- text-mask
- typewriter
- and more

These files being present does not mean they are fully registered, built, or supported in the current plugin state.

Important distinction:

- Do not assume a widget file existing means it is active.
- The source of truth for active/built widgets is:
  - `class-eap-elementor.php`
  - `class-eap-admin.php`

## 10. Known Risky / Re-Test Areas

These are the areas another AI should verify first before building more:

1. Animated Text

- frontend visibility/rendering
- split modes and directions
- viewport trigger behavior

2. Team slider mode

- every team layout in slider mode
- loop duplicates
- nav/pagination behavior
- numbered hover inside slider
- social/icon alignment in slider mode

3. Testimonial Slider

- loop/autoplay
- duplicate slides
- floating avatar clipping
- nav/pagination positions
- icon color/size inheritance

4. Brand Slider

- seamless marquee/continuous loop
- separator spacing
- text hover color behavior

5. Parallax Sections

- user said not to touch unless asked
- if touched later, retest pinning, blank-space, transition to next section immediately

## 11. Current Admin / Dashboard Cleanup State

Current widget dashboard intentionally no longer shows:

- Animations category
- Form Widgets category
- Video Widgets category
- Notification widget entry

Active animation widgets were moved into `General Widgets`.

## 12. Important Behavioral / History Notes

- The plugin was intentionally reset to blank earlier in the project, so older pre-reset work should be ignored.
- Many later fixes were CSS/JS cache-sensitive, so version bumps were used frequently.
- If anything “looks unchanged” in Elementor/frontend, cache/versioning is often part of the issue.
- Nested repeaters in Team were abandoned because Elementor editor behavior became unstable; replaced with fixed per-member social fields.

## 13. Suggested Continuation Plan For Another AI

Recommended next steps, in order:

1. Verify Animated Text frontend rendering thoroughly
2. Verify Team widget slider mode across all team layouts
3. Verify Testimonial Slider loop/autoplay/floating avatar
4. Verify Brand Slider seamless behavior
5. Only after stabilization, continue building next widgets

## 14. Current Important File Map

### Core / Plugin

- [C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\elementor-animatepro.php](C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\elementor-animatepro.php)
- [C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\class-eap-admin.php](C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\class-eap-admin.php)
- [C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\class-eap-assets.php](C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\class-eap-assets.php)
- [C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\class-eap-elementor.php](C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\includes\class-eap-elementor.php)

### Editor Badge System

- [C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\assets\js\editor.js](C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\assets\js\editor.js)
- [C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\assets\css\editor.css](C:\Users\abc\Local Sites\plugin-dev\app\public\wp-content\plugins\elementor-animatepro\assets\css\editor.css)

### Key Widget Files

- `class-eap-widget-image-box.php`
- `class-eap-widget-image-box-slider.php`
- `class-eap-widget-image-hotspot.php`
- `class-eap-widget-social-icons.php`
- `class-eap-widget-image.php`
- `class-eap-widget-image-gallery.php`
- `class-eap-widget-image-comparison.php`
- `class-eap-widget-progress-bar.php`
- `class-eap-widget-team.php`
- `class-eap-widget-parallax-sections.php`
- `class-eap-widget-text-hover-image.php`
- `class-eap-widget-brand-slider.php`
- `class-eap-widget-testimonial.php`
- `class-eap-widget-testimonial-slider.php`
- `class-eap-widget-animated-button.php`
- `class-eap-widget-animated-text.php`
- `class-eap-widget-advanced-animated-text.php`

## 15. One-Line Current State

Current codebase is a modular Elementor addon plugin with per-widget assets and a working admin dashboard, with 18 widgets marked built, but the highest-risk items that still need hands-on QA are Animated Text, Team slider mode, Testimonial Slider loop states, and Brand Slider continuity.
