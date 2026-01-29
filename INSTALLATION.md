# Installation Guide

## Requirements

- **Kirby CMS**: 5.0 or higher
- **PHP**: 8.2 or higher

## Installation Methods

### Method 1: Composer (Recommended)

```bash
composer require nerdcel/kirby-languagerelease
```

The plugin will be automatically installed to `site/plugins/languagerelease`.

### Method 2: Download ZIP

1. Go to [Releases](https://github.com/nerdcel/kirby-languagerelease/releases)
2. Download the latest `kirby-languagerelease-x.x.x.zip`
3. Extract to `site/plugins/languagerelease`

### Method 3: Git Submodule

```bash
cd your-kirby-project
git submodule add https://github.com/nerdcel/kirby-languagerelease.git site/plugins/languagerelease
```

### Method 4: Git Clone

```bash
cd site/plugins
git clone https://github.com/nerdcel/kirby-languagerelease.git languagerelease
```

## Post-Installation Setup

### 1. Add Field to Blueprints

Edit your page blueprints (e.g., `site/blueprints/pages/default.yml`):

```yaml
fields:
  languageReleased:
    type: toggle
    label: Language Released
    default: false
```

### 2. Configure Plugin (Optional)

Create or edit `site/config/config.php`:

```php
return [
    'nerdcel.languagerelease' => [
        'fieldName' => 'languageReleased',
        'behavior' => '404', // Options: '404', 'redirect', 'default-content'
    ],
];
```

### 3. Release Your Language Variants

⚠️ **Important**: After installation, all language variants (except default) are blocked from public access.

To release a language variant:
1. Open the Panel
2. Navigate to a page
3. Switch to the language you want to release
4. Check the "Release Language Variant" checkbox
5. Save

## Verification

### Check Installation

Visit the Panel and open any page. You should see the language release checkbox when viewing non-default languages.

### Test Frontend Access

1. Try accessing an unreleased language variant in frontend → Should show 404 (or redirect/default content based on config)
2. Release the language in Panel
3. Try accessing it again → Should work normally

### Test Preview Mode

1. In Panel, open an unreleased page
2. Click the Preview button
3. The preview should work despite the page being unreleased

## Updating

### Via Composer

```bash
composer update nerdcel/kirby-languagerelease
```

### Via Download

1. Download new version
2. Replace `site/plugins/languagerelease` folder
3. Clear cache if necessary

### Via Git

```bash
cd site/plugins/languagerelease
git pull origin main
```

## Troubleshooting

### Issue: Plugin not showing in Panel

**Solution:**
- Clear Kirby cache: Delete `site/cache` folder contents
- Check file permissions
- Verify plugin is in `site/plugins/languagerelease`

### Issue: Composer installation fails

**Solution:**
- Check PHP version: `php -v` (must be 8.2+)
- Check Kirby version in `composer.json` (must be 5.0+)
- Run `composer diagnose`

### Issue: All pages show 404 after installation

**Solution:** This is expected! Release your language variants through the Panel.

## Uninstallation

### Via Composer

```bash
composer remove nerdcel/kirby-languagerelease
```

### Manual

1. Delete `site/plugins/languagerelease` folder
2. Remove plugin configuration from `site/config/config.php`
3. Optionally remove the field from your blueprints

**Note**: Removing the plugin will make all language variants accessible again immediately.

## Next Steps

- Read the [Configuration Guide](CONFIGURATION.md)
- Learn about [Frontend Access Control](FRONTEND-ACCESS.md)
- Check the [README](README.md) for advanced usage

