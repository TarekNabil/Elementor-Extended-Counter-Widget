# Plan: Elementor Extended Counter Widget (Non-Latin Numbers)

## TL;DR
Create a **standalone Elementor widget addon** that adds animated counters with **Arabic numeral support (٠١٢٣٤٥٦٧٨٩)** alongside Latin numbers, with full RTL language support. The widget will have its own controls independent from Elementor's built-in counter. v1 focuses on Arabic numerals; architecture is future-proof for user-selectable character sets and additional number systems.

---

## Architecture Overview
- **Type**: Standalone Elementor Widget (not extending the built-in counter, to avoid conflicts)
- **Scope**: Complete new widget with independent controls
- **MVP Features**: 
  - Display animated counters with Arabic numerals
  - RTL language support (for Arabic/Persian contexts)
  - Full WordPress.org compliance
  - Future-proof design for character set extensibility

---

## Phase 1: Foundation & Structure

### 1.1 Set up plugin entry point
- Create `elementor-extended-counter-widget.php` with:
  - Standard WordPress plugin header (name, description, version 1.0.0, author, license: GPLv3)
  - Plugin initialization hook (plugins_loaded)
  - Elementor check (is Elementor active?)
  - File structure documentation comments
- **Depends on**: None
- **Parallel with**: 1.2

### 1.2 Create class autoloading structure  
- Create `includes/` directory with:
  - `class-plugin.php` — Main plugin orchestrator class, handles registration
  - `widgets/` directory for future widget organization
- Set up PSR-4-style autoloading pattern (namespace: `ElementorExtendedCounterWidget`)
- **Depends on**: None
- **Parallel with**: 1.1

### 1.3 Create number conversion utility
- Create `includes/class-number-converter.php`:
  - Static method `latin_to_arabic($number)` — Converts single digit or full number to Arabic numerals
  - Static method `get_supported_formats()` — Returns available number systems (v1: "latin", "arabic")
  - Future-extensible design: registry pattern to add more formats
- **Depends on**: 1.2
- **Parallel with**: 1.4

### 1.4 Create widget base class
- Create `includes/widgets/class-counter-widget.php` extending `\Elementor\Widget_Base`
- Define widget name (`elementor_extended_counter`), title, category, icon
- Stub all abstract methods (will implement in Phase 2)
- **Depends on**: 1.2
- **Parallel with**: 1.3

---

## Phase 2: Widget Controls & Settings

### 2.1 Implement widget controls
- Add control groups in register_controls():
  - **Content → Counter**
    - Number (default: 100, type: number)
    - Prefix (text, optional)
    - Suffix (text, optional)
    - Number Format dropdown ("Latin" | "Arabic") — *uses class-number-converter*
  - **Content → Animation**
    - Duration (default: 2000ms)
    - Delay (default: 0ms)
  - **Style → Number**
    - Color picker
    - Typography selector
  - **Style → Prefix/Suffix**
    - Color picker
    - Typography selector
- **Depends on**: 1.4, 1.3

### 2.2 Add responsive controls
- Make typography breakpoint-aware (desktop/tablet/mobile)
- Font size responsive options
- **Depends on**: 2.1

---

## Phase 3: Rendering & JavaScript

### 3.1 Implement widget render_content() method
- Generate HTML structure:
  ```
  <div class="elementor-counter">
    <div class="counter-prefix">{{prefix}}</div>
    <div class="counter-number" data-target="{{target}}" data-format="{{format}}">0</div>
    <div class="counter-suffix">{{suffix}}</div>
  </div>
  ```
- Pass widget settings to JS via `wp_localize_script()`
- Add data attributes for JS consumption (target number, format, duration)
- **Depends on**: 2.1

### 3.2 Create counter animation script
- Create `assets/js/counter-animation.js`:
  - jQuery plugin or vanilla JS that animates number from 0 → target
  - Hooks into Elementor's edit mode & frontend
  - On animation frame: convert displayed number using `number_converter`
  - Use `requestAnimationFrame` for smooth animation
  - Respect duration and delay settings
- Enqueue in main plugin file using `wp_enqueue_script()` with Elementor dependency
- **Depends on**: 3.1, 1.3
- **Parallel with**: 3.3

### 3.3 Create base styling
- Create `assets/css/counter-widget.css`:
  - Default counter layout (flex, centered)
  - RTL support: `[dir="rtl"]` awareness in CSS
  - Responsive font sizing
  - Animation state classes
- Enqueue via `wp_enqueue_style()`
- **Depends on**: None
- **Parallel with**: 3.2

