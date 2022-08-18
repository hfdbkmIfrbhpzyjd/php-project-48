<?php

declare(strict_types=1);

namespace Differ\Formatters;

use function Functional\flatten;

function toString(mixed $value): string
{
    if (is_bool($value) || is_null($value)) {
        return json_encode($value, JSON_THROW_ON_ERROR);
    }

    if (is_object($value)) {
        return "[complex value]";
    }

    if (is_string($value)) {
        return "'{$value}'";
    }

    return (string) $value;
}

function plain(object $AST): string
{
    $iter = function (object $AST, string $path) use (&$iter): array {

        return array_map(function ($node) use ($iter, $path): array|string {
            [
                'status' => $status,
                'name' => $name,
                'oldValue' => $oldValue,
                'newValue' => $newValue
            ] = (array) $node;

            $newPath = (strlen($path) > 1) ? implode('.', [$path, $name]) : $name;

            switch ($status) {
                case 'added':
                    return "Property '{$newPath}' was added with value: " . toString($newValue);
                case 'removed':
                    return "Property '{$newPath}' was removed";
                case 'unchanged':
                    return [];
                case 'changed':
                    return "Property '{$newPath}' was updated. From " .
                    toString($oldValue) . " to " . toString($newValue);
                case 'children':
                    return $iter($oldValue, $newPath);
                default:
                    throw new \Exception("Unbelivibale");
            }
        }, (array) $AST);
    };

    return implode("\n", array_filter(flatten([$iter($AST, '')])));
}
