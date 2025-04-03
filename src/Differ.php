<?php

namespace Hexlet\Code\Differ;

use function Hexlet\Code\Differ\Parser\parseFile;
use function Hexlet\Code\Differ\Parser\normalizePath;
use function Hexlet\Code\Differ\Formaters\Stylish\stylishDiff;
use function Hexlet\Code\Differ\Formaters\Plain\plainDiff;
use function Hexlet\Code\Differ\Formaters\Json\jsonDiff;

//Генерирует промежуточный массив для последующего форматирования
function generateDiff(array $firstArray, array $secondArray): array
{
    $uniqueKeys = array_unique(array_merge(array_keys($firstArray), array_keys($secondArray)));
    sort($uniqueKeys);
    $diff = [];

    foreach ($uniqueKeys as $key) {
        if (array_key_exists($key, $firstArray) && array_key_exists($key, $secondArray)) {
            if (is_array($firstArray[$key]) && is_array($secondArray[$key])) {
                $diff[$key] = makeNestedDiff(generateDiff($firstArray[$key], $secondArray[$key]));
            } else {
                if ($firstArray[$key] === $secondArray[$key]) {
                    $diff[$key] = makeUnchangedDiff($firstArray[$key]);
                } else {
                    $diff[$key] = makeChangedDiff($firstArray[$key], $secondArray[$key]);
                }
            }
        } elseif (array_key_exists($key, $firstArray)) {
            if (is_array($firstArray[$key])) {
                $arr = $firstArray[$key];
                $diff[$key] = ['type' => 'removed', 'class' => 'node', 'children' => nestedDiff($arr)];
            } else {
                $diff[$key] = makeRemovedDiff($firstArray[$key]);
            }
        } else {
            if (is_array($secondArray[$key])) {
                $arr = $secondArray[$key];
                $diff[$key] = ['type' => 'added', 'class' => 'node', 'children' => nestedDiff($arr)];
            } else {
                $diff[$key] = makeAddedDiff($secondArray[$key]);
            }
        }
    }
    return $diff;
}

function nestedDiff(mixed $value): array
{
    if ($value === null) {
        return [];
    }
    if (!is_array($value)) {
        return makeUnchangedDiff($value);
    }

    $children = array_map(function ($item) {
        if (is_array($item)) {
            return ['type' => 'unchanged', 'class' => 'node', 'children' => nestedDiff($item)];
        }
        return makeUnchangedDiff($item);
    }, $value);

    return $children;
}

function makeAddedDiff(mixed $value): array
{
    return [
        'type' => 'added',
        'class' => 'item',
        'value' => $value,
    ];
}

function makeRemovedDiff(mixed $value): array
{
    return [
        'type' => 'removed',
        'class' => 'item',
        'value' => $value,
    ];
}

function makeUnchangedDiff(mixed $value): array
{
    return [
        'type' => 'unchanged',
        'class' => 'item',
        'value' => $value,
    ];
}

function makeChangedDiff(mixed $value1, mixed $value2): array
{
    return [
        'type' => 'changed',
        'class' => 'item',
        'value' => $value1,
        'newValue' => $value2
    ];
}

function makeNestedDiff(array $value): array
{
    return [
        'type' => 'unchanged',
        'class' => 'node',
        'children' => $value
    ];
}

//Сравнивает два файла и возвращает разницу в зависимости от формата
function genDiff(string $firstPath, string $secondPath, string $format = 'stylish'): string
{
    $firstFileData = parseFile(normalizePath($firstPath));
    $secondFileData = parseFile(normalizePath($secondPath));
    $diff = generateDiff($firstFileData, $secondFileData);
    if ($format === 'stylish') {
        return stylishDiff($diff);
    }
    if ($format === 'plain') {
        return plainDiff($diff);
    }
    if ($format === 'json') {
        return jsonDiff($diff);
    }
    return stylishDiff($diff);
}
