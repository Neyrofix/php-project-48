<?php

namespace Hexlet\Tests;

use PHPUnit\Framework\TestCase;

use function Hexlet\Code\Differ\Formaters\Stylish\formatDiff;
use function Hexlet\Code\Differ\Formaters\Stylish\formatDiffContent;
use function Hexlet\Code\Differ\Formaters\Stylish\formatNode;
use function Hexlet\Code\Differ\Formaters\Stylish\formatValue;
use function Hexlet\Code\Differ\Formaters\Stylish\getPrefix;

class FormatersTest extends TestCase
{
    public function testFormatValue(): void
    {
        // Тест для форматирования строки
        $this->assertEquals('test', formatValue('test', 1));

        // Тест для форматирования числа
        $this->assertEquals('123', formatValue(123, 1));

        // Тест для форматирования булевых значений
        $this->assertEquals('true', formatValue(true, 1));
        $this->assertEquals('false', formatValue(false, 1));

        // Тест для форматирования null
        $this->assertEquals('null', formatValue(null, 1));

        // Тест для форматирования массива
        $array = ['key' => 'value'];
        $expected = "{\n    key: value\n}";
        $this->assertEquals($expected, formatValue($array, 1));

        // Тест для форматирования вложенного массива
        $nestedArray = ['key' => ['nested' => 'value']];
        $expectedNested = "{\n    key: {\n        nested: value\n    }\n}";
        $this->assertEquals(
            $expectedNested,
            formatValue($nestedArray, 1)
        );
    }

    public function testFormatNode(): void
    {
        // Тест для форматирования узла с типом unchanged и классом item
        $node = [
            'type' => 'unchanged',
            'class' => 'item',
            'value' => 'value'
        ];
        $expected = "    key: value";
        $this->assertEquals($expected, formatNode('key', $node, 1));

        // Тест для форматирования узла с типом added и классом item
        $node = [
            'type' => 'added',
            'class' => 'item',
            'value' => 'value'
        ];
        $expected = "  + key: value";
        $this->assertEquals($expected, formatNode('key', $node, 1));

        // Тест для форматирования узла с типом removed и классом item
        $node = [
            'type' => 'removed',
            'class' => 'item',
            'value' => 'value'
        ];
        $expected = "  - key: value";
        $this->assertEquals($expected, formatNode('key', $node, 1));

        // Тест для форматирования узла с типом changed и классом item
        $node = [
            'type' => 'changed',
            'class' => 'item',
            'value' => 'old',
            'newValue' => 'new'
        ];
        $expected = "  - key: old\n  + key: new";
        $this->assertEquals($expected, formatNode('key', $node, 1));

        // Тест для форматирования узла с типом unchanged и классом node
        $node = [
            'type' => 'unchanged',
            'class' => 'node',
            'children' => [
                'nested' => [
                    'type' => 'unchanged',
                    'class' => 'item',
                    'value' => 'value'
                ]
            ]
        ];
        $expected = "    key: {\n        nested: value\n    }";
        $this->assertEquals(
            $expected,
            formatNode('key', $node, 1)
        );

        // Тест для форматирования узла с типом added и классом node
        $node = [
            'type' => 'added',
            'class' => 'node',
            'children' => [
                'nested' => [
                    'type' => 'unchanged',
                    'class' => 'item',
                    'value' => 'value'
                ]
            ]
        ];
        $expected = "  + key: {\n        nested: value\n    }";
        $this->assertEquals(
            $expected,
            formatNode('key', $node, 1)
        );

        // Тест для форматирования узла с типом removed и классом node
        $node = [
            'type' => 'removed',
            'class' => 'node',
            'children' => [
                'nested' => [
                    'type' => 'unchanged',
                    'class' => 'item',
                    'value' => 'value'
                ]
            ]
        ];
        $expected = "  - key: {\n        nested: value\n    }";
        $this->assertEquals(
            $expected,
            formatNode('key', $node, 1)
        );
    }

    public function testFormatDiffContent(): void
    {
        // Тест для форматирования содержимого diff
        $diff = [
            'key1' => [
                'type' => 'unchanged',
                'class' => 'item',
                'value' => 'value1'
            ],
            'key2' => [
                'type' => 'added',
                'class' => 'item',
                'value' => 'value2'
            ]
        ];
        $expected = "    key1: value1\n  + key2: value2";
        $this->assertEquals(
            $expected,
            formatDiffContent($diff, 1)
        );
    }

    public function testFormatDiff(): void
    {
        // Тест для форматирования diff
        $diff = [
            'key1' => [
                'type' => 'unchanged',
                'class' => 'item',
                'value' => 'value1'
            ],
            'key2' => [
                'type' => 'added',
                'class' => 'item',
                'value' => 'value2'
            ]
        ];
        $expected = "{\n    key1: value1\n  + key2: value2\n}";
        $this->assertEquals($expected, formatDiff($diff));

        // Тест для пустого diff
        $emptyDiff = [];
        $expectedEmpty = "{\n\n}";
        $this->assertEquals($expectedEmpty, formatDiff($emptyDiff));
    }

    public function testComplexDiff(): void
    {
        $diff = [
            'common' => [
                'type' => 'unchanged',
                'class' => 'node',
                'children' => [
                    'setting1' => [
                        'type' => 'unchanged',
                        'class' => 'item',
                        'value' => 'Value 1'
                    ],
                    'setting2' => [
                        'type' => 'removed',
                        'class' => 'item',
                        'value' => 200
                    ],
                    'setting3' => [
                        'type' => 'changed',
                        'class' => 'item',
                        'value' => true,
                        'newValue' => null
                    ]
                ]
            ],
            'group1' => [
                'type' => 'unchanged',
                'class' => 'node',
                'children' => [
                    'baz' => [
                        'type' => 'changed',
                        'class' => 'item',
                        'value' => 'bas',
                        'newValue' => 'bars'
                    ],
                    'foo' => [
                        'type' => 'unchanged',
                        'class' => 'item',
                        'value' => 'bar'
                    ]
                ]
            ]
        ];

        $expected = "{\n"
            . "    common: {\n"
            . "        setting1: Value 1\n"
            . "      - setting2: 200\n"
            . "      - setting3: true\n"
            . "      + setting3: null\n"
            . "    }\n"
            . "    group1: {\n"
            . "      - baz: bas\n"
            . "      + baz: bars\n"
            . "        foo: bar\n"
            . "    }\n"
            . "}";
        $this->assertEquals($expected, formatDiff($diff));
    }
}
