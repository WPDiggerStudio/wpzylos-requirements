<?php

declare(strict_types=1);

namespace WPZylos\Framework\Requirements\Requirements;

use WPZylos\Framework\Requirements\Contracts\RequirementInterface;

/**
 * PHP Version Requirement
 *
 * Validates that the current PHP version meets the minimum requirement.
 *
 * @package WPZylos\Framework\Requirements\Requirements
 * @since   1.0.0
 */
class PhpVersionRequirement implements RequirementInterface
{
    /**
     * Minimum required PHP version.
     */
    private string $minVersion;

    /**
     * Text domain for translations.
     */
    private string $textDomain;

    /**
     * Create a new PHP version requirement.
     *
     * @param string $minVersion  Minimum required PHP version (e.g., '8.0').
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
        return version_compare(PHP_VERSION, $this->minVersion, '>=');
    }

    /**
     * {@inheritDoc}
     */
    public function getErrorMessage(): string
    {
        return sprintf(
            /* translators: 1: Required PHP version, 2: Current PHP version */
            __('PHP %1$s+ is required. You are running: %2$s', $this->textDomain),
            $this->minVersion,
            PHP_VERSION
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'php_version';
    }
}
