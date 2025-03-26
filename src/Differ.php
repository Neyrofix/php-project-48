<?php

namespace Hexlet\Code\Differ;

function normalizePath($path)
{
    // Если путь абсолютный, возвращаем его как есть
    if (strpos($path, '/') === 0) {
        return $path;
    }
    // Иначе преобразуем относительный путь в абсолютный
    return realpath(getcwd() . '/' . $path);
}

function readFile(string $filePath)
{
    if (!file_exists($filePath)) {
        throw new \Exception("The file {$filePath} does not exists.");
    }
    $file = file_get_contents($filePath);
    return json_decode($file, true);
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
        $chunks1 = explode(':', $a)[0];
        $chunks2 = explode(':', $b)[0];
        if (substr($chunks1, 2) === substr($chunks2, 2)) {
            if (str_starts_with($chunks1, '-')) {
                return -1;
            } else {
                return 1;
            }
        }
        return $a[2] < $b[2] ? -1 : 1;
    });
    return implode("\n", $result);
}

function genDiff($path1, $path2)
{
    $file1 = readFile(normalizePath($path1));
    $file2 = readFile(normalizePath($path2));
    return differ($file1, $file2);
}
