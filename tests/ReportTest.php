<?php
namespace Differ\tests;

use \PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use Differ\Differ;
use function Differ\Parsers\parseFile;

class ReportTest extends TestCase
{
    public function testGetReport()
    {
        $differ = new Differ();
        //JSON
        $testFilePath1 = __DIR__ . '/fixtures/testFile1.json';
        $testFilePath2 = __DIR__ . '/fixtures/testFile2.json';
        $expectedFilePath = __DIR__ . '/fixtures/expected.txt';
        $expectedValue = parseFile($expectedFilePath);
        $diff = $differ->genDiff($testFilePath1, $testFilePath2)->report;
        $this->assertEquals($expectedValue, $diff);
        //YAML
        $testFilePath1 = __DIR__ . '/fixtures/testFile1.yml';
        $testFilePath2 = __DIR__ . '/fixtures/testFile2.yml';
        $expectedValue = parseFile($expectedFilePath);
        $diff = $differ->genDiff($testFilePath1, $testFilePath2)->report;
        $this->assertEquals($expectedValue, $diff);
        //AST
        $testFilePath1 = __DIR__ . '/fixtures/astFile1.json';
        $testFilePath2 = __DIR__ . '/fixtures/astFile2.json';
        $expectedFilePath = __DIR__ . '/fixtures/expectedAST.txt';
        $expectedValue = parseFile($expectedFilePath);
        $diff = $differ->genDiff($testFilePath1, $testFilePath2)->report;
        $this->assertEquals($expectedValue, $diff);
        //plain format
        $expectedFilePath = __DIR__ . '/fixtures/plainTest.txt';
        $expectedValue = parseFile($expectedFilePath);
        $diff = $differ->genDiff($testFilePath1, $testFilePath2, 'plain')->report;
        $this->assertEquals($expectedValue, $diff);
    }
}
