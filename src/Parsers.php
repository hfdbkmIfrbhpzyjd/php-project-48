<?php

declare(strict_types=1);

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse(string $filePath): object
{
    if (!file_exists($filePath)) {
        throw new \Exception("Invalid filepath: {$filePath}");
    }

    $fileContent = file_get_contents($filePath);

    if ($fileContent === false) {
        throw new \Exception("Can't read file: {$filePath}");
    }

    $extension = pathinfo($filePath, PATHINFO_EXTENSION);

    switch ($extension) {
        case "json":
            return json_decode($fileContent, false);
        case "yml":
        case "yaml":
            return Yaml::parse($fileContent, Yaml::PARSE_OBJECT_FOR_MAP);
        default:
            throw new \Exception("Format {$extension} not supported.");
    }
}
