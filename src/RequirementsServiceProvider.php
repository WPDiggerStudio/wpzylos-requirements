<?php

declare(strict_types=1);

namespace WPZylos\Framework\Requirements;

use WPZylos\Framework\Core\Contracts\ApplicationInterface;
use WPZylos\Framework\Core\Contracts\ContextInterface;
use WPZylos\Framework\Core\ServiceProvider;
use WPZylos\Framework\Requirements\Contracts\RequirementsCheckerInterface;

/**
 * Requirements Service Provider
 *
 * Registers and validates plugin requirements on bootstrap.
 *
 * @package WPZylos\Framework\Requirements
 * @since   1.0.0
 */
class RequirementsServiceProvider extends ServiceProvider
{
    /**
     * Register package services.
     *
     * @param ApplicationInterface $app The application instance.
     *
     * @return void
     */
    public function register(ApplicationInterface $app): void
    {
        parent::register($app);

        $this->singleton('requirements.checker', function () {
            return $this->buildChecker();
        });
    }

    /**
     * Bootstrap the requirements checking.
     *
     * @param ApplicationInterface $app The application instance.
     *
     * @return void
     */
    public function boot(ApplicationInterface $app): void
    {
        $checker = $this->make('requirements.checker');

        if ($checker->hasFailures()) {
            $this->handleFailures($checker);
        }
    }

    /**
     * Build the requirements checker from config.
     *
     * @return RequirementsCheckerInterface
     */
    private function buildChecker(): RequirementsCheckerInterface
    {
        $configPath = $this->getConfigPath();
        $textDomain = $this->getTextDomain();

        if (!file_exists($configPath)) {
            return new RequirementsChecker();
        }

        $config = require $configPath;

        if (!is_array($config)) {
            return new RequirementsChecker();
        }

        return RequirementsConfig::fromArray($config, $textDomain);
    }

    /**
     * Handle failed requirements.
     *
     * @param RequirementsCheckerInterface $checker The requirements checker.
     *
     * @return void
     */
    private function handleFailures(RequirementsCheckerInterface $checker): void
    {
        $handler = new RequirementsErrorHandler(
            $checker->getErrors(),
            $this->getPluginFile(),
            $this->getPluginName(),
            $this->getTextDomain()
        );

        $handler->handle(true);
    }

    /**
     * Get the config file path.
     *
     * @return string
     */
    private function getConfigPath(): string
    {
        if ($this->app->container()->has('context')) {
            /** @var ContextInterface $context */
            $context = $this->make('context');

            return $context->path('config/requirements.php');
        }

        if (method_exists($this->app, 'configPath')) {
            return $this->app->configPath('requirements.php');
        }

        return '';
    }

    /**
     * Get the text domain.
     *
     * @return string
     */
    private function getTextDomain(): string
    {
        if ($this->app->container()->has('context')) {
            /** @var ContextInterface $context */
            $context = $this->make('context');

            return $context->textDomain();
        }

        return 'wpzylos';
    }

    /**
     * Get the plugin file path.
     *
     * @return string|null
     */
    private function getPluginFile(): ?string
    {
        if ($this->app->container()->has('context')) {
            /** @var ContextInterface $context */
            $context = $this->make('context');

            return $context->file();
        }

        if ($this->app->container()->has('plugin.file')) {
            return $this->make('plugin.file');
        }

        return null;
    }

    /**
     * Get the plugin name.
     *
     * @return string
     */
    private function getPluginName(): string
    {
        if ($this->app->container()->has('context')) {
            /** @var ContextInterface $context */
            $context = $this->make('context');

            return $context->slug();
        }

        return 'Plugin';
    }

    /**
     * Get the path to the stub file.
     *
     * @return string
     */
    public static function getStubPath(): string
    {
        return dirname(__DIR__) . '/stubs/requirements.php.stub';
    }

    /**
     * Publish the requirements config file to the target directory.
     *
     * @param string $targetPath Full path to the target config file.
     * @param bool $force Whether to overwrite an existing file.
     *
     * @return bool True if published, false if a file exists and force is false.
     *
     * @throws \RuntimeException If a stub file is not found.
     *
     * @example
     * // In your plugin setup:
     * RequirementsServiceProvider::publishConfig(  * __DIR__ . '/config/requirements.php'  *);
     */
    public static function publishConfig(string $targetPath, bool $force = false): bool
    {
        $stubPath = self::getStubPath();

        if (!file_exists($stubPath)) {
            throw new \RuntimeException('Requirements stub file not found: ' . $stubPath);
        }

        if (file_exists($targetPath) && !$force) {
            return false;
        }

        // Ensure target directory exists
        $targetDir = dirname($targetPath);
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                throw new \RuntimeException('Failed to create config directory: ' . $targetDir);
            }
        }

        return copy($stubPath, $targetPath);
    }

    /**
     * Get publishes array for framework integration.
     *
     * This method returns an array of stub files that can be published,
     * following Laravel-style package publishing conventions.
     *
     * @return array<string, string> Array of [source => target] paths.
     */
    public static function publishes(): array
    {
        return [
            self::getStubPath() => 'config/requirements.php',
        ];
    }
}
