<?php

namespace Differ\Differ;

use function Differ\Parsers\parse;
use function Differ\Formatters\format;
use function Functional\sort;

function genDiff(string $filePath1, string $filePath2, string $format = "stylish"): string
{
    $AST = genAST(parse(genAbsolutPath($filePath1)), parse(genAbsolutPath($filePath2)));

    return format($AST, $format);
}

function genAbsolutPath(string $pathToFile): string
{
    $absolutPath = $pathToFile[0] === '/' ? $pathToFile : __DIR__ . "/{$pathToFile}";
    if (file_exists($absolutPath)) {
        return $absolutPath;
    }
    throw new \Exception("The '{$pathToFile}' doesn't exists");
}


function makeNode(string $status, string $name, mixed $oldValue, mixed $newValue): mixed
{
    return (object) ['status' => $status, 'name' => $name, 'oldValue' => $oldValue, 'newValue' => $newValue];
}

function genAST(object $firstFile, object $secondFile): object
{
    $firstFileKeys = array_keys((array) $firstFile);
    $secondFileKeys = array_keys((array) $secondFile);
    $unionKeys = array_merge($firstFileKeys, array_diff($secondFileKeys, $firstFileKeys));
    $sortedKeys = sort($unionKeys, fn($first, $second) => strcmp($first, $second));

    $AST = array_map(function ($name) use ($firstFile, $secondFile) {

        $value1 = $firstFile->$name ?? null;
        $value2 = $secondFile->$name ?? null;

        if (!property_exists($secondFile, (string) $name)) {
            return makeNode('removed', $name, $value1, null);
        }

        if (!property_exists($firstFile, (string) $name)) {
            return makeNode('added', $name, null, $value2);
        }

        if (is_object($value1) && is_object($value2)) {
            return makeNode('children', $name, genAST($value1, $value2), null);
        }

        if ($value1 === $value2) {
            return makeNode('unchanged', $name, $value1, $value2);
        }

        if ($value1 !== $value2) {
            return makeNode('changed', $name, $value1, $value2);
        }

        return null;
    }, $sortedKeys);

    return (object) $AST;
}
