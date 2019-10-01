<?php
namespace Differ\tests;

use \PHPUnit\Framework\TestCase;
use function Differ\differ\genDiff;

class DifferTest extends TestCase
{
    /**
    * @dataProvider additionProvider
    */
    public function testGenDiff($expectedFileName, $testFileName1, $testFileName2, $format = 'pretty')
    {
        $expectedFilePath = $this->getFilePath($expectedFileName);
        $testFilePath1 = $this->getFilePath($testFileName1);
        $testFilePath2 = $this->getFilePath($testFileName2);
        $diff = genDiff($testFilePath1, $testFilePath2, $format);
        $expectedResult = file_get_contents($expectedFilePath);
        if ($format == 'json') {
            $this->assertJsonStringEqualsJsonString($expectedResult, $diff);
        } else {
            $this->assertEquals($expectedResult, $diff);
        }
    }

    public function getFilePath($fileName)
    {
        return __DIR__ . "/fixtures/$fileName";
    }
    
    public function additionProvider()
    {
        return [
            ['expectedPretty.txt', 'testFile1.json', 'testFile2.json'],
            ['expectedPretty.txt', 'testFile1.yml', 'testFile2.yml'],
            ['expectedPrettyForAST.txt', 'testFileForAST1.json', 'testFileForAST2.json'],
            ['expectedPlain.txt', 'testFileForAST1.json', 'testFileForAST2.json', 'plain'],
            ['expectedJson.json', 'testFileForAST1.json', 'testFileForAST2.json', 'json']
        ];
    }
}
