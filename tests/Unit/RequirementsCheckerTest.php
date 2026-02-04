<?php

declare(strict_types=1);

namespace WPZylos\Framework\Requirements\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Requirements\RequirementsChecker;
use WPZylos\Framework\Requirements\Requirements\PhpVersionRequirement;
use WPZylos\Framework\Requirements\Requirements\PhpExtensionRequirement;

/**
 * Requirements Checker Test
 *
 * @covers \WPZylos\Framework\Requirements\RequirementsChecker
 */
class RequirementsCheckerTest extends TestCase
{
    public function testEmptyCheckerHasNoFailures(): void
    {
        $checker = new RequirementsChecker();

        $this->assertFalse($checker->hasFailures());
        $this->assertEmpty($checker->getErrors());
        $this->assertEmpty($checker->getFailedRequirements());
    }

    public function testPassingRequirements(): void
    {
        $checker = new RequirementsChecker();

        // These should pass on any modern PHP
        $checker->addRequirement(new PhpVersionRequirement('7.0'));
        $checker->addRequirement(new PhpExtensionRequirement('json'));

        $this->assertTrue($checker->check());
        $this->assertFalse($checker->hasFailures());
        $this->assertEmpty($checker->getErrors());
    }

    public function testFailingPhpVersion(): void
    {
        $checker = new RequirementsChecker();

        // Require PHP 99.0 which doesn't exist
        $checker->addRequirement(new PhpVersionRequirement('99.0'));

        $this->assertFalse($checker->check());
        $this->assertTrue($checker->hasFailures());
        $this->assertCount(1, $checker->getErrors());
        $this->assertStringContainsString('PHP 99.0+', $checker->getErrors()[0]);
    }

    public function testFailingExtension(): void
    {
        $checker = new RequirementsChecker();

        // Require an extension that doesn't exist
        $checker->addRequirement(new PhpExtensionRequirement('nonexistent_extension_xyz'));

        $this->assertFalse($checker->check());
        $this->assertTrue($checker->hasFailures());
        $this->assertCount(1, $checker->getErrors());
        $this->assertStringContainsString('nonexistent_extension_xyz', $checker->getErrors()[0]);
    }

    public function testMultipleFailures(): void
    {
        $checker = new RequirementsChecker();

        $checker->addRequirement(new PhpVersionRequirement('99.0'));
        $checker->addRequirement(new PhpExtensionRequirement('fake_ext_1'));
        $checker->addRequirement(new PhpExtensionRequirement('fake_ext_2'));

        $this->assertFalse($checker->check());
        $this->assertCount(3, $checker->getFailedRequirements());
        $this->assertCount(3, $checker->getErrors());
    }

    public function testClearRequirements(): void
    {
        $checker = new RequirementsChecker();

        $checker->addRequirement(new PhpVersionRequirement('99.0'));
        $checker->check();

        $this->assertTrue($checker->hasFailures());

        $checker->clear();

        $this->assertFalse($checker->hasFailures());
        $this->assertEmpty($checker->getRequirements());
    }

    public function testMethodChaining(): void
    {
        $checker = new RequirementsChecker();

        $result = $checker
            ->addRequirement(new PhpVersionRequirement('7.0'))
            ->addRequirement(new PhpExtensionRequirement('json'));

        $this->assertSame($checker, $result);
        $this->assertCount(2, $checker->getRequirements());
    }
}
