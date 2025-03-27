<?php

namespace Hexlet\Tests;

use PHPUnit\Framework\TestCase;
use function Hexlet\Code\Differ\Parser\parseFile;
use function Hexlet\Code\Differ\Parser\parseJson;
use function Hexlet\Code\Differ\Parser\parseYaml;

class ParserTest extends TestCase
{
    private string $fixturesPath;

    protected function setUp(): void
    {
        $this->fixturesPath = __DIR__ . '/fixtures/';
    }

    public function testParseJson(): void
    {
        $content = '{"host": "hexlet.io","timeout": 50,"proxy": "123.234.53.22","follow": false}';
        $expected = [
            'host' => 'hexlet.io',
            'timeout' => 50,
            'proxy' => '123.234.53.22',
            'follow' => false
        ];

        $this->assertEquals($expected, parseJson($content, 'test.json'));
    }

    public function testParseJsonInvalid(): void
    {
        $content = '{"host": "hexlet.io", invalid json}';
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid JSON in test.json.');

        parseJson($content, 'test.json');
    }

    public function testParseYaml(): void
    {
        $content = "host: hexlet.io\ntimeout: 50\nproxy: 123.234.53.22\nfollow: false";
        $expected = [
            'host' => 'hexlet.io',
            'timeout' => 50,
            'proxy' => '123.234.53.22',
            'follow' => false
        ];

        $this->assertEquals($expected, parseYaml($content, 'test.yml'));
    }

    public function testParseYamlInvalid(): void
    {
        $content = "host: hexlet.io\ninvalid: yaml: :";
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid YAML in test.yml');

        parseYaml($content, 'test.yml');
    }

    public function testParseFileJson(): void
    {
        $filePath = $this->fixturesPath . 'file1.json';
        $expected = [
            'host' => 'hexlet.io',
            'timeout' => 50,
            'proxy' => '123.234.53.22',
            'follow' => false
        ];

        $this->assertEquals($expected, parseFile($filePath));
    }

    public function testParseFileYaml(): void
    {
        $filePath = $this->fixturesPath . 'file1.yml';
        $expected = [
            'host' => 'hexlet.io',
            'timeout' => 50,
            'proxy' => '123.234.53.22',
            'follow' => false
        ];

        $this->assertEquals($expected, parseFile($filePath));
    }

    public function testParseFileNotExists(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('not-exists.json is not a file.');

        parseFile('not-exists.json');
    }

    public function testParseFileUnsupportedFormat(): void
    {
        $filePath = $this->fixturesPath . 'file1.txt';
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unsupported file format: txt');

        parseFile($filePath);
    }
}