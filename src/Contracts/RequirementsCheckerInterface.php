<?php

declare(strict_types=1);

namespace WPZylos\Framework\Requirements\Contracts;

/**
 * Requirements Checker Interface
 *
 * Contract for the main requirements checker.
 *
 * @package WPZylos\Framework\Requirements\Contracts
 * @since   1.0.0
 */
interface RequirementsCheckerInterface
{
    /**
     * Add a requirement to be checked.
     *
     * @param RequirementInterface $requirement The requirement to add.
     *
     * @return static Returns self for method chaining.
     */
    public function addRequirement(RequirementInterface $requirement): static;

    /**
     * Check all registered requirements.
     *
     * @return bool True if all requirements are satisfied, false otherwise.
     */
    public function check(): bool;

    /**
     * Get all failed requirements.
     *
     * @return array<RequirementInterface> Array of failed requirements.
     */
    public function getFailedRequirements(): array;

    /**
     * Get all error messages from failed requirements.
     *
     * @return array<string> Array of error messages.
     */
    public function getErrors(): array;

    /**
     * Check if there are any failed requirements.
     *
     * @return bool True if there are failures, false otherwise.
     */
    public function hasFailures(): bool;
}
