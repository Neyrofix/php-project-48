<?php

namespace Hexlet\Code\Differ\Formaters\Json;

// Форматирует промежуточный массив в JSON строку
function jsonDiff(array $diff): string
{
    $result = convertDiffToJson($diff);
    $json = json_encode($result, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT);

    if ($json === false) {
        throw new \JsonException('Failed to encode JSON: ' . json_last_error_msg());
    }

    return $json;
}

// Преобразует промежуточный массив в структуру для JSON
function convertDiffToJson(array $diff): array
{
    $result = [];

    foreach ($diff as $key => $node) {
        $type = $node['type'];
        $class = $node['class'];

        if ($class === 'node') {
            // Обработка вложенных объектов
            if ($type === 'unchanged') {
                $result[$key] = convertDiffToJson($node['children']);
            } elseif ($type === 'removed') {
                $result["-{$key}"] = convertDiffToJson($node['children']);
            } elseif ($type === 'added') {
                $result["+{$key}"] = convertDiffToJson($node['children']);
            }
        } else {
            // Обработка значений
            if ($type === 'unchanged') {
                $result[$key] = $node['value'];
            } elseif ($type === 'removed') {
                $result["-{$key}"] = $node['value'];
            } elseif ($type === 'added') {
                $result["+{$key}"] = $node['value'];
            } elseif ($type === 'changed') {
                $result["-{$key}"] = $node['value'];
                $result["+{$key}"] = $node['newValue'];
            }
        }
    }

    return $result;
}
