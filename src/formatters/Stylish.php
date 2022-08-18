<?php

declare(strict_types=1);

namespace Differ\Formatters;

use function Functional\flatten;

const INDENT_WIDTH = 4;

function makeIndent(int $depth): string
{
    return str_repeat(' ', INDENT_WIDTH * $depth);
}

function parseValue(mixed $value, int $depth): string
{
    if (is_bool($value) || is_null($value)) {
        return json_encode($value, JSON_THROW_ON_ERROR);
    }

    if (is_object($value)) {
        $indent = makeIndent($depth);

        $leafs = array_map(function ($name) use ($value, $depth): string {
            $doubleIndent = makeIndent($depth + 1);
            return "{$doubleIndent}{$name}: " . parseValue($value->$name, $depth + 1);
        }, array_keys((array) $value));

        $branch = implode("\n", flatten($leafs));
        return "{\n{$branch}\n{$indent}}";
    }

    return (string) $value;
}

function stylish(object $AST): string
{
    $iter = function (object $AST, int $depth) use (&$iter): array {
        return array_map(function ($node) use ($iter, $depth): string {
            [
                'status' => $status,
                'name' => $name,
                'oldValue' => $oldValue,
                'newValue' => $newValue
            ] = (array) $node;

            $indent = makeIndent($depth - 1);

            switch ($status) {
                case 'added':
                    return "{$indent}  + {$node->name}: " . parseValue($newValue, $depth);
                case 'removed':
                    return "{$indent}  - {$node->name}: " . parseValue($oldValue, $depth);
                case 'unchanged':
                    return "{$indent}    {$name}: " . parseValue($oldValue, $depth);
                case 'changed':
                    $oldLine = "{$indent}  - {$name}: " . parseValue($oldValue, $depth);
                    $newLine = "{$indent}  + {$name}: " . parseValue($newValue, $depth);
                    return "{$oldLine}\n{$newLine}";
                case 'children':
                    return makeIndent($depth) .
                        "{$name}: {\n" .
                         implode("\n", flatten($iter($node->oldValue, $depth + 1))) .
                         "\n" . makeIndent($depth) . "}";
                default:
                    throw new \Exception("Type {$status} not supported");
            }
        }, (array) $AST);
    };

    return implode("\n", flatten(['{', $iter($AST, 1), '}']));
}
