<?php

declare(strict_types=1);

namespace WPZylos\Framework\Requirements\Contracts;

/**
 * Requirement Interface
 *
 * Contract for individual requirement validators.
 *
 * @package WPZylos\Framework\Requirements\Contracts
 * @since   1.0.0
 */
interface RequirementInterface
{
    /**
     * Check if the requirement is satisfied.
     *
     * @return bool True if the requirement is met, false otherwise.
     */
    public function isSatisfied(): bool;

    /**
     * Get the error message when the requirement fails.
     *
     * @return string Human-readable error message.
     */
    public function getErrorMessage(): string;

    /**
     * Get the requirement name/identifier.
     *
     * @return string Requirement name for logging/debugging.
     */
    public function getName(): string;
}
