<?php

namespace Hexlet\Tests;

use PHPUnit\Framework\TestCase;

use function Hexlet\Code\Differ\Formaters\Stylish\stylishDiff;
use function Hexlet\Code\Differ\Formaters\Stylish\formatDiffContent;
use function Hexlet\Code\Differ\Formaters\Stylish\formatNode;
use function Hexlet\Code\Differ\Formaters\Stylish\formatValue;
use function Hexlet\Code\Differ\Formaters\Plain\plainDiff;
use function Hexlet\Code\Differ\Formaters\Plain\formatPlainNode;
use function Hexlet\Code\Differ\Formaters\Plain\formatPlainValue;
use function Hexlet\Code\Differ\Formaters\Json\jsonDiff;

class FormatersTest extends TestCase
{
    // Тесты для Stylish форматтера
    public function testStylishFormatValue(): void
    {
        // Тест для строки
        $this->assertEquals('test', formatValue('test', 1));

        // Тест для числа
        $this->assertEquals('123', formatValue(123, 1));

        // Тест для булевых значений
        $this->assertEquals('true', formatValue(true, 1));
        $this->assertEquals('false', formatValue(false, 1));

        // Тест для null
        $this->assertEquals('null', formatValue(null, 1));

        // Тест для массива
        $array = ['key' => 'value'];
        $expected = "{\n    key: value\n}";
        $this->assertEquals($expected, formatValue($array, 1));

        // Тест для вложенного массива
        $nestedArray = ['key' => ['nested' => 'value']];
        $expectedNested = "{\n    key: {\n        nested: value\n    }\n}";
        $this->assertEquals($expectedNested, formatValue($nestedArray, 1));
    }

    public function testStylishFormatNode(): void
    {
        // Тест для unchanged item
        $node = [
            'type' => 'unchanged',
            'class' => 'item',
            'value' => 'value'
        ];
        $expected = "    key: value";
        $this->assertEquals($expected, formatNode('key', $node, 1));

        // Тест для added item
        $node = [
            'type' => 'added',
            'class' => 'item',
            'value' => 'value'
        ];
        $expected = "  + key: value";
        $this->assertEquals($expected, formatNode('key', $node, 1));

        // Тест для removed item
        $node = [
            'type' => 'removed',
            'class' => 'item',
            'value' => 'value'
        ];
        $expected = "  - key: value";
        $this->assertEquals($expected, formatNode('key', $node, 1));

        // Тест для changed item
        $node = [
            'type' => 'changed',
            'class' => 'item',
            'value' => 'old',
            'newValue' => 'new'
        ];
        $expected = "  - key: old\n  + key: new";
        $this->assertEquals($expected, formatNode('key', $node, 1));

        // Тест для unchanged node
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
        $this->assertEquals($expected, formatNode('key', $node, 1));

        // Тест для added node
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
        $this->assertEquals($expected, formatNode('key', $node, 1));

        // Тест для removed node
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
        $this->assertEquals($expected, formatNode('key', $node, 1));
    }

    public function testStylishFormatDiffContent(): void
    {
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
        $this->assertEquals($expected, formatDiffContent($diff, 1));
    }

    public function testStylishDiff(): void
    {
        // Тест для простого diff
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
        $this->assertEquals($expected, stylishDiff($diff));

        // Тест для пустого diff
        $emptyDiff = [];
        $expectedEmpty = "{\n\n}";
        $this->assertEquals($expectedEmpty, stylishDiff($emptyDiff));
    }

    // Тесты для Plain форматтера
    public function testPlainFormatValue(): void
    {
        // Тест для строки
        $this->assertEquals("'test'", formatPlainValue('test'));

        // Тест для числа
        $this->assertEquals('123', formatPlainValue(123));

        // Тест для булевых значений
        $this->assertEquals('true', formatPlainValue(true));
        $this->assertEquals('false', formatPlainValue(false));

        // Тест для null
        $this->assertEquals('null', formatPlainValue(null));

        // Тест для массива
        $array = ['key' => 'value'];
        $this->assertEquals('[complex value]', formatPlainValue($array));

        // Тест для вложенного массива
        $nestedArray = ['key' => ['nested' => 'value']];
        $this->assertEquals('[complex value]', formatPlainValue($nestedArray));
    }

    public function testPlainFormatNode(): void
    {
        // Тест для added item
        $node = [
            'type' => 'added',
            'class' => 'item',
            'value' => 'value'
        ];
        $expected = "Property 'key' was added with value: 'value'";
        $this->assertEquals($expected, formatPlainNode($node, 'key'));

        // Тест для removed item
        $node = [
            'type' => 'removed',
            'class' => 'item',
            'value' => 'value'
        ];
        $expected = "Property 'key' was removed";
        $this->assertEquals($expected, formatPlainNode($node, 'key'));

        // Тест для changed item
        $node = [
            'type' => 'changed',
            'class' => 'item',
            'value' => 'old',
            'newValue' => 'new'
        ];
        $expected = "Property 'key' was updated. From 'old' to 'new'";
        $this->assertEquals($expected, formatPlainNode($node, 'key'));

        // Тест для unchanged item
        $node = [
            'type' => 'unchanged',
            'class' => 'item',
            'value' => 'value'
        ];
        $this->assertEquals('', formatPlainNode($node, 'key'));

        // Тест для added node
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
        $expected = "Property 'key' was added with value: [complex value]";
        $this->assertEquals($expected, formatPlainNode($node, 'key'));

        // Тест для removed node
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
        $expected = "Property 'key' was removed";
        $this->assertEquals($expected, formatPlainNode($node, 'key'));

        // Тест для unchanged node с вложенными изменениями
        $node = [
            'type' => 'unchanged',
            'class' => 'node',
            'children' => [
                'nested' => [
                    'type' => 'added',
                    'class' => 'item',
                    'value' => 'value'
                ]
            ]
        ];
        $expected = "Property 'key.nested' was added with value: 'value'";
        $this->assertEquals($expected, formatPlainNode($node, 'key'));
    }

