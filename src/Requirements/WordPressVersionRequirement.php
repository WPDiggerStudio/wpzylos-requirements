<?php

declare(strict_types=1);

namespace WPZylos\Framework\Requirements\Requirements;

use WPZylos\Framework\Requirements\Contracts\RequirementInterface;

/**
 * WordPress Version Requirement
 *
 * Validates that the current WordPress version meets the minimum requirement.
 *
 * @package WPZylos\Framework\Requirements\Requirements
 * @since   1.0.0
 */
class WordPressVersionRequirement implements RequirementInterface
{
    /**
     * Minimum required WordPress version.
     */
    private string $minVersion;

    /**
     * Text domain for translations.
     */
    private string $textDomain;

    /**
     * Create a new WordPress version requirement.
     *
     * @param string $minVersion  Minimum required WordPress version (e.g., '6.0').
     * @param string $textDomain  Text domain for translations.
     */
    public function __construct(string $minVersion, string $textDomain = 'wpzylos')
    {
        $this->minVersion = $minVersion;
        $this->textDomain = $textDomain;
    }

    /**
     * {@inheritDoc}
     */
    public function isSatisfied(): bool
    {
        global $wp_version;

        return version_compare($wp_version, $this->minVersion, '>=');
    }

    /**
     * {@inheritDoc}
     */
    public function getErrorMessage(): string
    {
        global $wp_version;

        return sprintf(
            /* translators: 1: Required WordPress version, 2: Current WordPress version */
            __('WordPress %1$s+ is required. You are running: %2$s', $this->textDomain),
            $this->minVersion,
            $wp_version
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'wordpress_version';
    }
}
