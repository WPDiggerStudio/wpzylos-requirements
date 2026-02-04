<?php

declare(strict_types=1);

namespace WPZylos\Framework\Requirements;

use WPZylos\Framework\Requirements\Contracts\RequirementInterface;
use WPZylos\Framework\Requirements\Contracts\RequirementsCheckerInterface;

/**
 * Requirements Checker
 *
 * Main class for validating all registered requirements.
 *
 * @package WPZylos\Framework\Requirements
 * @since   1.0.0
 */
class RequirementsChecker implements RequirementsCheckerInterface
{
    /**
     * Registered requirements.
     *
     * @var array<RequirementInterface>
     */
    private array $requirements = [];

    /**
     * Failed requirements after check.
     *
     * @var array<RequirementInterface>
     */
    private array $failedRequirements = [];

    /**
     * Whether the check has been performed.
     */
    private bool $checked = false;

    /**
     * {@inheritDoc}
     */
    public function addRequirement(RequirementInterface $requirement): static
    {
        $this->requirements[] = $requirement;
        $this->checked = false; // Reset check state when requirements change

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function check(): bool
    {
        $this->failedRequirements = [];

        foreach ($this->requirements as $requirement) {
            if (!$requirement->isSatisfied()) {
                $this->failedRequirements[] = $requirement;
            }
        }

        $this->checked = true;

        return empty($this->failedRequirements);
    }

    /**
     * {@inheritDoc}
     */
    public function getFailedRequirements(): array
    {
        if (!$this->checked) {
            $this->check();
        }

        return $this->failedRequirements;
    }

    /**
     * {@inheritDoc}
     */
    public function getErrors(): array
    {
        $errors = [];

        foreach ($this->getFailedRequirements() as $requirement) {
            $errors[] = $requirement->getErrorMessage();
        }

        return $errors;
    }

    /**
     * {@inheritDoc}
     */
    public function hasFailures(): bool
    {
        if (!$this->checked) {
            $this->check();
        }

        return !empty($this->failedRequirements);
    }

    /**
     * Get all registered requirements.
     *
     * @return array<RequirementInterface>
     */
    public function getRequirements(): array
    {
        return $this->requirements;
    }

    /**
     * Clear all registered requirements.
     *
     * @return static
     */
    public function clear(): static
    {
        $this->requirements = [];
        $this->failedRequirements = [];
        $this->checked = false;

        return $this;
    }
}
