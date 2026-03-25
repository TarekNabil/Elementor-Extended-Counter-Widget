# Elementor Extended Counter Widget

**Transform your counters with non-Latin numerals.** An Elementor addon that extends animated counter functionality to support Arabic numerals (٠١٢٣٤٥٦٧٨٩) alongside traditional Latin numbers, with full RTL language support.

---

## Features

### Current (v1.0)
✅ **Animated Counters** — Smooth animations from 0 to target number  
✅ **Arabic Numerals** — Display counters in Arabic numerals (٠-٩)  
✅ **Latin Numerals** — Classic Latin number support (1-9)  
✅ **RTL Language Ready** — Full support for Arabic, Persian, and other RTL languages  
✅ **Customizable** — Add prefixes/suffixes, adjust animation duration and delay  
✅ **Responsive Design** — Adapts beautifully to all screen sizes  
✅ **Elementor Native** — Works seamlessly with Elementor page builder  

### Future Roadmap (v1.1+)
- Persian/Farsi numeral support (۰-۹)
- Chinese numerals
- Roman numerals (I, II, III, etc.)
- User-configurable character sets
- Advanced animation easing options
- Thousand separators

---

## Requirements

- **WordPress**: Latest stable version (currently tested on 6.x)
- **PHP**: 7.4 or higher
- **Elementor**: Latest stable version (tested with Elementor 3.15+)
- **Browser**: Modern browsers with ES6 support (Chrome, Firefox, Safari, Edge)

---

## Installation

### From WordPress.org Plugin Repository (Coming Soon)
1. Go to **WordPress Admin** → **Plugins** → **Add New**
2. Search for "Elementor Extended Counter Widget"
3. Click **Install Now**, then **Activate**

### Manual Installation
1. Download the plugin `.zip` file
2. Extract to `/wp-content/plugins/elementor-extended-counter-widget/`
3. Go to **WordPress Admin** → **Plugins**
4. Find "Elementor Extended Counter Widget" and click **Activate**

### From Source (Development)
```bash
cd wp-content/plugins
git clone https://github.com/TarekNabil/Elementor-Extended-Counter-Widget.git
cd Elementor-Extended-Counter-Widget
# Plugin is ready to activate in WordPress Admin
```

---

## Quick Start

1. **Activate Plugin**
   - Go to WordPress Admin → Plugins
   - Click Activate on "Elementor Extended Counter Widget"

2. **Add Widget to Page**
   - Open/create a page with Elementor
   - Search for "Extended Counter" in the widgets library
   - Drag to your page

3. **Configure Counter**
   - Set number value (default: 100)
   - Choose number format: Latin or Arabic
   - Add optional prefix/suffix (e.g., "$" or "K+")
   - Customize animation duration and delay
   - Style colors and typography

4. **Publish & Done!**
   - Preview on frontend to see animation in action

---

## Architecture

### Structure
```
elementor-extended-counter-widget/
├── elementor-extended-counter-widget.php  # Plugin entry point
├── includes/
│   ├── class-plugin.php                   # Plugin orchestrator
│   ├── class-number-converter.php         # Number format utilities
│   ├── widgets/
│   │   ├── class-counter-widget.php       # Counter widget class
│   │   └── loader.php                     # Widget registration
├── assets/
│   ├── js/counter-animation.js            # Animation engine
│   └── css/counter-widget.css             # Widget styling
├── languages/                              # Translation files (i18n)
├── plan.md                                # Development roadmap
└── readme.txt                             # WordPress.org metadata
```

### Design Patterns
- **PSR-4 Autoloading**: Automatic class loading via namespaces
- **Elementor Widget Model**: Extends `\Elementor\Widget_Base` for native integration
- **Utility Classes**: Reusable `NumberConverter` for easy format extensions
- **RTL-First CSS**: Defensive styling that works with both LTR and RTL languages
- **Extensible Architecture**: Registry patterns for adding custom number formats

---

## Developer Guide

