<?php

namespace Hexlet\Tests;

use PHPUnit\Framework\TestCase;

use function Hexlet\Code\Differ\genDiff;
use function Hexlet\Code\Differ\generateDiff;
use function Hexlet\Code\Differ\makeAddedDiff;
use function Hexlet\Code\Differ\makeRemovedDiff;
use function Hexlet\Code\Differ\makeUnchangedDiff;
use function Hexlet\Code\Differ\makeChangedDiff;
use function Hexlet\Code\Differ\makeNestedDiff;
use function Hexlet\Code\Differ\nestedDiff;

class DifferTest extends TestCase
{
    private string|false $fixturesPath;

    protected function setUp(): void
    {
        $this->fixturesPath = realpath(__DIR__ . '/fixtures');
        $this->assertNotFalse($this->fixturesPath, 'Fixtures path not found');
    }

    public function testMakeAddedDiff(): void
    {
        $value = 'test';
        $expected = [
            'type' => 'added',
            'class' => 'item',
            'value' => $value,
        ];

        $this->assertEquals($expected, makeAddedDiff($value));
    }

    public function testMakeRemovedDiff(): void
    {
        $value = 'test';
        $expected = [
            'type' => 'removed',
            'class' => 'item',
            'value' => $value,
        ];

        $this->assertEquals($expected, makeRemovedDiff($value));
    }

    public function testMakeUnchangedDiff(): void
    {
        $value = 'test';
        $expected = [
            'type' => 'unchanged',
            'class' => 'item',
            'value' => $value,
        ];

        $this->assertEquals($expected, makeUnchangedDiff($value));
    }

    public function testMakeChangedDiff(): void
    {
        $value1 = 'test1';
        $value2 = 'test2';
        $expected = [
            'type' => 'changed',
            'class' => 'item',
            'value' => $value1,
            'newValue' => $value2
        ];

        $this->assertEquals($expected, makeChangedDiff($value1, $value2));
    }

    public function testMakeNestedDiff(): void
    {
        $value = ['key' => 'value'];
        $expected = [
            'type' => 'unchanged',
            'class' => 'node',
            'children' => $value
        ];

        $this->assertEquals($expected, makeNestedDiff($value));
    }

    public function testNestedDiff(): void
    {
        // Тест для случая с null
        $this->assertEquals([], nestedDiff(null));

        // Тест для простого значения
        $simpleValue = 'test';
        $expectedSimple = [
            'type' => 'unchanged',
            'class' => 'item',
            'value' => $simpleValue,
        ];
        $this->assertEquals($expectedSimple, nestedDiff($simpleValue));

        // Тест для массива
        $arrayValue = ['key' => 'value'];
        $expectedArray = [
            'key' => [
                'type' => 'unchanged',
                'class' => 'item',
                'value' => 'value',
            ]
        ];
        $this->assertEquals($expectedArray, nestedDiff($arrayValue));

        // Тест для вложенного массива
        $nestedArrayValue = ['key' => ['nested' => 'value']];
        $expectedNestedArray = [
            'key' => [
                'type' => 'unchanged',
                'class' => 'node',
                'children' => [
                    'nested' => [
                        'type' => 'unchanged',
                        'class' => 'item',
                        'value' => 'value',
                    ]
                ]
            ]
        ];
        $this->assertEquals(
            $expectedNestedArray,
            nestedDiff($nestedArrayValue)
        );
    }

    public function testGenerateDiff(): void
    {
        // Тест для пустых массивов
        $emptyArray1 = [];
        $emptyArray2 = [];
        $this->assertEquals([], generateDiff($emptyArray1, $emptyArray2));

        // Тест для одинаковых массивов
        $array1 = ['key' => 'value'];
        $array2 = ['key' => 'value'];
        $expected = [
            'key' => [
                'type' => 'unchanged',
                'class' => 'item',
                'value' => 'value',
            ]
        ];
        $this->assertEquals($expected, generateDiff($array1, $array2));

        // Тест для добавления ключа
        $array1 = [];
        $array2 = ['key' => 'value'];
        $expected = [
            'key' => [
                'type' => 'added',
                'class' => 'item',
                'value' => 'value',
            ]
        ];
        $this->assertEquals($expected, generateDiff($array1, $array2));

        // Тест для удаления ключа
        $array1 = ['key' => 'value'];
        $array2 = [];
        $expected = [
            'key' => [
                'type' => 'removed',
                'class' => 'item',
                'value' => 'value',
            ]
        ];
        $this->assertEquals($expected, generateDiff($array1, $array2));

        // Тест для изменения значения
        $array1 = ['key' => 'value1'];
        $array2 = ['key' => 'value2'];
        $expected = [
            'key' => [
                'type' => 'changed',
                'class' => 'item',
                'value' => 'value1',
                'newValue' => 'value2'
            ]
        ];
        $this->assertEquals($expected, generateDiff($array1, $array2));

        // Тест для вложенных массивов
        $array1 = ['key' => ['nested' => 'value1']];
        $array2 = ['key' => ['nested' => 'value2']];
        $expected = [
            'key' => [
                'type' => 'unchanged',
                'class' => 'node',
                'children' => [
                    'nested' => [
                        'type' => 'changed',
                        'class' => 'item',
                        'value' => 'value1',
                        'newValue' => 'value2'
                    ]
                ]
            ]
        ];
        $this->assertEquals(
            $expected,
            generateDiff($array1, $array2)
        );
    }

