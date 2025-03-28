<?php

namespace Hexlet\Code\Differ;

use function Hexlet\Code\Differ\Parser\parseFile;
use function Hexlet\Code\Differ\Parser\normalizePath;
use function Hexlet\Code\Differ\Formaters\formatValue;
use function Hexlet\Code\Differ\Formaters\formatDiffLine;

use const Hexlet\Code\Differ\DIFF_FORMAT;

/**
 * Генерирует diff между двумя массивами
 * @param array<string, mixed> $firstArray Первый массив для сравнения
 * @param array<string, mixed> $secondArray Второй массив для сравнения
 * @return string Строка с различиями в формате diff
 */
function generateDiff(array $firstArray, array $secondArray): string
{
    $uniqueKeys = array_unique(array_merge(array_keys($firstArray), array_keys($secondArray)));
    sort($uniqueKeys);
    $diffLines = [];
    foreach ($uniqueKeys as $key) {
        $firstValue = $firstArray[$key] ?? null;
        $secondValue = $secondArray[$key] ?? null;

        if (is_array($firstValue) && is_array($secondValue)) {
            $nestedDiff = generateDiff($firstValue, $secondValue);
            $diffLines[] = formatDiffLine(DIFF_FORMAT['NESTED'], $key, $nestedDiff);
            continue;
        }

        if ($firstValue === $secondValue) {
            $diffLines[] = formatDiffLine(DIFF_FORMAT['UNCHANGED'], $key, $firstValue);
            continue;
        }
        if (array_key_exists($key, $firstArray)) {
            $diffLines[] = formatDiffLine(DIFF_FORMAT['REMOVED'], $key, $firstValue);
        }
        if (array_key_exists($key, $secondArray)) {
            $diffLines[] = formatDiffLine(DIFF_FORMAT['ADDED'], $key, $secondValue);
        }
    }
    return implode("\n", $diffLines);
}

/**
 * Сравнивает два файла и возвращает их различия
 * @param string $firstPath Путь к первому файлу
 * @param string $secondPath Путь ко второму файлу
 * @return string Строка с различиями в формате diff
 */
function genDiff(string $firstPath, string $secondPath): string
{
    $firstFileData = parseFile(normalizePath($firstPath));
    $secondFileData = parseFile(normalizePath($secondPath));
    return generateDiff($firstFileData, $secondFileData);
}