### Setting Up Development Environment
1. Clone the repository
2. Ensure WordPress and Elementor are installed locally
3. Create `.github/instructions/plugin.instructions.md` (read for coding standards)
4. Review `plan.md` for implementation roadmap

### Adding a New Number Format

The `NumberConverter` class uses a registry pattern for extensibility:

**Step 1:** Add your converter method in `includes/class-number-converter.php`
```php
public static function convert_to_Armenian( $number ) {
  // Conversion logic here
  return $converted;
}
```

**Step 2:** Register the format in widget controls (`includes/widgets/class-counter-widget.php`)
```php
'Armenian' => __( 'Armenian Numerals', 'elementor-extended-counter-widget' ),
```

**Step 3:** Update the animation script (`assets/js/counter-animation.js`) to call the new format

### Code Standards
- **PHP**: PSR-12 WordPress Coding Standards
- **JavaScript**: ES6+ with JSDoc documentation
- **CSS**: BEM naming, RTL-aware selectors
- All user-visible strings wrapped in `__()` / `_e()` for translation

### Commit Message Format
```
<phase>: <action> — <description>

Phase 1: Add Number_Converter utility — Implements Latin to Arabic conversion
Phase 3: Implement widget renderering — Adds HTML structure and data attributes
```

### Testing
Before commit, verify:
- [ ] PHP lint passes (no syntax errors)
- [ ] All strings are translatable
- [ ] Input is validated, output is escaped
- [ ] Code follows WordPress standards
- [ ] Widget appears in Elementor library
- [ ] Animations work (Latin and Arabic)
- [ ] RTL rendering is correct

---

## WordPress.org Submission

This plugin is prepared for WordPress.org plugin repository:

- ✅ GPLv3+ License
- ✅ Security best practices (input validation, output escaping)
- ✅ i18n-ready (text domain, translation functions)
- ✅ Elementor dependency declared
- ✅ Minimum PHP version specified
- ✅ No tracking or external data collection

---

## Contributing

We welcome contributions! 

### How to Contribute
1. **Fork** the repository
2. **Create a feature branch** (`git checkout -b feature/arabic-animation-easing`)
3. **Follow the code standards** (see `.github/instructions/plugin.instructions.md`)
4. **Test thoroughly** (see testing checklist above)
5. **Submit a pull request** with clear description

### What We're Looking For
- Bug fixes with test cases
- Performance improvements
- Accessibility enhancements
- Additional number format support
- Documentation improvements
- Translation contributions

### Code of Conduct
Be respectful, inclusive, and constructive. Harassment or discrimination will not be tolerated.

---

## Troubleshooting

### Plugin Not Appearing After Activation
- **Check**: Elementor is activated and up to date
- **Fix**: Deactivate and reactivate the plugin; clear browser cache

### Arabic Numerals Not Displaying
- **Check**: Number format dropdown is set to "Arabic"
- **Fix**: Ensure WordPress language is set to an RTL language for proper rendering

### Animation Not Playing
- **Check**: Browser console for JavaScript errors
- **Fix**: Update browser to latest version; disable JS minification plugins temporarily

### More Help
[Create an issue](https://github.com/TarekNabil/Elementor-Extended-Counter-Widget/issues) with:
- WordPress version
- Elementor version
- PHP version
- Steps to reproduce the issue

---

## Changelog

### v1.0.0 — Initial Release
- ✨ Arabic numeral support
- ✨ Animated counter widget
- ✨ RTL language support
- ✨ Customizable animation duration and delay
- ✨ Prefix/suffix support

---

## License

This plugin is licensed under the **GNU General Public License v3.0 (GPLv3+)**. See [LICENSE](LICENSE) file for details.

---

## Author

**Tarek Nabil**  
[GitHub](https://github.com/TarekNabil) | [Portfolio](https://taeknabil.dev)

---

**Questions?** Open an issue or visit [GitHub Discussions](https://github.com/TarekNabil/Elementor-Extended-Counter-Widget/discussions) 
