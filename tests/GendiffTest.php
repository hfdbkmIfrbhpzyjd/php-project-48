<?php

namespace Differ\tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

const PATH_TO_BEFORE = '../tests/fixtures/before.';
const PATH_TO_AFTER = '../tests/fixtures/after.';
const PATH_TO_RESULT = __DIR__ . '/fixtures/results/';

class GenDiffTest extends TestCase
{
    /**
     * @dataProvider additionProvider
    */

    public function testGendiff($extension, $format)
    {
        $pathToFile1 = PATH_TO_BEFORE . $extension;
        $pathToFile2 = PATH_TO_AFTER . $extension;
        $expected = PATH_TO_RESULT . $format;
        $this->assertEquals(file_get_contents($expected), genDiff($pathToFile1, $pathToFile2, $format));
    }

    public function additionProvider()
    {
        return [
            ['json', 'stylish'],
            ['json', 'plain']
        ];
    }
}