    public function testPlainDiff(): void
    {
        $diff = [
            'common' => [
                'type' => 'unchanged',
                'class' => 'node',
                'children' => [
                    'follow' => [
                        'type' => 'added',
                        'class' => 'item',
                        'value' => false
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
                    ],
                    'setting4' => [
                        'type' => 'added',
                        'class' => 'item',
                        'value' => 'blah blah'
                    ],
                    'setting5' => [
                        'type' => 'added',
                        'class' => 'node',
                        'children' => [
                            'key5' => [
                                'type' => 'unchanged',
                                'class' => 'item',
                                'value' => 'value5'
                            ]
                        ]
                    ],
                    'setting6' => [
                        'type' => 'unchanged',
                        'class' => 'node',
                        'children' => [
                            'doge' => [
                                'type' => 'unchanged',
                                'class' => 'node',
                                'children' => [
                                    'wow' => [
                                        'type' => 'changed',
                                        'class' => 'item',
                                        'value' => '',
                                        'newValue' => 'so much'
                                    ]
                                ]
                            ],
                            'ops' => [
                                'type' => 'added',
                                'class' => 'item',
                                'value' => 'vops'
                            ]
                        ]
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
                    'nest' => [
                        'type' => 'changed',
                        'class' => 'node',
                        'value' => ['key' => 'value'],
                        'newValue' => 'str'
                    ]
                ]
            ],
            'group2' => [
                'type' => 'removed',
                'class' => 'node',
                'children' => [
                    'abc' => [
                        'type' => 'unchanged',
                        'class' => 'item',
                        'value' => 12345
                    ]
                ]
            ],
            'group3' => [
                'type' => 'added',
                'class' => 'node',
                'children' => [
                    'deep' => [
                        'type' => 'unchanged',
                        'class' => 'node',
                        'children' => [
                            'id' => [
                                'type' => 'unchanged',
                                'class' => 'node',
                                'children' => [
                                    'number' => [
                                        'type' => 'unchanged',
                                        'class' => 'item',
                                        'value' => 45
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'fee' => [
                        'type' => 'unchanged',
                        'class' => 'item',
                        'value' => 100500
                    ]
                ]
            ]
        ];

        $expected = "Property 'common.follow' was added with value: false\n"
            . "Property 'common.setting2' was removed\n"
            . "Property 'common.setting3' was updated. From true to null\n"
            . "Property 'common.setting4' was added with value: 'blah blah'\n"
            . "Property 'common.setting5' was added with value: [complex value]\n"
            . "Property 'common.setting6.doge.wow' was updated. From '' to 'so much'\n"
            . "Property 'common.setting6.ops' was added with value: 'vops'\n"
            . "Property 'group1.baz' was updated. From 'bas' to 'bars'\n"
            . "Property 'group2' was removed\n"
            . "Property 'group3' was added with value: [complex value]";

        $this->assertEquals($expected, plainDiff($diff));
    }

    // Тесты для Json форматтера
    public function testJsonDiffEmpty(): void
    {
        $diff = [];
        $expected = '{}';
        $this->assertEquals($expected, jsonDiff($diff));
    }

    public function testJsonDiffSimple(): void
    {
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
        $expected = '{
    "key1": "value1",
    "+key2": "value2"
}';
        $this->assertEquals($expected, jsonDiff($diff));
    }

    public function testJsonDiffComplex(): void
    {
        $diff = [
            'common' => [
                'type' => 'unchanged',
                'class' => 'node',
                'children' => [
                    'follow' => [
                        'type' => 'added',
                        'class' => 'item',
                        'value' => false
                    ],
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
                    ],
                    'setting4' => [
                        'type' => 'added',
                        'class' => 'item',
                        'value' => 'blah blah'
                    ]
                ]
            ]
        ];
        $expected = '{
    "common": {
        "+follow": false,
        "setting1": "Value 1",
        "-setting2": 200,
        "-setting3": true,
        "+setting3": null,
        "+setting4": "blah blah"
    }
}';
        $this->assertEquals($expected, jsonDiff($diff));
    }

    public function testJsonDiffNested(): void
    {
        $diff = [
            'group1' => [
                'type' => 'unchanged',
                'class' => 'node',
                'children' => [
                    'nested' => [
                        'type' => 'unchanged',
                        'class' => 'node',
                        'children' => [
                            'key' => [
                                'type' => 'changed',
                                'class' => 'item',
                                'value' => 'old',
                                'newValue' => 'new'
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $expected = '{
    "group1": {
        "nested": {
            "-key": "old",
            "+key": "new"
        }
    }
}';
        $this->assertEquals($expected, jsonDiff($diff));
    }
}
