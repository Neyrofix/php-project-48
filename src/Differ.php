<?php

namespace Hexlet\Code\Differ;

function normalizePath(string $path): string
{
    $absolutePath = realpath($path);
    if ($absolutePath === false) {
        throw new \InvalidArgumentException("Path {$path} does not exist.");
    }
    return $absolutePath;
}

function readFile(string $filePath): array
{
    if (!is_file($filePath)) {
        throw new \Exception("{$filePath} is not a file.");
    }
    $content = (string) file_get_contents($filePath);
    $data = json_decode($content, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \Exception("Invalid JSON in {$filePath}.");
    }
    return $data;
}

function formatValue(mixed $value): string
{
    return is_bool($value) ? var_export($value, true) : (string)$value;
}

function generateDiff(array $arr1, array $arr2): string
{
    $allKeys = array_unique(array_merge(array_keys($arr1), array_keys($arr2)));
    sort($allKeys);

    $result = [];
    foreach ($allKeys as $key) {
        $value1 = $arr1[$key] ?? null;
        $value2 = $arr2[$key] ?? null;

        if ($value1 === $value2) {
            $result[] = "  {$key}: " . formatValue($value1);
            continue;
        }
        if (array_key_exists($key, $arr1)) {
            $result[] = "- {$key}: " . formatValue($value1);
        }
        if (array_key_exists($key, $arr2)) {
            $result[] = "+ {$key}: " . formatValue($value2);
        }
    }

    return implode("\n", $result);
}

function genDiff(string $path1, string $path2): string
{
    $file1 = readFile(normalizePath($path1));
    $file2 = readFile(normalizePath($path2));
    return generateDiff($file1, $file2);
}
