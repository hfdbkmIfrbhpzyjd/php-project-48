<?php

namespace Differ\Differ;

use function Functional\sort;

function genDiff($firstFilePath, $secondFilePath, $format = 'stylish')
{
    $firstFile = json_decode(file_get_contents(genAbsolutPath($firstFilePath)), true);
    $secondFile = json_decode(file_get_contents(genAbsolutPath($secondFilePath)), true);

    $firstFileKeys = array_keys($firstFile);
    $secondFileKeys = array_keys($secondFile);
    $unionKeys = array_merge($firstFileKeys, array_diff($secondFileKeys, $firstFileKeys));

    $sortedKeys = sort($unionKeys, fn($first, $second) => strcmp($first, $second));

    $res = array_map(function ($key) use ($firstFile, $secondFile) {

        $value1 = $firstFile[$key] ?? null;
        $value2 = $secondFile[$key] ?? null;

        if (is_bool($value1)) {
            $value1 = $value1 ? 'true' : 'false';
        }

        if (is_bool($value2)) {
            $value2 = $value2 ? 'true' : 'false';
        }

        if (is_null($value1)) {
            return "    + {$key}: {$value2}";
        }

        if (is_null($value2)) {
            return "    - {$key}: {$value1}";
        }

        if ($value1 === $value2) {
            return "      {$key}: {$value1}";
        }

        if ($value1 !== $value2) {
            return "    - {$key}: {$value1}" . PHP_EOL . "    + {$key}: {$value2}";
        }

        return null;
    }, $sortedKeys);

    return "{" . PHP_EOL . implode("\n", $res) . PHP_EOL . "}";
}

function genAbsolutPath(string $pathToFile): string
{
    $absolutPath = $pathToFile[0] === '/' ? $pathToFile : __DIR__ . "/{$pathToFile}";
    if (file_exists($absolutPath)) {
        return $absolutPath;
    }
    throw new \Exception("The '{$pathToFile}' doesn't exists");
}
