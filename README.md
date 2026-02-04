# WPZylos Requirements

[![PHP Version](https://img.shields.io/badge/php-%5E8.0-blue)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![GitHub](https://img.shields.io/badge/GitHub-WPDiggerStudio-181717?logo=github)](https://github.com/WPDiggerStudio/wpzylos-requirements)

Environment and dependency validation for WPZylos framework plugins.

📖 **[Full Documentation](https://wpzylos.com/docs/latest/packages/wpzylos-requirements/)** | 🐛 **[Report Issues](https://github.com/WPDiggerStudio/wpzylos-requirements/issues)**

---

## ✨ Features

- **PHP Version Check** — Validate minimum PHP version
- **WordPress Version Check** — Validate minimum WordPress version
- **PHP Extensions** — Ensure required extensions are loaded
- **Plugin Dependencies** — Check required plugins are active with minimum versions
- **Multisite Compatibility** — Validate multisite environment compatibility
- **Automatic Deactivation** — Deactivates plugin when requirements fail
- **Admin Notices** — User-friendly error messages in WordPress admin
- **Config Publishing** — Publish stub config to your plugin

---

## 📋 Requirements

| Requirement  | Version |
| ------------ | ------- |
| PHP          | ^8.0    |
| wpzylos-core | ^1.0    |

---

## 🚀 Installation

```bash
composer require wpdiggerstudio/wpzylos-requirements
```

### Publish Configuration

```php
use WPZylos\Framework\Requirements\RequirementsServiceProvider;

// Publish config stub to your plugin
RequirementsServiceProvider::publishConfig(
    __DIR__ . '/config/requirements.php'
);
```

---

## 📖 Quick Start

### 1. Create Requirements Config

Create `config/requirements.php` in your plugin:

```php
<?php

return [
    'min_php_version'         => '8.1',
    'min_wp_version'          => '6.0',
    'is_multisite_compatible' => true,
    'php_extensions'          => [
        'json',
        'mbstring',
    ],
    'required_plugins'        => [
        'WooCommerce' => [
            'plugin_slug'        => 'woocommerce/woocommerce.php',
            'min_plugin_version' => '8.0.0',
        ],
    ],
];
```

### 2. Register Service Provider

In your `bootstrap/app.php`:

```php
use WPZylos\Framework\Requirements\RequirementsServiceProvider;

$app->register(RequirementsServiceProvider::class);
```

That's it! Requirements are automatically validated on plugin activation.

---

## 🏗️ How It Works

```
┌─────────────────────────────────────────────────────────────┐
│                    Plugin Activation                        │
├─────────────────────────────────────────────────────────────┤
│  1. RequirementsServiceProvider::register()                 │
│     └── Loads config/requirements.php                       │
│     └── Builds RequirementsChecker with all requirements    │
│                                                             │
│  2. RequirementsServiceProvider::boot()                     │
│     └── Runs all requirement checks                         │
│     └── If failures: deactivate + show admin notice         │
│     └── If on activation: wp_die with error page            │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔧 Standalone Usage

Use directly without the service provider:

```php
use WPZylos\Framework\Requirements\RequirementsChecker;
use WPZylos\Framework\Requirements\Requirements\PhpVersionRequirement;
use WPZylos\Framework\Requirements\Requirements\WordPressVersionRequirement;
use WPZylos\Framework\Requirements\Requirements\PhpExtensionRequirement;
use WPZylos\Framework\Requirements\Requirements\PluginRequirement;

$checker = new RequirementsChecker();

$checker
    ->addRequirement(new PhpVersionRequirement('8.1'))
    ->addRequirement(new WordPressVersionRequirement('6.0'))
    ->addRequirement(new PhpExtensionRequirement('json'))
    ->addRequirement(new PluginRequirement(
        'WooCommerce',
        'woocommerce/woocommerce.php',
        '8.0.0'
    ));

if ($checker->hasFailures()) {
    foreach ($checker->getErrors() as $error) {
        error_log($error);
    }
}
```

### Config-Based Usage

```php
use WPZylos\Framework\Requirements\RequirementsConfig;

$checker = RequirementsConfig::fromFile(
    __DIR__ . '/config/requirements.php',
    'my-plugin' // text domain
);

if ($checker->hasFailures()) {
    // Handle failures
}
```

### Error Handling

```php
use WPZylos\Framework\Requirements\RequirementsErrorHandler;

$handler = new RequirementsErrorHandler(
    $checker->getErrors(),
    __FILE__,           // plugin file for deactivation
    'My Plugin',        // plugin name
    'my-plugin'         // text domain
);

$handler->handle(true); // true = deactivate plugin
```

---

## 📦 Available Requirements

| Class                         | Purpose                                 | Config Key                |
| ----------------------------- | --------------------------------------- | ------------------------- |
| `PhpVersionRequirement`       | Validates minimum PHP version           | `min_php_version`         |
| `WordPressVersionRequirement` | Validates minimum WordPress version     | `min_wp_version`          |
| `PhpExtensionRequirement`     | Validates PHP extension is loaded       | `php_extensions`          |
| `PluginRequirement`           | Validates plugin is active with version | `required_plugins`        |
| `MultisiteRequirement`        | Validates multisite compatibility       | `is_multisite_compatible` |

---

## 🛡️ Error Display

### Admin Notice

When requirements fail, a dismissible admin notice appears:

```
┌────────────────────────────────────────────────────────────┐
│ ❌ My Plugin Activation Failed                             │
│                                                            │
│ • PHP 8.1+ is required. You are running: 7.4.33           │
│ • Required plugin WooCommerce is not active.              │
│ • Required PHP extension missing: mbstring                │
└────────────────────────────────────────────────────────────┘
```

### Activation Error Page

During plugin activation, a styled error page is displayed with wp_die():

```
🚫 Plugin Activation Failed

My Plugin

• PHP 8.1+ is required. You are running: 7.4.33
• WordPress 6.0+ is required. You are running: 5.9.3

« Back to Plugins
```

---

## 📚 Documentation

- [Overview](docs/overview.md) — Requirements system architecture
- [Installation](docs/installation.md) — Setup guide
- [Usage](docs/usage.md) — Core patterns
- [Configuration](docs/configuration.md) — Config file options
- [API Reference](docs/api-reference.md) — Class and method reference
- [Examples](docs/examples.md) — Real-world usage patterns
- [Testing](docs/testing.md) — Testing requirements
- [Security](docs/security.md) — Security considerations
- [Troubleshooting](docs/troubleshooting.md) — Common issues

---

## 📦 Related Packages

| Package                                                                | Description              |
| ---------------------------------------------------------------------- | ------------------------ |
| [wpzylos-core](https://github.com/WPDiggerStudio/wpzylos-core)         | Application foundation   |
| [wpzylos-config](https://github.com/WPDiggerStudio/wpzylos-config)     | Configuration management |
| [wpzylos-scaffold](https://github.com/WPDiggerStudio/wpzylos-scaffold) | Plugin template          |

---

## ☕ Support the Project

If you find this package helpful, consider buying me a coffee! Your support helps maintain and improve the WPZylos ecosystem.

<a href="https://www.paypal.com/donate/?hosted_button_id=66U4L3HG4TLCC" target="_blank">
  <img src="https://img.shields.io/badge/Donate-PayPal-blue.svg?style=for-the-badge&logo=paypal" alt="Donate with PayPal" />
</a>

---

## 📄 License

MIT License. See [LICENSE](LICENSE) for details.

---

## 🤝 Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

---

**Made with ❤️ by [WPDiggerStudio](https://github.com/WPDiggerStudio)**
