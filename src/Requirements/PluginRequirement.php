<?php

declare(strict_types=1);

namespace WPZylos\Framework\Requirements\Requirements;

use WPZylos\Framework\Requirements\Contracts\RequirementInterface;

/**
 * Plugin Requirement
 *
 * Validates that a required plugin is installed and active with a minimum version.
 *
 * @package WPZylos\Framework\Requirements\Requirements
 * @since   1.0.0
 */
class PluginRequirement implements RequirementInterface
{
    /**
     * Plugin display name.
     */
    private string $pluginName;

    /**
     * Plugin slug (e.g., 'woocommerce/woocommerce.php').
     */
    private string $pluginSlug;

    /**
     * Minimum required version (optional).
     */
    private ?string $minVersion;

    /**
     * Text domain for translations.
     */
    private string $textDomain;

    /**
     * Cached error message.
     */
    private ?string $cachedError = null;

    /**
     * Create a new plugin requirement.
     *
     * @param string      $pluginName  Plugin display name.
     * @param string      $pluginSlug  Plugin slug (folder/file.php).
     * @param string|null $minVersion  Minimum required version, or null for any version.
     * @param string      $textDomain  Text domain for translations.
     */
    public function __construct(
        string $pluginName,
        string $pluginSlug,
        ?string $minVersion = null,
        string $textDomain = 'wpzylos'
    ) {
        $this->pluginName = $pluginName;
        $this->pluginSlug = $pluginSlug;
        $this->minVersion = $minVersion;
        $this->textDomain = $textDomain;
    }

    /**
     * {@inheritDoc}
     */
    public function isSatisfied(): bool
    {
        // Ensure plugin functions are available
        if (!function_exists('is_plugin_active')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $pluginPath = WP_PLUGIN_DIR . '/' . $this->pluginSlug;

        // Check if a plugin file exists
        if (!file_exists($pluginPath)) {
            $this->cachedError = sprintf(
                /* translators: %s: Plugin name */
                __('Required plugin <strong>%s</strong> is missing.', $this->textDomain),
                $this->pluginName
            );
            return false;
        }

        // Check if the plugin is active
        if (!is_plugin_active($this->pluginSlug)) {
            $this->cachedError = sprintf(
                /* translators: %s: Plugin name */
                __('Required plugin <strong>%s</strong> is not active.', $this->textDomain),
                $this->pluginName
            );
            return false;
        }

        // Check version if specified
        if ($this->minVersion !== null) {
            $pluginData = get_plugin_data($pluginPath, false, false);
            $currentVersion = $pluginData['Version'] ?? '0.0.0';

            if (version_compare($currentVersion, $this->minVersion, '<')) {
                $this->cachedError = sprintf(
                    // translators: 1: Plugin name, 2: Required version, 3: Current version
                    __(
                        'Required plugin <strong>%1$s</strong> must be version %2$s+ (installed: %3$s)',
                        $this->textDomain
                    ),
                    $this->pluginName,
                    $this->minVersion,
                    $currentVersion
                );
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getErrorMessage(): string
    {
        if ($this->cachedError === null) {
            // Trigger check to populate error
            $this->isSatisfied();
        }

        return $this->cachedError ?? sprintf(
            /* translators: %s: Plugin name */
            __('Required plugin <strong>%s</strong> requirement not met.', $this->textDomain),
            $this->pluginName
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'plugin_' . sanitize_key($this->pluginSlug);
    }
}
