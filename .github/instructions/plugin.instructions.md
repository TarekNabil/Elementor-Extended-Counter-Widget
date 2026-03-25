---
name: "Elementor-Extended-Counter-Widget Plugin Development"
description: "Use when: developing the Elementor Extended Counter Widget plugin, implementing features, debugging issues, or maintaining code quality. This instruction set ensures consistency with WordPress/Elementor best practices, security standards, and the plugin's architecture."
applyTo: ["**/*.php", "**/*.js", "**/*.css", "includes/**", "assets/**", "README.md"]
---

# Elementor Extended Counter Widget — AI Development Instructions

## Project Context
- **Plugin**: Elementor Extended Counter Widget
- **Type**: Elementor Addon / WordPress Plugin
- **Goal**: Add Arabic numeral support to animated counters
- **Target**: WordPress.org plugin repository
- **License**: GPLv3+

---

## Code Style & Conventions

### PHP (PSR-12 WordPress Standards)
- **Indentation**: Tabs (1 tab = 4 spaces in IDE)
- **Line length**: Soft 100, hard 120 characters
- **Naming**:
  - Classes: `PascalCase` (e.g., `NumberConverter`, `CounterWidget`)
  - Functions: `snake_case` (e.g., `get_counter_value()`)
  - Constants: `UPPER_SNAKE_CASE` (e.g., `PLUGIN_VERSION`)
  - Private methods/props: prefix with underscore (e.g., `_initialize()`, `$_settings`)