    public function testGenDiff(): void
    {
        // Тест для пустых файлов
        $file1 = $this->fixturesPath . '/empty1.json';
        $file2 = $this->fixturesPath . '/empty2.json';
        $expected = "{\n\n}";
        $this->assertEquals($expected, genDiff($file1, $file2));

        // Тест для простых файлов
        $file1 = $this->fixturesPath . '/file1.json';
        $file2 = $this->fixturesPath . '/file2.json';
        $result = genDiff($file1, $file2);

        $this->assertStringContainsString('host: hexlet.io', $result);
        $this->assertStringContainsString('- timeout: 50', $result);
        $this->assertStringContainsString('+ timeout: 20', $result);
        $this->assertStringContainsString('- proxy: 123.234.53.22', $result);
        $this->assertStringContainsString('- follow: false', $result);
        $this->assertStringContainsString('+ verbose: true', $result);

        // Тест для сложных вложенных файлов
        $file1 = $this->fixturesPath . '/nested1.json';
        $file2 = $this->fixturesPath . '/nested2.json';

        $content = genDiff($file1, $file2);

        // Проверяем наличие ключевых строк
        $this->assertStringContainsString('common: {', $content);
        $this->assertStringContainsString('+ follow: false', $content);
        $this->assertStringContainsString('setting1: Value 1', $content);
        $this->assertStringContainsString('- setting2: 200', $content);
        $this->assertStringContainsString('- setting3: true', $content);
        $this->assertStringContainsString('+ setting3: null', $content);
        $this->assertStringContainsString('+ setting4: blah blah', $content);
        $this->assertStringContainsString('+ setting5: {', $content);
        $this->assertStringContainsString('key5: value5', $content);
        $this->assertStringContainsString('key: value', $content);
        $this->assertStringContainsString('- wow:', $content);
        $this->assertStringContainsString('+ wow: so much', $content);
    }

    public function testComplexGenerateDiff(): void
    {
        // Тест на основе данных из файлов JSON
        $file1 = $this->fixturesPath . '/nested1.json';
        $file2 = $this->fixturesPath . '/nested2.json';

        $fileContents1 = file_get_contents($file1);
        $fileContents2 = file_get_contents($file2);

        $this->assertNotFalse($fileContents1, 'Failed to read file ' . $file1);
        $this->assertNotFalse($fileContents2, 'Failed to read file ' . $file2);

        $json1 = json_decode($fileContents1, true);
        $json2 = json_decode($fileContents2, true);

        $diff = generateDiff($json1, $json2);

        // Проверяем некоторые ключевые элементы diff
        $this->assertArrayHasKey('common', $diff);
        $this->assertEquals('unchanged', $diff['common']['type']);
        $this->assertEquals('node', $diff['common']['class']);

        // Проверяем элементы first level
        $this->assertArrayHasKey('setting1', $diff['common']['children']);
        $this->assertEquals(
            'unchanged',
            $diff['common']['children']['setting1']['type']
        );
        $this->assertEquals(
            'Value 1',
            $diff['common']['children']['setting1']['value']
        );

        $this->assertArrayHasKey('setting2', $diff['common']['children']);
        $this->assertEquals(
            'removed',
            $diff['common']['children']['setting2']['type']
        );
        $this->assertEquals(
            200,
            $diff['common']['children']['setting2']['value']
        );

        $this->assertArrayHasKey('setting3', $diff['common']['children']);
        $this->assertEquals(
            'changed',
            $diff['common']['children']['setting3']['type']
        );
        $this->assertEquals(
            true,
            $diff['common']['children']['setting3']['value']
        );
        $this->assertEquals(
            null,
            $diff['common']['children']['setting3']['newValue']
        );

        // Проверка добавленных элементов
        $this->assertArrayHasKey('follow', $diff['common']['children']);
        $this->assertEquals(
            'added',
            $diff['common']['children']['follow']['type']
        );
        $this->assertEquals(
            false,
            $diff['common']['children']['follow']['value']
        );

        // Проверка для вложенных элементов
        $settingsPath = $diff['common']['children']['setting6']['children'];
        $this->assertArrayHasKey('doge', $settingsPath);
        $this->assertEquals('unchanged', $settingsPath['doge']['type']);
        $this->assertEquals('node', $settingsPath['doge']['class']);

        $dogePath = $settingsPath['doge']['children'];
        $this->assertArrayHasKey('wow', $dogePath);
        $this->assertEquals('changed', $dogePath['wow']['type']);
        $this->assertEquals('', $dogePath['wow']['value']);
        $this->assertEquals('so much', $dogePath['wow']['newValue']);
    }
}
