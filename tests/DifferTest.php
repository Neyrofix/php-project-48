<?php

namespace Hexlet\Tests;

use PHPUnit\Framework\TestCase;

use function Hexlet\Code\Differ\genDiff;
use function Hexlet\Code\Differ\generateDiff;

class DifferTest extends TestCase
{
    private string $fixturesPath;

    private const EXPECTED_DIFF =
    "- follow: false\n  host: hexlet.io\n- proxy: 123.234.53.22\n- timeout: 50\n+ timeout: 20\n+ verbose: true";

    //private const EXPECTED_DIFF_JSON =
    //'{"- follow":false,"  host":"hexlet.io","- proxy":"123.234.53.22",
    //"- timeout":50,"+ timeout":20,"+ verbose":true}';

    protected function setUp(): void
    {
        $this->fixturesPath = __DIR__ . '/fixtures/';
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

        $this->assertEquals(self::EXPECTED_DIFF, generateDiff($firstArray, $secondArray));
    }

    public function testGenDiffJson(): void
    {
        $firstPath = $this->fixturesPath . 'file1.json';
        $secondPath = $this->fixturesPath . 'file2.json';

        $this->assertEquals(self::EXPECTED_DIFF, genDiff($firstPath, $secondPath));
    }

    public function testGenDiffYaml(): void
    {
        $firstPath = $this->fixturesPath . 'file1.yml';
        $secondPath = $this->fixturesPath . 'file2.yml';

        $this->assertEquals(self::EXPECTED_DIFF, genDiff($firstPath, $secondPath));
    }

    public function testGenDiffMixedFormats(): void
    {
        $firstPath = $this->fixturesPath . 'file1.json';
        $secondPath = $this->fixturesPath . 'file2.yml';

        $this->assertEquals(self::EXPECTED_DIFF, genDiff($firstPath, $secondPath));
    }

    public function testGenDiffDifferentFormats(): void
    {
        $firstPath = $this->fixturesPath . 'file1.json';
        $secondPath = $this->fixturesPath . 'file2.json';

        $this->assertEquals(self::EXPECTED_DIFF, genDiff($firstPath, $secondPath));
        //$this->assertEquals(self::EXPECTED_DIFF_JSON, genDiff($firstPath, $secondPath, 'json'));
    }

    public function testGenDiffEmptyFiles(): void
    {
        $firstPath = $this->fixturesPath . 'empty1.json';
        $secondPath = $this->fixturesPath . 'empty2.json';

        $this->assertEquals('', genDiff($firstPath, $secondPath));
    }
}
