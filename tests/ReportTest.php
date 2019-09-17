<?php
namespace Differ\tests;

use \PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use function Differ\Parsers\parseFile;
use function Differ\differ\genDiff;

class ReportTest extends TestCase
{
    public function testGetReport()
    {
        //JSON
        $testFilePath1 = __DIR__ . '/fixtures/testFile1.json';
        $testFilePath2 = __DIR__ . '/fixtures/testFile2.json';
        $expectedFilePath = __DIR__ . '/fixtures/expectedPretty.txt';
        $expectedValue = parseFile($expectedFilePath);
        $diff = genDiff($testFilePath1, $testFilePath2);
        $this->assertEquals($expectedValue, $diff);
        //YAML
        $testFilePath1 = __DIR__ . '/fixtures/testFile1.yml';
        $testFilePath2 = __DIR__ . '/fixtures/testFile2.yml';
        $expectedValue = parseFile($expectedFilePath);
        $diff = genDiff($testFilePath1, $testFilePath2);
        $this->assertEquals($expectedValue, $diff);
        //AST
        $testFilePath1 = __DIR__ . '/fixtures/astFile1.json';
        $testFilePath2 = __DIR__ . '/fixtures/astFile2.json';
        $expectedFilePath = __DIR__ . '/fixtures/expectedAST.txt';
        $expectedValue = parseFile($expectedFilePath);
        $diff = genDiff($testFilePath1, $testFilePath2);
        $this->assertEquals($expectedValue, $diff);
        //plain format
        $expectedFilePath = __DIR__ . '/fixtures/expectedPlain.txt';
        $expectedValue = parseFile($expectedFilePath);
        $diff = genDiff($testFilePath1, $testFilePath2, 'plain');
        $this->assertEquals($expectedValue, $diff);
        //json format
        $expectedFilePath = __DIR__ . '/fixtures/expectedJson.json';
        $this->assertEquals($expectedValue, $diff);
    }
}
