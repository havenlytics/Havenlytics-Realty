=== Havenlytics Realty ===

Contributors: havenlytics
Tags: two-columns, right-sidebar, custom-logo, custom-menu, featured-images, full-width-template, theme-options, translation-ready, block-styles, wide-blocks
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 2.3.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Official companion theme for the free Havenlytics plugin — launch a real estate website with map search, listings, agents, and agencies.

== Description ==

**[Live demo](https://demo.havenlytics.com/)** | **[Havenlytics plugin](https://wordpress.org/plugins/havenlytics/)** | **[Theme author](https://profiles.wordpress.org/havenlytics/)**

Havenlytics Realty is the official WordPress theme built to work with the free **[Havenlytics](https://wordpress.org/plugins/havenlytics/)** property plugin. Together they deliver a complete real estate website: interactive map search, filtered property archives, agent and agency directories, and a conversion-focused homepage — without custom code.

Use the theme on its own for a fast, accessible blog or business site. Activate Havenlytics to unlock the full real estate experience.

= What Havenlytics Realty provides =

* A purpose-built **real estate homepage** with eight customizable sections
* **Appearance → Realty** onboarding hub with setup checklist and site snapshot
* **Automatic site launch** after demo import (homepage, menus, front page)
* **CSS harmonization** for property, agent, and agency pages
* **Header property search panel** with keyword, type, location, price, and bed/bath filters
* **Theme breadcrumbs** on listings, taxonomies, and directory pages
* **Customizer controls** for homepage sections, hero map height, listing counts, and CTAs
* **Full-width layouts** for plugin shortcode pages
* Accessible, responsive foundation for blogs and business pages

= What the Havenlytics plugin adds =

The plugin is the property engine. It registers properties, agents, and agencies; powers AJAX search with grid, list, and map views; renders single property pages with galleries, maps, and Contact Agent forms; and includes a Setup Wizard to import demo listings.

**Install both** to get the experience shown in the [live demo](https://demo.havenlytics.com/).

= Real estate homepage (Havenlytics plugin active) =

Eight sections, each toggleable in **Appearance → Customize → Real Estate Homepage**:

1. **Hero map** — Interactive property map with Customizer department filtering and adjustable height (desktop/mobile)
2. **Featured properties** — Curated listing carousel (featured meta)
3. **Properties by department** — Tabbed grids per department (Sale, Rent, Let, Commercial, etc.)
4. **Browse Properties** — Configurable taxonomy cards (locations, types, features, status, badges, or tags)
5. **Agents** — Agent directory carousel
6. **Agencies** — Agency directory carousel
7. **Latest blog posts** — Content marketing section (core WordPress posts)
8. **Call to action** — Search and agent discovery prompts

**Header search:** The search icon opens a property search panel (keyword, type, location, bedrooms, bathrooms, min/max price) that submits to the plugin Property Search page.

= Havenlytics plugin integration =

* Detects the plugin automatically — no theme edits to plugin files
* Primes plugin assets on the homepage for shortcode-rendered sections
* Resolves plugin page URLs for search, grid, lists, agents, and agencies
* Applies Customizer design tokens (primary, secondary, text) to plugin UI
* Adds context body classes for property, agent, agency, and shortcode pages
* Hooks `hvnly_before_main_content` for breadcrumb alignment
* Hides duplicate breadcrumbs on the real estate homepage
* Friendly fallback prompt when the plugin is not installed

= Theme foundation =

* Lightweight, modular CSS architecture
* Unified Customizer panel (design, header, footer, layout, typography)
* Responsive header with mobile menu and optional CTA
* Blog grid and list layouts with column controls
* Professional breadcrumb navigation with schema markup
* Elementor-compatible full-width pages
* Block editor styles and patterns

= Accessibility =

* Full keyboard navigation support
* Focus styles for all interactive elements
* Skip to content link
* ARIA labels and landmarks
* Screen reader friendly
* High contrast focus indicators

= Performance =

* Minimal vanilla JavaScript on the front end (jQuery only where plugin/home scripts require it)
* Conditional stylesheet loading per template
* Google Fonts loaded only for selected typography choices

= Works without the plugin =

Havenlytics Realty remains a fully functional WordPress theme for blogs, business pages, and directories. Without Havenlytics, the real estate homepage shows a clear prompt to install the plugin — no errors, no broken layouts.

== Installation ==

= Quick start (recommended) =

1. Install and activate **Havenlytics Realty** from **Appearance → Themes**.
2. Open **Appearance → Realty** for the setup checklist.
3. Install and activate the free **[Havenlytics plugin](https://wordpress.org/plugins/havenlytics/)** when prompted.
4. In the plugin admin, run the **Setup Wizard / Onboarding** to import demo listings, agents, and agencies.
5. Visit your site — the theme automatically configures the homepage, primary menu, and static front page after import.

**See the finished result:** [https://demo.havenlytics.com/](https://demo.havenlytics.com/)

= Manual install =

1. In your admin panel, go to **Appearance → Themes** and click **Add New**.
2. Search for **Havenlytics Realty** or upload the theme ZIP file.
3. Click **Activate**.
4. Go to **Appearance → Customize → Havenlytics Theme Settings** to adjust colors, header, footer, and layout.

= After activation =

* **Appearance → Realty** — onboarding checklist, progress ring, and quick links
* **Appearance → Customize → Real Estate Homepage** — hero map, sections, counts, and CTAs (requires Havenlytics plugin)
* **Appearance → Customize → Havenlytics Theme Settings** — global design, header, footer, typography, layout

== Plugin integration ==

Havenlytics Realty is designed as the **official front-end companion** for the Havenlytics plugin. Integration is entirely theme-side.

= Plugin features represented in the theme =

| Plugin capability | How the theme showcases it |
|-------------------|----------------------------|
| Property map search | Hero map section; header search panel; links to Property Search page |
| Property grid | Department tabs (`[hvnly_property_grid]`); Featured carousel (theme query) |
| Departments (`hvnly_prop_depts`) | Department tabs; hero map department filter; primary menu |
| Locations (`hvnly_prop_locations`) | Location cards; header search location filter |
| Property types (`hvnly_prop_types`) | Header search type filter; featured card badges |
| Featured listings | Featured properties homepage section |
| Agents (`hvnly_agent`) | Agents carousel; CTA links; breadcrumbs; CSS harmonization |
| Agencies (`hvnly_agent_agency`) | Agencies carousel; breadcrumbs; CSS harmonization |
| Property singles | Styled via compat CSS; theme breadcrumbs; full-width layout |
| Property archives & taxonomies | Breadcrumbs; body classes; archive styling |
| Contact Agent | Available on plugin single property pages (plugin feature; theme styles the page) |
| Setup Wizard demo import | Triggers theme auto-launch (homepage, menus, front page) |
| Five plugin pages | Auto-linked in primary menu and footer widgets after launch |

= Plugin pages created on install =

* Property Search (`[hvnly_property_search]`) — full filters, grid/list/map views
* Property Grid (`[hvnly_property_grid]`)
* Property Lists (`[hvnly_property_lists]`) — list layout
* Property Agents (`[hvnly_property_agents]`)
* Property Agencies (`[hvnly_property_agencies]`)

The theme links to these pages from the homepage, header search, CTAs, and menus. Property Search and Lists pages are not embedded as homepage sections — users navigate to them via search and menu links.

= What stays in the plugin =

Property Builder layouts, archive filter sidebar configuration, Contact Agent forms, AJAX search logic, map providers, import wizard, and single-property widgets remain plugin responsibilities. The theme does not modify plugin code.

== Frequently Asked Questions ==

= Do I need the Havenlytics plugin? =

No. Havenlytics Realty works as a standalone WordPress theme. For property listings, search, agents, agencies, and the real estate homepage, install the free [Havenlytics plugin](https://wordpress.org/plugins/havenlytics/) — the theme is built as its official companion.

= Where is the live demo? =

[https://demo.havenlytics.com/](https://demo.havenlytics.com/) — Havenlytics Realty with the Havenlytics plugin and demo content.

= How do I launch a real estate homepage? =

Install the Havenlytics plugin, run the Setup Wizard to import demo content, and the theme automatically creates a Home page, primary menu, and static front page. Use **Appearance → Realty** to track progress or **Appearance → Customize → Real Estate Homepage** to customize sections.

= Does the theme modify the Havenlytics plugin? =

No. All integration is theme-side: shortcodes, CSS harmonization, body classes, breadcrumbs, and admin onboarding. The plugin continues to work with any WordPress theme.

= How do I search for properties from the homepage? =

Click the **search icon** in the header to open the property search panel, or use the **Call to Action** section and menu links to the Property Search page.

= Does this theme support Elementor? =

Yes. Havenlytics Realty supports Elementor full-width layouts. The Havenlytics plugin also provides Elementor widgets for property archives, agents, and agencies.

= Is this theme accessibility ready? =

Yes. The theme includes keyboard navigation, focus styles, skip links, ARIA landmarks, and screen reader support.

= How do I change the primary color? =

Go to **Appearance → Customize → Havenlytics Theme Settings → Global Design System**. Colors also apply to Havenlytics plugin pages when both are active.

= How do I customize the real estate homepage? =

With the Havenlytics plugin active, go to **Appearance → Customize → Real Estate Homepage** to edit section visibility, hero map height, listing counts, department filters, CTA text, and more.

== Screenshots ==

Recommended WordPress.org screenshot set (accurate to the product):

1. **Homepage** — Hero map, featured properties, and department tabs ([live demo](https://demo.havenlytics.com/))
2. **Header search panel** — Expanded property search modal with filters
3. **Property single** — Gallery, details, and Contact Agent (plugin) with theme header
4. **Property search page** — Grid/list/map views and filter sidebar (plugin page)
5. **Agents directory** — Agent cards grid (plugin page or homepage section)
6. **Customizer** — Real Estate Homepage panel with section controls
7. **Appearance → Realty** — Onboarding hub with setup checklist
8. **Mobile homepage** — Responsive map hero and listing cards

== Changelog ==

= 2.3.2 - July 30, 2026 =
* IMPROVED: Hero search shows every enabled Search Builder field on tablet and mobile (stacked; no collapsed/advanced-only hide).
* IMPROVED: Hero carousel navigation grouped as previous + dots + next with tighter premium spacing.
* IMPROVED: Hero slider cross-fade overlaps outgoing/incoming slides for a cinematic transition without zoom jump.
* NEW: Floating contact action menu (WhatsApp, Phone, Email) with staggered open/close motion, Customizer controls, and keyboard accessibility.

= 2.3.1 - July 27, 2026 =
* FIXED: Upgrade regression (2.3.0 → 2.3.1) where the Testimonials carousel overflowed horizontally on mobile when more than three testimonials were stored.
* FIXED: Testimonial track width / translateX calculations so multi-slide carousels no longer widen the page, mobile menu, search, or footer.
* IMPROVED: Testimonials carousel shows one card per view on mobile and three cards on desktop while keeping display:flex.
* IMPROVED: Carousel respects Customizer autoplay + speed; pause on hover; resize-safe pixel transforms.
* NEW: One-time migration (2.3.1) normalizes invalid testimonial slider settings (JSON shape, speed clamp, autoplay boolean) without deleting testimonial content.

= 2.3.0 - July 27, 2026 =
* NEW: Homepage redesign release candidate — pixel-faithful implementation from the approved design specification.
* NEW: Interactive Property Map with custom tooltip (Leaflet + MarkerCluster via Havenlytics plugin assets).
* NEW: Curated Collections section driven by featured properties.
* NEW: Hero Background Mode — Static Image or Property Carousel with slide count, autoplay, transition, loop, pause-on-hover, and Ken Burns zoom.
* NEW: One-time safe migration (2.3.0) maps legacy CTA/hero settings and appends map/collections without deleting user data.
* IMPROVED: Cinematic hero cross-fade + zoom, stronger overlay for header/heading readability, subtle dotted rail decoration.
* IMPROVED: Favorite buttons use a white circular chip with soft shadow for contrast on photos.
* IMPROVED: Map tooltip close (outside, top-right) and favorite (top-left) never overlap; title contrast on media.
* IMPROVED: Customizer live preview no longer clears the hero title on load.
* IMPROVED: Search Console wraps the existing Search Builder; property status badges use hvnly_prop_status.
* IMPROVED: Homepage-scoped white overlay typography without changing global theme fonts/colors.
* COMPATIBILITY: Header, footer, Mobile Search Drawer, Favorites, Login/Workspace, colors, typography, and section order preserved. How It Works remains available.
* COMPATIBILITY: Fully backward compatible. No database migrations of posts. No plugin modifications.

= 3.0.0 - July 26, 2026 =
* NOTE: Pre-release package label superseded by 2.3.0 for the public Homepage redesign release.

= 2.2.0 - July 26, 2026 =
* NEW: Homepage 2.2 premium redesign — immersive hero, search-led first impression, and intentional section rhythm from Hero through CTA.
* NEW: How It Works process section with Customizer-managed steps (backward compatible; appended for existing section orders).
* IMPROVED: Featured Properties mosaic layout with a large lead listing card.
* IMPROVED: Editorial Why Choose Us, Types gallery, Locations chapter, human Agent cards, editorial Testimonials, and magazine Blog layout.
* IMPROVED: Memorable CTA finale and fixed legacy CTA footer hash default.
* IMPROVED: Fresh-install default section order prioritizes Search → Properties before marketing sections.
* COMPATIBILITY: Header, footer, Mobile Search Drawer, Search Builder, Favorites, Login/Workspace, Customizer settings, visibility, and ordering preserved. Fully backward compatible.

= 2.1.4 - July 25, 2026 =
* FIXED: Header Favorites dropdown positioning on mobile devices so the panel stays fully inside the viewport.
* FIXED: Prevented the Favorites panel from overflowing or being clipped outside the screen on narrow widths.
* IMPROVED: Responsive alignment of the Favorites dropdown across phones and tablets.
* IMPROVED: Better mobile usability for the Header Favorites component with viewport-aware positioning.
* IMPROVED: Complete version synchronization for reliable asset cache busting.
* COMPATIBILITY: Fully backward compatible. No database changes. No frontend feature changes. No plugin modifications. Desktop header behavior unchanged.

= 2.1.3 - July 24, 2026 =
* FIXED: Replaced legacy onboarding detection with live Havenlytics status detection.
* FIXED: Property Import status now reflects actual imported properties in the database.
* FIXED: Launch Complete status uses real site configuration (plugin, properties, homepage, front page, primary menu).
* FIXED: Theme Setup only appears when setup is genuinely incomplete.
* IMPROVED: Better compatibility with the latest Havenlytics onboarding workflow.
* IMPROVED: Admin dashboard and System Status accuracy.
* COMPATIBILITY: Fully backward compatible. No database changes. No frontend template redesign.

= 2.1.2 - July 24, 2026 =
* FIXED: Updated the Realty Setup Wizard to use the new Havenlytics React onboarding page (`hvnly-property-onboarding`).
* FIXED: Welcome Notice now appears only on fresh installations with zero Havenlytics Properties (all statuses).
* FIXED: Removed obsolete onboarding links pointing to the legacy Property Import Wizard.
* IMPROVED: Compatibility with the latest Havenlytics onboarding workflow.
* IMPROVED: Admin onboarding experience and property-aware setup checklist.
* IMPROVED: Complete version synchronization for reliable asset cache busting.
* COMPATIBILITY: Fully backward compatible. No database changes. No frontend changes. No plugin modifications.

= 2.1.1 - July 23, 2026 =
* IMPROVED: Premium mobile header action group — unified 44×44 Favorites, Login / Account, and menu controls with consistent borders, spacing, badge placement, and focus states.
* IMPROVED: Disabled the legacy theme mobile floating bottom dock on Single Property pages. This contact experience is now provided by the Havenlytics plugin mobile contact dock for a unified mobile experience.
* COMPATIBILITY: Desktop header, navigation, favorites logic, and Homepage Mobile Search Drawer are unchanged.

= 2.1.0 - July 20, 2026 =
* NEW: Smart authentication-aware header integrated with Havenlytics Agent Workspace.
* NEW: Dynamic Sign In, Sign Up, My Account, and Logout actions.
* NEW: Header Favorites icon with quick access to Saved Properties.
* NEW: Featured Properties cards now support Favorites (guest and logged-in).
* NEW: Responsive authenticated navigation in the mobile menu.
* IMPROVED: Better integration between Havenlytics Realty and Havenlytics.
* IMPROVED: UX for logged-in agents and guest visitors.
* IMPROVED: Header navigation and account experience.
* IMPROVED: Mobile authentication flow.
* COMPATIBILITY: Fully backward compatible. Theme still works without the Havenlytics plugin. No database changes. No duplicate authentication or favorites systems.

= 2.0.9 - July 6, 2026 =
* NEW: Improved custom testimonial carousel with seamless infinite looping using the native theme slider.
* IMPROVED: Continuous carousel experience without blank spaces at the beginning or end.
* IMPROVED: Responsive carousel calculations for desktop, tablet, and mobile.
* IMPROVED: Pagination synchronization with active slide.
* IMPROVED: Resize handling for consistent behavior across viewport changes.
* FIXED: Empty space appearing after the final testimonial slide.
* FIXED: Responsive carousel edge-case calculations.
* COMPATIBILITY: Fully backward compatible with all existing Havenlytics Realty installations.

= 2.0.8 - July 4, 2026 =
* NEW: Standalone Blog Mode when Havenlytics plugin is inactive.
* IMPROVED: Automatic detection of companion plugin via centralized hvn_realty_has_havenlytics() helper.
* IMPROVED: Graceful fallback to native WordPress blog templates.
* IMPROVED: Conditional loading of property templates and assets.
* IMPROVED: Frontend performance when plugin is inactive.
* IMPROVED: Better first-time user experience.
* FIXED: Broken homepage layout before plugin installation.
* FIXED: Empty property sections on fresh theme installation.
* FIXED: Property-dependent template rendering when plugin is unavailable.
* FIXED: Conditional asset loading for plugin components.
* COMPATIBILITY: 100% backward compatible with all existing Havenlytics Realty installations.

= 2.0.7 - July 3, 2026 =
* Added universal runtime asset loader with graceful CSS/JS fallback recovery.
* Added asset diagnostics to System Status for missing-file troubleshooting.
* Added translation template (languages/havenlytics-realty.pot) and release upgrade notice.
* Improved theme integrity reporting without blocking theme bootstrap.
* Minor stability and packaging improvements.

= 2.0.6 - June 30, 2026 =
* Improved default WordPress blog templates.
* Fixed mobile blog grid responsive layout.
* Improved blog archive, search, author, category and tag templates.
* Improved comments accessibility and Theme Check compatibility.
* Improved responsive blog experience across all devices.
* Minor UI/UX improvements and overall stability.

= 2.0.5 - June 29, 2026 =
* Mobile Search Drawer department pills now smooth-scroll to center the active department with scroll-snap and edge fade indicators for overflow
* New Customizer section: Homepage → Mobile Search Drawer — full control over colors, spacing, typography, animation, and advanced drawer behavior with live preview

= 2.0.4 - June 28, 2026 =
* New mobile-only floating search dock and filter drawer on the homepage — appears after the hero search scrolls out of view, hides when the hero search returns, and reuses the same Havenlytics search parameters and dynamic taxonomies as the desktop hero search (no duplicate query logic)
* Mobile hero search submit button order improved — Search is always the last action after More Filters on tablet and mobile (desktop layout unchanged)

= 2.0.3 - June 28, 2026 =
* Homepage hero search department tabs now load dynamically from the Property Department taxonomy — new departments appear automatically with no code changes (previously a fixed Buy/Rent/Sell set)
* Fixed hero search returning mixed results: a selected Department is now a hard filter that all other criteria (keyword, type, status, location, beds, baths, reception, area, features, badges, price) narrow, instead of being widened by an OR relation
* Listings note beneath the tabs now shows the real published-property count for the active Department and updates instantly when the active tab changes (previously a single site-wide total)

= 2.0.2 - June 27, 2026 =
* Final UI/UX polish: homepage header now uses the same navigation component as internal pages — dropdown child menus work on desktop hover, keyboard, and mobile touch, with arrow indicators and active highlighting
* Hero search "More Filters" button moved inline to the right of the Buy/Rent/Sell tabs; expanded panel regrouped with consistent field heights and a responsive grid (4 columns desktop, 2 tablet, 1 mobile)
* Fixed Homepage Section Order drag-and-drop (control now binds on lazy embed and uses the correct sortable engine; reordering saves and refreshes the preview)
* Why Choose Us converted to an unlimited repeater (icon, title, description, optional link) with drag-and-drop reorder, duplicate and delete; legacy card values are migrated automatically and never deleted
* Mobile hero rebuilt across 320–768px breakpoints: no overflow, stacked actions, wrapping stats, fitted search panel and scaled images
* Mobile menu reliability hardened: single toggle handler, overlay click-to-close, Escape, body scroll lock, resize auto-close, accordion submenus, and a higher z-index so the hamburger is always tappable
* Desktop and mobile headers now share one Site Identity logo — no second logo setting
* Customizer completion: every homepage section now has independent style controls (background, text color, spacing top/bottom, animation toggle) with live preview
* New drag-and-drop Homepage Section Order manager — reorder sections and toggle visibility without code edits; order saved in theme_mod
* Testimonials repeater control fully registered in the Customizer (unlimited items, photo, name, position, rating, content, drag-and-drop reorder, delete)
* Hero search "More Filters" panel — collapsed by default with smooth expand animation; adds property status, min price, bathrooms, keywords and min area fields
* Global colors and typography now update the frontend instantly: primary/secondary propagate to plugin brand variables in the Customizer preview; navigation font family control added
* Homepage inherits global `--hvn-primary` / `--hvn-secondary` tokens for buttons and accents without layout changes
* Removed misleading plugin-only color notice from Global Design — Theme Customizer remains the branding source of truth
* Backward compatible: existing theme_mods, widgets and menus are never deleted or overwritten

= 2.0.1 - June 27, 2026 =
* UI system consolidation so the theme and Havenlytics plugin feel like one product
* Unified global design tokens: container width, spacing, section gap, radius and shadow are emitted once on `:root` and shared by the theme and plugin
* Theme is now the single branding source of truth — the plugin brand variables (`--hvnly-brand-*`) inherit the Theme Customizer colors (Theme Customizer → Theme Default → Plugin Default); no duplicate color settings
* Homepage container width now follows the global Customizer container width
* New reusable `hvn-theme-header-actions` component used on every header (homepage, internal pages and mobile) with Customizer controls: show/hide, primary & secondary label/URL, open in new tab
* New reusable social-links component shared by the footer and mobile menu (Facebook, Instagram, X, LinkedIn, YouTube)
* Mobile menu now includes the header action buttons and social icons; existing animated hamburger, slide-in, overlay, scroll-lock and desktop auto-close retained
* Redesigned footer: brand block (logo, description, social) + unified responsive widget grid; new dynamic "Property Locations" and "Contact Information" widgets (locations hides gracefully when the plugin is inactive)
* New dedicated `footer-bottom` menu location (Privacy / Terms / Sitemap) with copyright on the left and the menu on the right
* New Customizer footer fields: brand description and contact address / phone / email / business hours
* Fresh-install footer seeding updated to the new layout (Quick Links, Property Locations, Contact) — only seeds when every footer area is empty; never overwrites existing widgets
* Fresh-install primary menu now adds Commercial / Let / Rent / Sale as children of the Search item (only when a new menu is created)
* Backward compatible: no settings, menus, widgets or theme mods are deleted; saved values continue to drive the site

= 2.0.0 - June 26, 2026 =
* Complete homepage rebuild from the ground up to match the new Havenlytics Realty premium design
* Brand-new homepage presentation layer: dedicated homepage header and footer, hero, integrated search, Why Choose Us, featured properties, property types, locations, agents, testimonials, latest insights, call to action and newsletter
* Single, self-contained homepage stylesheet (`assets/css/home.css`) — all legacy homepage CSS modules removed; styles scoped to `body.hvn-theme-home`
* Single vanilla-JavaScript homepage file (`assets/js/home.js`) — sticky header, mobile menu, animated counters, scroll reveal, search tabs, testimonials slider, back-to-top; all legacy homepage scripts removed
* Consistent `hvn-theme-` prefix across every homepage class, ID, CSS variable and JavaScript function
* Homepage interactive map removed from the homepage (map/search remain available on dedicated property search pages)
* Agencies section removed from the homepage (the agency archive remains available elsewhere)
* Rebuilt premium agent section driven by the existing agent query and helper data
* Rebuilt Customizer for every visible homepage heading, subtitle, image, statistic, button, toggle and section; obsolete map, department, hero-search and agency homepage controls retired (stored values kept, not rendered)
* Dynamic content preserved: properties, property search, blog, menus, logo, widgets and site identity
* Performance: the homepage now loads less CSS and JavaScript than before, with no unused homepage assets
* Existing user data, theme mods, menus, widgets, media, listings, agents and agencies remain untouched on update

= 1.24.0 - June 23, 2026 =
* Added automatic default logo assignment on fresh theme activation.
* Added automatic default site icon assignment on fresh installations.
* Existing user branding remains untouched.
* Improved first-time theme setup experience.
* Version and documentation updates.

= 1.23.0 - June 23, 2026 =
* Production stability release: restored single Modern Realty homepage architecture
* Package integrity hardening: safe file loaders, release manifest verification, and System Status theme integrity panel
* Customizer control classes load on customize_register to prevent false missing-class reports
* Optional Customizer controls degrade gracefully when control files are absent from a package
* Performance: blog single CSS limited to posts; Jetpack infinite scroll limited to blog views; duplicate admin CSS dequeue on Realty screens
* SEO: fixed hero search H1 markup; screen-reader H1 when hero search panel is hidden; archive empty-state H1

= 1.22.0 - June 20, 2026 =
* Starter Sites import engine: choose Modern Realty or Modern Minimal Realty, preview, confirm, and import safely
* New option `hvn_realty_active_demo_id` tracks the active starter demo (`modern` or `minimal`)
* Import log stored in `hvn_realty_import_log` (demo, date, version, user)
* Complete site import: home, blog, contact, plugin pages (when active), primary/footer menus, footer widgets (empty sidebars only), section order, and demo Customizer settings
* Non-destructive safeguards: no duplicate pages/menus, reuse launch menus on upgrade, properties and plugin data never removed
* 1.22.0 migration links legacy launch menus to Modern demo options for existing sites

= 1.21.0 - June 20, 2026 =
* Blog semantic and accessibility hardening: single sidebar landmark, main content landmark on blog views, search form landmark, and comment navigation labels
* Sidebar widget spacing uses flex column gap for consistent vertical rhythm
* Fixed single post content column collapse when sidebar layout is enabled
* Gutenberg block overflow containment and duplicate Comments block suppression on single posts
* Fixed latest-posts front page missing blog grid/list wrapper when realty homepage is not active

= 1.20.0 - June 8, 2026 =
* Added shared design token bridge so theme brand colors inherit Havenlytics plugin global colors when the plugin is active
* Legacy Customizer color settings remain for backward compatibility; existing theme_mod values are preserved
* Customizer shows a notice when Havenlytics manages global colors

= 1.19.2 - June 8, 2026 =
* Fixed first-load navigation issue after setup completion
* Improved primary menu initialization during first launch
* Prevented temporary page fallback menus on new Havenlytics installations
* Improved onboarding stability

= 1.19.1 - June 8, 2026 =
* Added first-time onboarding tutorial modal for new Havenlytics Realty users
* Added quick access setup tutorial inside the Realty dashboard
* Improved onboarding experience for new theme installations

= 1.19.0 - June 8, 2026 =
* Automatically populates the Havenlytics Single Property Sidebar with default widgets after theme launch on fresh installs
* Inserts Property Agent, Featured Properties, Related Properties, and Agent Listings widgets once; existing sidebars are left unchanged

= 1.18.0 - June 8, 2026 =
* Added Hero Search as the default onboarding layout for new Havenlytics Realty installations
* Fresh demo import now enables the hero map search panel and hides the header search icon by default

= 1.17.0 - June 8, 2026 =
* Added optional Hero Search Panel for map-based homepage layouts
* Customizer: Hero search display — header only (default), hero panel, or both
* Reuses the existing property search form; no duplicate search logic or assets

= 1.16.0 - June 8, 2026 =
* Property Taxonomies homepage section replaces Property Locations with a flexible taxonomy source selector (locations, types, features, status, badges, tags)
* New Browse Properties Customizer panel: source, columns, counts, icons, and images
* Modern responsive taxonomy card grid with dynamic image/icon support and accessible markup
* Read-time migration preserves existing Property Locations theme_mod settings (no database writes)
* Step 0 production hardening: integration fallbacks and graceful homepage section rendering

= 1.15.5 - June 8, 2026 =
* Premium desktop dropdown styling: soft fade + translateY reveal, elevated panel shadow, improved spacing and typography
* Removed submenu label shift on hover (no padding-left jump); stable hover and focus highlights
* Fixed mobile dropdown arrow anchoring so the toggle stays fixed while submenus expand underneath
* Mobile submenu open/close now uses max-height and opacity (no display toggle or sliding keyframes)
* Submenu state resets when the mobile menu closes

= 1.15.4 - June 8, 2026 =
* Fixed desktop dropdown menus closing when moving the pointer from parent item to submenu
* Added hover bridge and open-state handling for more reliable desktop submenu targeting
* Improved desktop dropdown transitions, submenu link hit area, and aria-expanded keyboard support
* Improved mobile submenu toggle indicators, aria-controls wiring, and expand/collapse behavior
* Navigation accessibility refinements for focus, Escape, and screen reader states

= 1.15.3 - June 8, 2026 =
* Synchronized mobile off-canvas menu branding with Appearance → Customize → Site Identity custom logo
* Added Customizer selective refresh and live preview support for the mobile menu logo
* Improved desktop navigation dropdown transitions, hover states, focus states, and active submenu styles
* Improved mobile menu spacing, tap targets, submenu toggle controls, and visual hierarchy
* Responsive refinements for header logo sizing, homepage cards, department tabs, and footer columns (320px–1440px)

= 1.15.2 - June 8, 2026 =
* Fixed hero map height Customizer controls (desktop and mobile vh) overriding plugin fixed map heights
* Fixed homepage Customizer inline CSS attaching to the correct stylesheet handle
* Department section footer button text and URL Customizer controls
* Expanded header property search panel with bedrooms, bathrooms, and price filters; smoother modal animation
* WordPress.org readme and product description accuracy improvements

= 1.15.0 - June 8, 2026 =
* Improved Customizer live preview for site logo, favicon, header CTA link, footer columns, back-to-top, and homepage section toggles/text
* Expanded typography choices with 12 additional Google Fonts (Roboto, Open Sans, Montserrat, Lato, Nunito, Source Sans Pro, Work Sans, Raleway, DM Sans, Outfit, Manrope, Playfair Display, Merriweather)
* Responsive fixes for narrow mobile headers, property search panel, mobile menu, homepage section headings, and tablet footer columns
* Minor Customizer preview stability improvements (font loading in preview iframe)

= 1.13.0 - June 8, 2026 =
* Replaced Getting Started with **Appearance → Realty** admin hub (progress ring, step cards, quick actions)
* Removed TGMPA plugin installer library and notices; plugin install links to WordPress.org
* Completed setup steps show disabled "Completed" buttons; legacy URL redirects to Realty page

= 1.12.0 - June 8, 2026 =
* Phase 5: WordPress.org companion positioning — readme, installation guide, FAQ, and screenshot list
* Updated theme description for Havenlytics plugin pairing
* Font credits updated (Inter, Poppins)

= 1.11.0 - June 8, 2026 =
* Phase 4: Real Estate Homepage Customizer section (hero, counts, CTA text, section toggles)
* Hero background color and image with live preview
* Auto-setup toggle for theme launch after demo import
* Selective refresh for homepage text fields
* Customizer preview defaults to static front page

= 1.10.0 - June 8, 2026 =
* Phase 3: Getting Started admin page under Appearance with 4-step onboarding checklist
* Progress bar, quick links, and site snapshot (properties, agents, agencies)
* Manual theme setup trigger when demo is imported but homepage not configured
* Welcome and launch notices link to Getting Started page

= 1.9.0 - June 8, 2026 =
* Phase 2: Full Havenlytics front-end integration via CSS harmonization (property, agent, agency, shortcode pages)
* Smart context detection for property taxonomies, agents, agencies, and plugin pages
* Theme breadcrumbs on plugin archives with Listings/Agents/Agencies parent links
* Plugin shell hooks (hvnly_before_main_content) for layout alignment
* Body classes: hvn-realty-plugin-property, hvn-realty-plugin-agent, hvn-realty-plugin-agency, hvn-realty-plugin-page
* Plugin shortcode pages use full-width layout without sidebar

= 1.8.0 - June 8, 2026 =
* Added Real Estate Homepage template with homepage sections (hero map, featured listings, departments, locations, agents, agencies, blog, CTA)
* Added theme launch pack: auto-creates Home page, Primary menu, and front page assignment after demo import
* Added Havenlytics integration module (helpers, shortcode wrappers, compat CSS)
* Post-import admin notice with View Website CTA

= 1.7.4 - June 8, 2026 =
* Fixed Havenlytics plugin detection (HvnlyNab class) and hvnly_property CPT integration
* Header search links to the Havenlytics Property Search page when the plugin is active
* Property archives no longer receive blog grid body classes
* Added dismissible admin welcome notice after theme activation
* Added read-only plugin page ID helper for theme integration without modifying plugin code

== Credits ==

= Code Credits =

* Based on Underscores https://underscores.me/, (C) 2012-2020 Automattic, Inc., [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)

* Google Fonts: Inter, Poppins, Plus Jakarta Sans, Roboto, Open Sans, Montserrat, Lato, Nunito, Source Sans Pro, Work Sans, Raleway, DM Sans, Outfit, Manrope, Playfair Display, and Merriweather — licensed under SIL Open Font License 1.1
  Source: https://fonts.google.com/

* Normalize.css https://necolas.github.io/normalize.css/, (C) 2012-2018 Nicolas Gallagher and Jonathan Neal, [MIT](https://opensource.org/licenses/MIT)

= Image Credits =

All images used in the theme screenshot and demo content are from free stock photo websites with GPL-compatible licenses.

== Additional Information ==

Havenlytics Realty WordPress Theme, (C) 2026 Havenlytics
Havenlytics Realty Theme is distributed under the terms of the GNU GPL.

* **Live demo:** https://demo.havenlytics.com/
* **Theme:** https://wordpress.org/themes/havenlytics-realty/
* **Plugin:** https://wordpress.org/plugins/havenlytics/
* **Author:** https://profiles.wordpress.org/havenlytics/

== Upgrade Notice ==

= 2.3.2 =
Recommended update: full mobile hero search fields, refined hero slider/nav, and a new floating contact action menu. Fully backward compatible.

= 2.3.1 =
Recommended update: fixes Testimonials carousel horizontal overflow after upgrading from 2.3.0 (mobile). Normalizes invalid slider settings only; testimonial content is preserved.

= 2.3.0 =
Recommended update: Homepage redesign with property map, collections, hero carousel, and safe settings migration. Existing Customizer colors, typography, order, and visibility are preserved. Fully backward compatible.

= 3.0.0 =
Superseded by 2.3.0 (pre-release package label only).

= 2.2.0 =
Recommended update: premium Homepage 2.2 redesign with immersive hero, process section, and refined section layouts. Existing Customizer content, order, and visibility are preserved. Fully backward compatible.

= 2.1.4 =
Recommended maintenance update: fixes Header Favorites dropdown clipping on mobile so the panel stays fully visible. Desktop unchanged. Fully backward compatible.

= 2.1.3 =
Recommended maintenance update: live Property Import / Launch Complete detection replaces legacy setup flags. Fully backward compatible.

= 2.1.2 =
Recommended maintenance update: React onboarding URL, Welcome Notice only on fresh installs (no existing properties), and full 2.1.2 version sync. Fully backward compatible.

= 2.1.1 =
Maintenance update: polished mobile header action controls and theme floating bottom dock no longer renders on Single Property pages so the Havenlytics plugin mobile contact dock can own that experience. Homepage search drawer and desktop header unchanged.

= 2.1.0 =
Recommended update with smart Workspace authentication in the header, Favorites quick access, and Featured Property favorite buttons. Fully backward compatible; theme still works without the plugin.

= 2.0.9 =
Recommended update with seamless infinite-loop testimonial carousel, improved responsive slide calculations, and pagination sync. Fully backward compatible.

= 2.0.8 =
Recommended update introducing Standalone Blog Mode for sites without the Havenlytics plugin, with graceful fallbacks and improved first-install experience. Fully backward compatible.

= 2.0.7 =
Recommended update with runtime asset fallback recovery, improved theme integrity diagnostics, translation readiness, and stability improvements.

= 2.0.6 =
Recommended update containing stability improvements, homepage migration fixes, blog enhancements, responsive improvements, admin optimizations, and compatibility updates.

= 1.22.0 =
Introduces the Starter Sites platform with complete Modern Realty and Modern Minimal Realty website designs (header, footer, homepage, menus, widgets, and Customizer defaults). Import replaces demo-owned layout settings only; properties, agents, listings, and blog content are never removed. Existing sites without a starter import keep legacy homepage styling until you import from Realty → Starter Sites.

= 1.21.0 =
Blog accessibility and layout improvements: corrected sidebar landmarks, single-post sidebar layout stability, and Gutenberg block compatibility on posts. No breaking changes.

= 1.20.0 =
Introduces a CSS variable bridge between Havenlytics Realty and the Havenlytics plugin for primary, secondary, and accent colors. Existing Customizer settings are unchanged. No breaking changes.

= 1.19.2 =
Fixes primary menu assignment on first site load after setup. Existing menus are unchanged. No breaking changes.

= 1.19.1 =
Added a welcome setup tutorial modal for new installations and a Watch Tutorial card on the Realty admin page. No breaking changes.

= 1.16.0 =
Property Taxonomies section with Browse Properties Customizer controls. Existing Property Locations settings migrate automatically. No breaking changes.

= 1.15.5 =
Premium navigation interaction polish: desktop dropdown animation and styling, fixed mobile arrow positioning, smoother submenu expand. No breaking changes.

= 1.15.4 =
Navigation improvements: reliable desktop dropdowns, mobile submenu usability, and accessibility refinements. No breaking changes.

= 1.15.3 =
Logo synchronization, navigation polish, mobile menu usability, and responsive refinements. No breaking changes.

= 1.15.2 =
Hero map height fix, header search panel improvements, department button Customizer controls, and readme accuracy updates. No breaking changes.

= 1.15.0 =
Customizer live preview improvements, expanded Google Fonts, and responsive fixes. No breaking changes.

= 1.12.0 =
WordPress.org companion positioning update. Readme, onboarding guide, and Havenlytics plugin pairing documentation improved. No breaking changes.
