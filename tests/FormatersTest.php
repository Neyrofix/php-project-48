<?php

namespace Hexlet\Tests;

use PHPUnit\Framework\TestCase;

use function Hexlet\Code\Differ\Formaters\formatValue;
use function Hexlet\Code\Differ\Formaters\formatDiffLine;

class FormatersTest extends TestCase
{
    public function testFormatValueWithBooleanTrue(): void
    {
        $this->assertEquals('true', formatValue(true));
    }

    public function testFormatValueWithBooleanFalse(): void
    {
        $this->assertEquals('false', formatValue(false));
    }

    public function testFormatValueWithInteger(): void
    {
        $this->assertEquals('123', formatValue(123));
    }

    public function testFormatValueWithString(): void
    {
        $this->assertEquals('test', formatValue('test'));
    }

    public function testFormatValueWithArray(): void
    {
        $array = ['key1' => 'value1', 'key2' => 'value2'];
        $this->assertEquals(json_encode($array), formatValue($array));
    }

    public function testFormatDiffLine(): void
    {
        $prefix = '+ ';
        $key = 'host';
        $value = 'hexlet.io';
        $expected = '+ host: hexlet.io';
        $this->assertEquals($expected, formatDiffLine($prefix, $key, $value));
    }

    public function testFormatDiffLineWithArrayValue(): void
    {
        $prefix = '- ';
        $key = 'settings';
        $value = ['setting1' => 'value1', 'setting2' => 'value2'];
        $expected = '- settings: ' . json_encode($value);
        $this->assertEquals($expected, formatDiffLine($prefix, $key, $value));
    }
}
