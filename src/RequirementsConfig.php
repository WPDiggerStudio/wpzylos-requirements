<?php

declare(strict_types=1);

namespace WPZylos\Framework\Requirements;

use WPZylos\Framework\Requirements\Contracts\RequirementsCheckerInterface;
use WPZylos\Framework\Requirements\Requirements\MultisiteRequirement;
use WPZylos\Framework\Requirements\Requirements\PhpExtensionRequirement;
use WPZylos\Framework\Requirements\Requirements\PhpVersionRequirement;
use WPZylos\Framework\Requirements\Requirements\PluginRequirement;
use WPZylos\Framework\Requirements\Requirements\WordPressVersionRequirement;

/**
 * Requirements Config
 *
 * Builds requirements from configuration array.
 *
 * @package WPZylos\Framework\Requirements
 * @since   1.0.0
 */
class RequirementsConfig
{
    /**
     * Build a requirements checker from configuration.
     *
     * @param array<string, mixed> $config     Configuration array.
     * @param string               $textDomain Text domain for translations.
     * @return RequirementsCheckerInterface
     */
    public static function fromArray(array $config, string $textDomain = 'wpzylos'): RequirementsCheckerInterface
    {
        $checker = new RequirementsChecker();

        // PHP Version
        if (!empty($config['min_php_version'])) {
            $checker->addRequirement(
                new PhpVersionRequirement($config['min_php_version'], $textDomain)
            );
        }

        // WordPress Version
        if (!empty($config['min_wp_version'])) {
            $checker->addRequirement(
                new WordPressVersionRequirement($config['min_wp_version'], $textDomain)
            );
        }

        // Multisite Compatibility
        if (isset($config['is_multisite_compatible'])) {
            $checker->addRequirement(
                new MultisiteRequirement((bool) $config['is_multisite_compatible'], $textDomain)
            );
        }

        // PHP Extensions
        if (!empty($config['php_extensions']) && is_array($config['php_extensions'])) {
            foreach ($config['php_extensions'] as $extension) {
                $checker->addRequirement(
                    new PhpExtensionRequirement($extension, $textDomain)
                );
            }
        }

        // Required Plugins
        if (!empty($config['required_plugins']) && is_array($config['required_plugins'])) {
            foreach ($config['required_plugins'] as $pluginName => $pluginConfig) {
                $checker->addRequirement(
                    new PluginRequirement(
                        $pluginName,
                        $pluginConfig['plugin_slug'] ?? '',
                        $pluginConfig['min_plugin_version'] ?? null,
                        $textDomain
                    )
                );
            }
        }

        return $checker;
    }

    /**
     * Build a requirements checker from a config file path.
     *
     * @param string $configPath Path to the requirements.php config file.
     * @param string $textDomain Text domain for translations.
     * @return RequirementsCheckerInterface
     */
    public static function fromFile(string $configPath, string $textDomain = 'wpzylos'): RequirementsCheckerInterface
    {
        if (!file_exists($configPath)) {
            return new RequirementsChecker();
        }

        $config = require $configPath;

        if (!is_array($config)) {
            return new RequirementsChecker();
        }

        return self::fromArray($config, $textDomain);
    }
}
