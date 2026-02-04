<?php

declare(strict_types=1);

namespace WPZylos\Framework\Requirements;

/**
 * Requirements Error Handler
 *
 * Handles failed requirements with admin notices and wp_die.
 *
 * @package WPZylos\Framework\Requirements
 * @since   1.0.0
 */
class RequirementsErrorHandler
{
    /**
     * Array of error messages.
     *
     * @var array<string>
     */
    private array $errors;

    /**
     * Plugin file path for deactivation.
     */
    private ?string $pluginFile;

    /**
     * Plugin display name.
     */
    private string $pluginName;

    /**
     * Text domain for translations.
     */
    private string $textDomain;

    /**
     * Create a new error handler.
     *
     * @param array<string> $errors      Array of error messages.
     * @param string        $pluginFile  Main plugin file path.
     * @param string        $pluginName  Plugin display name.
     * @param string        $textDomain  Text domain for translations.
     */
    public function __construct(
        array $errors,
        ?string $pluginFile = null,
        string $pluginName = 'Plugin',
        string $textDomain = 'wpzylos'
    ) {
        $this->errors = $errors;
        $this->pluginFile = $pluginFile;
        $this->pluginName = $pluginName;
        $this->textDomain = $textDomain;
    }

    /**
     * Handle the requirement failures.
     *
     * @param bool $deactivate Whether to deactivate the plugin.
     * @return void
     */
    public function handle(bool $deactivate = true): void
    {
        if (empty($this->errors)) {
            return;
        }

        // Deactivate the plugin if requested
        if ($deactivate && $this->pluginFile && function_exists('deactivate_plugins')) {
            deactivate_plugins(plugin_basename($this->pluginFile));
        }

        // Show admin notice
        add_action('admin_notices', [$this, 'renderAdminNotice']);

        // If during activation, use wp_die
        if ($this->isActivating()) {
            wp_die(
                $this->renderErrorPage(),
                __('Plugin Requirements Not Met', $this->textDomain),
                ['back_link' => true]
            );
        }
    }

    /**
     * Render the admin notice.
     *
     * @return void
     */
    public function renderAdminNotice(): void
    {
        echo '<div class="notice notice-error">';
        echo '<p><strong>' . esc_html(
            sprintf(
                /* translators: %s: Plugin name */
                __('%s Activation Failed', $this->textDomain),
                $this->pluginName
            )
        ) . '</strong></p>';
        echo '<ul style="list-style-type: disc; margin-left: 20px;">';
        foreach ($this->errors as $error) {
            echo '<li>' . wp_kses_post($error) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }

    /**
     * Render the error page for wp_die.
     *
     * @return string
     */
    public function renderErrorPage(): string
    {
        $pluginName = esc_html($this->pluginName);
        $title = esc_html__('Plugin Activation Failed', $this->textDomain);
        $backLink = esc_url(admin_url('plugins.php'));
        $backText = esc_html__('Back to Plugins', $this->textDomain);

        $errorList = '';
        foreach ($this->errors as $error) {
            $errorList .= '<li>' . wp_kses_post($error) . '</li>';
        }

        return <<<HTML
        <style>
            .wpzylos-requirements {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
                    Oxygen, Ubuntu, Cantarell, sans-serif;
                max-width: 600px;
            }
            .wpzylos-requirements h1 {
                font-size: 26px;
                color: #dc3232;
                margin-bottom: 20px;
            }
            .wpzylos-requirements ul {
                margin: 0;
                padding-left: 20px;
            }
            .wpzylos-requirements li {
                margin-bottom: 10px;
                color: #555d66;
            }
            .wpzylos-requirements a {
                display: inline-block;
                margin-top: 20px;
                color: #0073aa;
                text-decoration: none;
            }
        </style>
        <div class="wpzylos-requirements">
            <h1>🚫 {$title}</h1>
            <p><strong>{$pluginName}</strong></p>
            <ul>
                {$errorList}
            </ul>
            <a href="{$backLink}">&laquo; {$backText}</a>
        </div>
        HTML;
    }

    /**
     * Check if we're in the activation process.
     *
     * @return bool
     */
    private function isActivating(): bool
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        return isset($_GET['action']) && $_GET['action'] === 'activate';
    }

    /**
     * Get the error messages.
     *
     * @return array<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