---

## Phase 4: Elementor Integration

### 4.1 Register widget with Elementor
- Create `includes/widgets/loader.php` or integrate into `class-plugin.php`:
  - Hook into `elementor/widgets/register` 
  - Register `class-counter-widget.php` widget
  - Handle editor preview rendering
- **Depends on**: 3.1, 3.2, 3.3

### 4.2 Add editor preview support
- Implement `get_script_depends()` to enqueue counter animation in editor
- Add edit mode hooks to re-trigger animations on setting changes
- **Depends on**: 4.1

---

## Phase 5: RTL & Localization Support

### 5.1 Add RTL CSS handling
- In counter-widget.css: duplicate selectors with `[dir="rtl"]` wrapper
- Mirror any directional CSS (padding, margin, text-align)
- Test with WordPress language set to Arabic
- **Depends on**: 3.3

### 5.2 Add text domain for translations
- Define text domain in main plugin file: `elementor-extended-counter-widget`
- Wrap all UI strings in `__()` / `_e()` functions
- Create `languages/` directory (optional for v1, but structure for i18n)
- **Depends on**: 2.1, 3.1

---

## Phase 6: Testing & Documentation

### 6.1 Manual testing checklist
- [ ] Plugin activates without errors
- [ ] Widget appears in Elementor's widget library
- [ ] All controls work (number, prefix, suffix, format dropdown)
- [ ] Latin number animation works (default)
- [ ] Arabic number animation works (verify conversion: 123 → ١٢٣)
- [ ] Prefix/suffix display correctly
- [ ] RTL mode (WordPress language = Arabic) displays numbers correctly
- [ ] Responsive design works on mobile/tablet
- [ ] Editor preview updates on setting change
- [ ] Frontend renders correctly

### 6.2 Browser testing
- Chrome/Chromium latest
- Firefox latest
- Safari latest
- Mobile browsers (iOS Safari, Chrome Mobile)

### 6.3 WordPress compatibility testing
- Latest WordPress stable version
- Latest Elementor stable version
- PHP 7.4+ (minimum supported)

---

## Phase 7: WordPress.org Preparation

### 7.1 Create plugin metadata files
- Add `readme.txt` with WordPress.org format:
  - Plugin name, description, author
  - Requires: WordPress version, PHP version, Elementor version
  - Stable tag: 1.0.0
  - Changelog, installation, usage sections
  - Screenshots (optional but recommended)
- Add `package.json` (if using build tools) or document manual build process

### 7.2 Plugin header optimization
- Ensure main plugin file header meets WordPress.org requirements:
  - License: GPLv3+
  - Text Domain: `elementor-extended-counter-widget`
  - Domain Path: `/languages` (for i18n)
  - Requires PHP: 7.4
  - Requires Plugins: elementor

### 7.3 Code standards compliance
- Verify PHPCS compliance (WordPress standards)
- No security issues (sanitization, escaping, nonces)
- No deprecated functions

---

## Key Architecture Decisions

1. **Separate Widget** — Independent from Elementor's built-in counter (zero conflicts, cleaner codebase)
2. **Utility-based Conversion** — `NumberConverter` uses registry pattern; extensible for Persian, Armenian, custom formats in future versions
3. **Vanilla JS + jQuery Compat** — Works with Elementor's editor and frontend
4. **PSR-4 Autoloading** — Professional structure, scales for features
5. **RTL-first CSS** — Defensive styling with `[dir="rtl"]` selectors from day 1

---

## Scope Boundaries

✅ **v1.0.0 Includes:**
- Arabic numeral (٠-٩) support
- Animated counters with custom duration/delay
- RTL language support
- WordPress/Elementor/PHP 7.4+ compatibility
- Single widget, ready for WordPress.org

❌ **v1.0.0 Excludes (future versions):**
- Multiple character sets (Persian, Armenian, Roman, alphabetic)
- Build tools / webpack
- Custom post types
- Admin dashboard

---

## Verification Checklist

1. Plugin activates without errors; Elementor shows widget in library
2. Controls work: number, prefix/suffix, format dropdown (Latin/Arabic)
3. Animation: Latin 123 animates correctly; Arabic conversion displays ١٢٣
4. RTL mode (WordPress language = Arabic): numbers display RTL correctly
5. Responsive design works on mobile/tablet/desktop
6. Editor preview re-animates on setting changes
7. Browser tested: Chrome, Firefox, Safari, mobile
8. Passes WordPress.org standards (sanitization, escaping, PHPCodeSniffer)
