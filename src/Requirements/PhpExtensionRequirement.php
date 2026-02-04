<?php

declare(strict_types=1);

namespace WPZylos\Framework\Requirements\Requirements;

use WPZylos\Framework\Requirements\Contracts\RequirementInterface;

/**
 * PHP Extension Requirement
 *
 * Validates that a required PHP extension is loaded.
 *
 * @package WPZylos\Framework\Requirements\Requirements
 * @since   1.0.0
 */
class PhpExtensionRequirement implements RequirementInterface
{
    /**
     * Required extension name.
     */
    private string $extension;

    /**
     * Text domain for translations.
     */
    private string $textDomain;

    /**
     * Create a new PHP extension requirement.
     *
     * @param string $extension   Required extension name (e.g., 'json', 'mbstring').
     * @param string $textDomain  Text domain for translations.
     */
    public function __construct(string $extension, string $textDomain = 'wpzylos')
    {
        $this->extension = $extension;
        $this->textDomain = $textDomain;
    }

    /**
     * {@inheritDoc}
     */
    public function isSatisfied(): bool
    {
        return extension_loaded($this->extension);
    }

    /**
     * {@inheritDoc}
     */
    public function getErrorMessage(): string
    {
        return sprintf(
            /* translators: %s: PHP extension name */
            __('Required PHP extension missing: %s', $this->textDomain),
            $this->extension
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'php_extension_' . $this->extension;
    }
}
