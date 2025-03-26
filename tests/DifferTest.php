<?php

namespace Hexlet\tests\Differ;

use PHPUnit\Framework\TestCase;

use function Hexlet\Code\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testGenDiff(): void
    {
        $path1 = "tests/fixtures/file1.json";
        $path2 = "tests/fixtures/file2.json";
        $result =
        "- follow: false\n  host: hexlet.io\n- proxy: 123.234.53.22\n- timeout: 50\n+ timeout: 20\n+ verbose: true";
        $this->assertEquals($result, genDiff($path1, $path2));
    }
}
