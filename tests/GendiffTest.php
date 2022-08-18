<?php

namespace Differ\tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

const PATH_TO_FIRTST = '../tests/fixtures/file1.';
const PATH_TO_SECOND = '../tests/fixtures/file2.';
const PATH_TO_RESULT = __DIR__ . '/fixtures/results/res';

class GendiffTest extends TestCase
{
    /**
     * @dataProvider additionProvider
    */

    public function testGendiff($extension)
    {
        $pathToFile1 = PATH_TO_FIRTST . $extension;
        $pathToFile2 = PATH_TO_SECOND . $extension;
        $expected = PATH_TO_RESULT;
        $this->assertEquals(file_get_contents($expected), genDiff($pathToFile1, $pathToFile2));
    }

    public function additionProvider()
    {
        return [
            ['json'],
            ['yml']
        ];
    }
}
