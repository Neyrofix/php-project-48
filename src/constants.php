<?php

namespace Hexlet\Code\Differ;

const DIFF_FORMAT = [
    'UNCHANGED' => '  ',
    'REMOVED' => '- ',
    'ADDED' => '+ ',
    'NESTED' => '    '
];

const SUPPORTED_FORMATS = [
    'json',
    'yml',
    'yaml'
];

const ERROR_MESSAGES = [
    'FILE_NOT_FOUND' => 'Path %s does not exist.',
    'NOT_A_FILE' => '%s is not a file.',
    'INVALID_JSON' => 'Invalid JSON in %s.',
    'INVALID_YAML' => 'Invalid YAML in %s: %s',
    'UNSUPPORTED_FORMAT' => 'Unsupported file format: %s'
];
