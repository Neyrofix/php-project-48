<?php

namespace Hexlet\Code\Differ\Formaters\Plain;

//форматирует промежуточный массив в строку в виде списка
function plainDiff(array $diff): string
{
    $result = [];
    foreach ($diff as $key => $node) {
        $formatted = formatPlainNode($node, $key);
        if ($formatted !== '') {
            $result[] = $formatted;
        }
    }
    return implode("\n", $result);
}
//основная функция форматирования
function formatPlainNode(array $node, string $path = ''): string
{
    $type = $node['type'];
    $class = $node['class'];
    $value = $node['value'] ?? null;
    $newValue = $node['newValue'] ?? null;
    $children = $node['children'] ?? null;

    if ($class === 'node') {
        if ($type === 'removed') {
            return "Property '{$path}' was removed";
        }
        if ($type === 'added') {
            return "Property '{$path}' was added with value: [complex value]";
        }
        $result = [];
        if ($children !== null) {
            foreach ($children as $key => $child) {
                $newPath = $path ? "{$path}.{$key}" : $key;
                $formatted = formatPlainNode($child, $newPath);
                if ($formatted !== '') {
                    $result[] = $formatted;
                }
            }
        }
        return implode("\n", $result);
    }

    switch ($type) {
        case 'added':
            return "Property '{$path}' was added with value: " . formatPlainValue($value);
        case 'removed':
            return "Property '{$path}' was removed";
        case 'changed':
            $oldValue = formatPlainValue($value);
            $newValue = formatPlainValue($newValue);
            return "Property '{$path}' was updated. From {$oldValue} to {$newValue}";
        case 'unchanged':
            return '';
        default:
            return '';
    }
}
//проверяет тип значения и возвращает строку
function formatPlainValue(mixed $value): string
{
    if (is_array($value)) {
        return '[complex value]';
    }

    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (is_string($value)) {
        return "'{$value}'";
    }

    return (string) $value;
}
