<?php

namespace Hexlet\Code\Differ;

function readFiles($arg1, $arg2)
{
    $file1 = file_get_contents($arg1);
    $file2 = file_get_contents($arg2);
    $json1 = json_decode($file1, true);
    $json2 = json_decode($file2, true);
    return [$json1, $json2];
}

function differ($arr1, $arr2)
{
    $mainDiff = array_map(function ($key, $value) use ($arr2) {
        switch ($key) {
            case array_key_exists($key, $arr2) && $value === $arr2[$key]:
                $value = is_bool($value) ? var_export($value, true) : $value;
                return "  {$key}: {$value}";
                break;
            case array_key_exists($key, $arr2):
                $value = is_bool($value) ? var_export($value, true) : $value;
                return "- {$key}: {$value}";
            default:
                $value = is_bool($value) ? var_export($value, true) : $value;
                return "- {$key}: {$value}";
        }
    }, array_keys($arr1), $arr1);

    $addedDiff = array_map(function ($key, $value) use ($arr1) {
        if ((array_key_exists($key, $arr1) && $value !== $arr1[$key]) || (!array_key_exists($key, $arr1))) {
            $value = is_bool($value) ? var_export($value, true) : $value;
            return "+ {$key}: {$value}";
        }
    }, array_keys($arr2), $arr2);
    $result = array_filter(array_merge($mainDiff, $addedDiff));

    usort($result, function ($a, $b) {
        if (substr($a, 2, 6) === substr($b, 2, 6)) {
            if (str_starts_with($a, '-')) {
                return -1;
            }
        }
        return $a[2] < $b[2] ? -1 : 1;
    });
    return implode("\n", $result);
}
