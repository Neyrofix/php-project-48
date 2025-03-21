<?php

namespace Hexlet\Code\Reader;

function readFiles($arg1, $arg2)
{
    //$file1 = file_get_contents(__DIR__ . "/..{$arg1}");
    //$file2 = file_get_contents(__DIR__ . "/..{$arg2}");
    $file1 = file_get_contents($arg1);
    $file2 = file_get_contents($arg2);
    $json1 = json_decode($file1);
    $json2 = json_decode($file2);
    $keys1 = (get_object_vars($json1));
    $keys2 = (get_object_vars($json2));
    var_dump($keys1);
    print("\n");
    var_dump($keys2);
    print("\n");
}
