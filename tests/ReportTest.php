<?php
namespace Differ\tests;

use \PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use Differ\Report;
use function Differ\Parsers\parseFile;

class ReportTest extends TestCase
{
    public function testGetReport()
    {
        $report = new Report();
        //JSON
        $testFilePath1 = __DIR__ . '/fixtures/testFile1.json';
        $testFilePath2 = __DIR__ . '/fixtures/testFile2.json';
        $expectedFilePath = __DIR__ . '/fixtures/expected.txt';
        $expectedValue = parseFile($expectedFilePath);
        #print_r($expectedValue);
        $diff = $report->genDiff($testFilePath1, $testFilePath2);
        $result = $report->getReport($diff, 'pretty');
        $this->assertEquals($expectedValue, $result);
        //YAML
        $testFilePath1 = __DIR__ . '/fixtures/testFile1.yml';
        $testFilePath2 = __DIR__ . '/fixtures/testFile2.yml';
        $expectedValue = parseFile($expectedFilePath);
        $diff = $report->genDiff($testFilePath1, $testFilePath2);
        $result = $report->getReport($diff, 'pretty');
        $this->assertEquals($expectedValue, $result);
        //AST
        $testFilePath1 = __DIR__ . '/fixtures/astFile1.json';
        $testFilePath2 = __DIR__ . '/fixtures/astFile2.json';
        $expectedFilePath = __DIR__ . '/fixtures/expectedAST.txt';
        $expectedValue = parseFile($expectedFilePath);
        $diff = $report->genDiff($testFilePath1, $testFilePath2);
        $result = $report->getReport($diff, 'pretty');
        $this->assertEquals($expectedValue, $result);
    }
}