- **Classes**: One class per file, filename matches class name (e.g., `class-counter-widget.php` for `Counter_Widget`)
- **Namespaces**: Use `ElementorExtendedCounterWidget\` with subnamespaces (e.g., `ElementorExtendedCounterWidget\Widgets`, `ElementorExtendedCounterWidget\Utils`)

### PHP Documentation
- Use standard PHPDoc blocks for all classes, methods, and constants
- Include `@param`, `@return`, `@throws` where applicable
- Example:
  ```php
  /**
   * Convert Latin number to Arabic numerals.
   *
   * @param int|string $number The number to convert.
   * @return string Converted number in Arabic numerals.
   * @throws \InvalidArgumentException If input is not numeric.
   */
  public static function latin_to_arabic( $number ) { ... }
  ```

### JavaScript (ES6+ / JSDoc)
- **Indentation**: Tabs
- **Naming**: `camelCase` for functions and variables, `PascalCase` for classes
- **Semicolons**: Required at statement end
- **Quotes**: Single quotes for strings (unless HTML attributes)
- Add JSDoc blocks for functions:
  ```javascript
  /**
   * Animate counter from start to target value.
   * 
   * @param {HTMLElement} element - Counter DOM element
   * @param {number} target - Target number value
   * @param {string} format - Number format ("latin" or "arabic")
   * @param {number} duration - Animation duration in milliseconds
   * @returns {Promise<void>} Resolves when animation completes
   */
  ```

### CSS
- **Indentation**: Tabs
- **Naming**: `kebab-case` for class names (e.g., `.counter-animation`, `.counter-prefix`)
- **BEM Pattern**: Use when nesting is complex (e.g., `.counter__number--large`)
- **RTL Support**: Always mirror directionality:
  ```css
  .counter-item { margin-left: 10px; }
  [dir="rtl"] .counter-item { margin-left: 0; margin-right: 10px; }
  ```
- **Mobile First**: Write base styles for mobile, enhance with media queries

---

## WordPress Security (CRITICAL)

### Input Validation
- Use `absint()`, `intval()`, `sanitize_text_field()`, `sanitize_html_class()` for all user input
- Example:
  ```php
  $number = absint( $_POST['counter_number'] );
  $format = sanitize_text_field( $_POST['format'] );
  ```

### Output Escaping
- Always escape output before displaying:
  - `esc_html()` for text
  - `esc_attr()` for HTML attributes
  - `esc_url()` for URLs
  - `wp_kses_post()` for allowed HTML
- Example:
  ```php
  echo '<div>' . esc_html( $counter_value ) . '</div>';
  ```

### Nonces (if handling form submissions in future)
- Use `wp_verify_nonce()` for form validation
- Use `wp_nonce_field()` in forms

### Data Storage
- Use Options API (`get_option()`, `update_option()`) for plugin settings
- Never store sensitive data in database without encryption

### No Direct Database Queries (for now)
- Phase 1-6: Use WordPress APIs only; no direct `$wpdb` queries
- If future versions need custom database tables, use `$wpdb->prepare()` with placeholders

---

## Elementor Widget Development Patterns

### Widget Structure
- All widgets must extend `\Elementor\Widget_Base`
- Required methods: `get_name()`, `get_title()`, `get_icon()`, `get_category()`, `register_controls()`, `render()`
- Optional: `get_script_depends()`, `get_style_depends()`, `get_keywords()`

### Controls Registration
- Organize controls into sections (Content, Style, Advanced)
- Use descriptive labels and help text
- Example:
  ```php
  $this->add_control(
    'number_format',
    [
      'label' => __( 'Number Format', 'elementor-extended-counter-widget' ),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => [
        'latin' => __( 'Latin (1, 2, 3...)', 'elementor-extended-counter-widget' ),
        'arabic' => __( 'Arabic (١, ٢, ٣...)', 'elementor-extended-counter-widget' ),
      ],
      'default' => 'latin',
      'help' => __( 'Choose the numeral system for counter display.', 'elementor-extended-counter-widget' ),
    ]
  );
  ```

### Widget Rendering
- Keep render() method simple; delegate logic to utilities
- Pass settings to JS via `wp_localize_script()` or data attributes
- Always use Elementor's render tag helpers for semantic HTML

---

## Plugin Architecture Conventions

### File Organization
```
elementor-extended-counter-widget/
├── elementor-extended-counter-widget.php  # Plugin entry point
├── includes/
│   ├── class-plugin.php                   # Main orchestrator
│   ├── class-number-converter.php         # Utility: number conversion
│   ├── widgets/
│   │   ├── class-counter-widget.php       # Widget class
│   │   └── loader.php                     # Widget registration loader
├── assets/
│   ├── js/
│   │   └── counter-animation.js           # Animation logic
│   └── css/
│       └── counter-widget.css             # Widget styles
├── languages/
│   └── elementor-extended-counter-widget.pot  # Translation template
├── readme.txt                              # WordPress.org metadata
└── plan.md                                 # Development plan
```

### Autoloading (PSR-4)
- Use namespaces starting with `ElementorExtendedCounterWidget`
- Manual autoloading in `class-plugin.php`:
  ```php
  spl_autoload_register( function( $class ) {
    if ( false === strpos( $class, 'ElementorExtendedCounterWidget' ) ) {
      return;
    }
    $path = PLUGIN_PATH . 'includes/' . str_replace( '\\', '/', $class ) . '.php';
    if ( file_exists( $path ) ) {
      require $path;
    }
  });
  ```

### Plugin Entry Point Pattern
- Check for Elementor active on `plugins_loaded` hook
- Instantiate main plugin class once
- Use action hooks for initialization stages

---

## Best Practices for AI Implementation

### 1. Read Plan First
- Before implementing, always consult `plan.md` for phase context and dependencies
- Link between code and plan (add inline comments referencing phase/step)

### 2. Security-First Mindset
- Never trust user input; always validate and sanitize
- Review every `$_POST`, `$_GET`, `$_REQUEST`, and database query before implementing
- When in doubt, apply `sanitize_text_field()` or `absint()`

### 3. Incremental Implementation
- Implement one phase at a time, don't skip ahead
- Test after each phase (especially after JavaScript and rendering)
- Commit to Git after each complete phase

### 4. Comment Design Decisions
- Add inline comments explaining *why*, not just *what*:
  ```php
  // Registry pattern allows future versions to add custom number formats
  // without modifying core conversion logic
  private static $format_registry = [];
  ```

### 5. Translation-Ready (i18n)
- Wrap ALL user-visible strings in `__()` or `_e()`:
  ```php
  echo __( 'Number Format', 'elementor-extended-counter-widget' );
  ```
- Text domain: `elementor-extended-counter-widget` (matches plugin slug)

### 6. Responsive by Default
- CSS: mobile-first; use `@media` for tablets/desktop
- JavaScript: test touch events and reduce animations on low-end devices (future optimization)

### 7. Git Commit Hygiene
- One logical change per commit
- Use descriptive messages: "Add Number_Converter utility class (Phase 1.3)"
- Format: `<phase>: <action> — <description>`

### 8. Future Extensibility
- Use `apply_filters()` for plugin hooks; use `do_action()` for events
- Design class methods to be easily overridden (consider final keyword sparingly)
- Registry/factory patterns for pluggable components (number formats, widget types)

---

## Testing Checklist Before Each Commit

- [ ] No PHP syntax errors (lint)
- [ ] All strings translated (grep for bare strings)
- [ ] Input validation present (grep for `$_POST`, `$_GET` without sanitization)
- [ ] Output escaped (grep for `echo`/`esc_` pairs)
- [ ] JSDoc comments added to new functions
- [ ] Code follows PSR-12 / WordPress standards
- [ ] Committed message references phase/step from plan.md

---

## Common Gotchas

1. **Elementor Dependency**: Always check `if ( ! did_action( 'elementor/loaded' ) )` before using Elementor APIs
2. **Widget Registration Timing**: Register on `elementor/widgets/register`, not `plugins_loaded`
3. **RTL CSS**: Don't forget to mirror CSS for `[dir="rtl"]` — RTL is not just text direction
4. **Number Conversion**: Test edge cases: 0, negative numbers (if supported), very large numbers
5. **Animation Performance**: Use `requestAnimationFrame()`, not `setInterval()`, for smooth 60fps animation
6. **WordPress Multisite**: Plugin should work on multisite without special handling (for now)
