<?php

namespace Hexlet\Code\Differ\Formaters;

use const Hexlet\Code\Differ\DIFF_FORMAT;

/**
 * Форматирует значение для вывода
 * @param mixed $value Значение для форматирования
 * @return string Отформатированное значение
 */
function formatValue(mixed $value): string
{
    if (is_array($value)) {
        $json = json_encode($value);
        if ($json === false) {
            return 'Invalid array'; // Или выбросьте исключение, если это более уместно
        }
        return $json;
    }
    return is_bool($value) ? var_export($value, true) : (string)$value;
}

/**
 * Форматирует строку diff
 * @param string $prefix Префикс строки (  , - или +)
 * @param string $key Ключ
 * @param mixed $value Значение
 * @return string Отформатированная строка
 */
function formatDiffLine(string $prefix, string $key, mixed $value): string
{
    return "{$prefix}{$key}: " . formatValue($value);
}
