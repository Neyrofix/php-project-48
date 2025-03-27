<?php

namespace Hexlet\Code\Differ;

use function Hexlet\Code\Differ\Parser\parseFile;
use function Hexlet\Code\Differ\Parser\normalizePath;

/**
 * Форматирует значение для вывода
 * @param mixed $value Значение для форматирования
 * @return string Отформатированное значение
 */
function formatValue(mixed $value): string
{
    return is_bool($value) ? var_export($value, true) : (string)$value;
}

/**
 * Генерирует diff между двумя массивами
 * @param array $firstArray Первый массив для сравнения
 * @param array $secondArray Второй массив для сравнения
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

        if ($firstValue === $secondValue) {
            $diffLines[] = "  {$key}: " . formatValue($firstValue);
            continue;
        }

        if (array_key_exists($key, $firstArray)) {
            $diffLines[] = "- {$key}: " . formatValue($firstValue);
        }
        if (array_key_exists($key, $secondArray)) {
            $diffLines[] = "+ {$key}: " . formatValue($secondValue);
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
