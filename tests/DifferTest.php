<?php

namespace Hexlet\Tests;

use PHPUnit\Framework\TestCase;
use function Hexlet\Code\Differ\genDiff;
use function Hexlet\Code\Differ\generateDiff;
use function Hexlet\Code\Differ\formatValue;
use function Hexlet\Code\Differ\Parser\normalizePath;

class DifferTest extends TestCase
{
    private string $fixturesPath;

    protected function setUp(): void
    {
        $this->fixturesPath = __DIR__ . '/fixtures/';
    }

    public function testFormatValue(): void
    {
        $this->assertEquals('true', formatValue(true));
        $this->assertEquals('false', formatValue(false));
        $this->assertEquals('123', formatValue(123));
        $this->assertEquals('test', formatValue('test'));
    }

    public function testNormalizePath(): void
    {
        $path = $this->fixturesPath . 'file1.json';
        $this->assertEquals(realpath($path), normalizePath($path));
    }

    public function testNormalizePathNotExists(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Path not-exists.json does not exist.');

        normalizePath('not-exists.json');
    }

    public function testGenerateDiff(): void
    {
        $firstArray = [
            'host' => 'hexlet.io',
            'timeout' => 50,
            'proxy' => '123.234.53.22',
            'follow' => false
        ];

        $secondArray = [
            'timeout' => 20,
            'verbose' => true,
            'host' => 'hexlet.io'
        ];

        $expected = "- follow: false\n  host: hexlet.io\n- proxy: 123.234.53.22\n- timeout: 50\n+ timeout: 20\n+ verbose: true";

        $this->assertEquals($expected, generateDiff($firstArray, $secondArray));
    }

    public function testGenDiffJson(): void
    {
        $firstPath = $this->fixturesPath . 'file1.json';
        $secondPath = $this->fixturesPath . 'file2.json';

        $expected = "- follow: false\n  host: hexlet.io\n- proxy: 123.234.53.22\n- timeout: 50\n+ timeout: 20\n+ verbose: true";

        $this->assertEquals($expected, genDiff($firstPath, $secondPath));
    }

    public function testGenDiffYaml(): void
    {
        $firstPath = $this->fixturesPath . 'file1.yml';
        $secondPath = $this->fixturesPath . 'file2.yml';

        $expected = "- follow: false\n  host: hexlet.io\n- proxy: 123.234.53.22\n- timeout: 50\n+ timeout: 20\n+ verbose: true";

        $this->assertEquals($expected, genDiff($firstPath, $secondPath));
    }

    public function testGenDiffMixedFormats(): void
    {
        $firstPath = $this->fixturesPath . 'file1.json';
        $secondPath = $this->fixturesPath . 'file2.yml';

        $expected = "- follow: false\n  host: hexlet.io\n- proxy: 123.234.53.22\n- timeout: 50\n+ timeout: 20\n+ verbose: true";

        $this->assertEquals($expected, genDiff($firstPath, $secondPath));
    }
}
