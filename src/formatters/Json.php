<?php

declare(strict_types=1);

namespace Differ\Formatters;

function json(object $AST): string
{
    return json_encode(array_values((array) $AST), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
}
