<?php

declare(strict_types=1);

namespace WPZylos\Framework\Requirements\Requirements;

use WPZylos\Framework\Requirements\Contracts\RequirementInterface;

/**
 * Multisite Requirement
 *
 * Validates multisite compatibility.
 *
 * @package WPZylos\Framework\Requirements\Requirements
 * @since   1.0.0
 */
class MultisiteRequirement implements RequirementInterface
{
    /**
     * Whether the plugin is multisite compatible.
     */
    private bool $isCompatible;

    /**
     * Text domain for translations.
     */
    private string $textDomain;

    /**
     * Create a new multisite requirement.
     *
     * @param bool   $isCompatible Whether the plugin is multisite compatible.
     * @param string $textDomain   Text domain for translations.
     */
    public function __construct(bool $isCompatible = true, string $textDomain = 'wpzylos')
    {
        $this->isCompatible = $isCompatible;
        $this->textDomain = $textDomain;
    }

    /**
     * {@inheritDoc}
     */
    public function isSatisfied(): bool
    {
        // If compatible with multisite, always passes
        if ($this->isCompatible) {
            return true;
        }

        // If not compatible, fail only on multisite installations
        return !is_multisite();
    }

    /**
     * {@inheritDoc}
     */
    public function getErrorMessage(): string
    {
        return __('This plugin is not compatible with WordPress Multisite.', $this->textDomain);
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'multisite_compatibility';
    }
}
