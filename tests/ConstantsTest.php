<?php

namespace Hexlet\Tests;

use PHPUnit\Framework\TestCase;
use const Hexlet\Code\Differ\ERROR_MESSAGES;
use const Hexlet\Code\Differ\SUPPORTED_FORMATS;

class ConstantsTest extends TestCase
{
    public function testErrorMessages(): void
    {
        $this->assertArrayHasKey('INVALID_JSON', ERROR_MESSAGES);
        $this->assertArrayHasKey('FILE_NOT_FOUND', ERROR_MESSAGES);
        $this->assertArrayHasKey('UNSUPPORTED_FORMAT', ERROR_MESSAGES);

        $this->assertEquals('Invalid JSON in %s.', ERROR_MESSAGES['INVALID_JSON']);
        $this->assertEquals('Path %s does not exist.', ERROR_MESSAGES['FILE_NOT_FOUND']);
        $this->assertEquals('Unsupported file format: %s', ERROR_MESSAGES['UNSUPPORTED_FORMAT']);
    }

    public function testSupportedFormats(): void
    {
        $this->assertContains('json', SUPPORTED_FORMATS);
        $this->assertContains('yaml', SUPPORTED_FORMATS);
        $this->assertContains('yml', SUPPORTED_FORMATS);
        $this->assertCount(3, SUPPORTED_FORMATS);
    }
}