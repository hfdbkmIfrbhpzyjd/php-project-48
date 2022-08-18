<?php

namespace Differ\tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    public function testGendiff()
    {
        $pathToFile1 = "../tests/fixtures/file1.json";
        $pathToFile2 = "../tests/fixtures/file2.json";
        $expected = __DIR__ . "/fixtures/results/res";
        $this->assertEquals(file_get_contents($expected), genDiff($pathToFile1, $pathToFile2));
    }
}
