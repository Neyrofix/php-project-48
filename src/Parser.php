<?php

namespace Hexlet\Code\Differ\Parser;

use Symfony\Component\Yaml\Yaml;

use const Hexlet\Code\Differ\ERROR_MESSAGES;
use const Hexlet\Code\Differ\SUPPORTED_FORMATS;

/**
 * Преобразует относительный путь в абсолютный
 * @param string $path Путь к файлу
 * @return string Абсолютный путь
 * @throws \InvalidArgumentException
 */
function normalizePath(string $path): string
{
    $absolutePath = realpath($path);
    if ($absolutePath === false) {
        throw new \InvalidArgumentException(sprintf(ERROR_MESSAGES['FILE_NOT_FOUND'], $path));
    }
    return $absolutePath;
}

/**
 * Читает и парсит файл в зависимости от его расширения
 * @param string $filePath Путь к файлу
 * @return array Распарсенные данные
 * @throws \Exception
 */
function parseFile(string $filePath): array
{
    if (!is_file($filePath)) {
        throw new \Exception(sprintf(ERROR_MESSAGES['NOT_A_FILE'], $filePath));
    }

    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $content = (string) file_get_contents($filePath);

    if (!in_array($extension, SUPPORTED_FORMATS)) {
        throw new \Exception(sprintf(ERROR_MESSAGES['UNSUPPORTED_FORMAT'], $extension));
    }

    return match ($extension) {
        'json' => parseJson($content, $filePath),
        'yml', 'yaml' => parseYaml($content, $filePath),
        default => throw new \Exception(sprintf(ERROR_MESSAGES['UNSUPPORTED_FORMAT'], $extension))
    };
}

/**
 * Парсит JSON строку
 * @param string $content Содержимое JSON файла
 * @param string $filePath Путь к файлу (для сообщений об ошибках)
 * @return array Распарсенные данные
 * @throws \Exception
 */
function parseJson(string $content, string $filePath): array
{
    $data = json_decode($content, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \Exception(sprintf(ERROR_MESSAGES['INVALID_JSON'], $filePath));
    }
    return $data;
}

/**
 * Парсит YAML строку
 * @param string $content Содержимое YAML файла
 * @param string $filePath Путь к файлу (для сообщений об ошибках)
 * @return array Распарсенные данные
 * @throws \Exception
 */
function parseYaml(string $content, string $filePath): array
{
    try {
        return Yaml::parse($content);
    } catch (\Exception $e) {
        throw new \Exception(sprintf(ERROR_MESSAGES['INVALID_YAML'], $filePath, $e->getMessage()));
    }
}
