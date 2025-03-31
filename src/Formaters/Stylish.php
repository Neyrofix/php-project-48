<?php

namespace Hexlet\Code\Differ\Formaters\Stylish;

use const Hexlet\Code\Differ\DIFF_FORMAT;

//Форматирует разницу
function formatDiff(array $diff): string
{
    return "{\n" . formatDiffContent($diff, 1) . "\n}";
}

//Форматирует содержимое разницы
function formatDiffContent(array $diff, int $depth): string
{
    $indent = str_repeat('    ', $depth - 1);
    $result = [];

    foreach ($diff as $key => $node) {
        $result[] = formatNode($key, $node, $depth);
    }

    return implode("\n", $result);
}

//Форматирует узел
function formatNode(string $key, array $node, int $depth): string
{
    $indent = str_repeat('    ', $depth - 1);
    $type = $node['type'];
    $nodeClass = $node['class'];
    $value = $node['value'] ?? null;
    $newValue = $node['newValue'] ?? null;
    $children = $node['children'] ?? null;

    if ($nodeClass === 'node') {
        if ($type === 'unchanged') {
            return "{$indent}    {$key}: {\n" . formatDiffContent($children, $depth + 1) . "\n{$indent}    }";
        } elseif ($type === 'removed') {
            return "{$indent}  - {$key}: {\n" . formatDiffContent($children, $depth + 1) . "\n{$indent}    }";
        } elseif ($type === 'added') {
            return "{$indent}  + {$key}: {\n" . formatDiffContent($children, $depth + 1) . "\n{$indent}    }";
        }
    } else {
        if ($type === 'unchanged') {
            return "{$indent}    {$key}: " . formatValue($value, $depth + 1);
        } elseif ($type === 'removed') {
            return "{$indent}  - {$key}: " . formatValue($value, $depth + 1);
        } elseif ($type === 'added') {
            return "{$indent}  + {$key}: " . formatValue($value, $depth + 1);
        } elseif ($type === 'changed') {
            $removed = "{$indent}  - {$key}: " . formatValue($value, $depth + 1);
            $added = "{$indent}  + {$key}: " . formatValue($newValue, $depth + 1);
            return $removed . "\n" . $added;
        }
    }

    return "";
}

//Форматирует значение
function formatValue(mixed $value, int $depth): string
{
    if (is_array($value)) {
        $indent = str_repeat('    ', $depth - 1);
        $result = "{\n";

        foreach ($value as $key => $subValue) {
            $result .= "{$indent}    {$key}: " . formatValue($subValue, $depth + 1) . "\n";
        }

        $result .= "{$indent}}";
        return $result;
    }

    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    return (string) $value;
}

//Возвращает префикс
function getPrefix(string $type): string
{
    return match ($type) {
        'added' => DIFF_FORMAT['ADDED'],
        'removed' => DIFF_FORMAT['REMOVED'],
        default => DIFF_FORMAT['UNCHANGED']
    };
}
