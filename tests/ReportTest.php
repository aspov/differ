<?php
namespace Differ\tests;

use \PHPUnit\Framework\TestCase;
use Differ\Report;
use Differ\Parser;
use SplFileObject;
use Symfony\Component\Yaml\Yaml;

class ReportTest extends TestCase
{
    public function testGenDiff()
    {
        $report = new Report();
        $parser = new Parser();
        $testFilePath1 = __DIR__ . '/fixtures/testFile1.json';
        $testFilePath2 = __DIR__ . '/fixtures/testFile2.json';
        $expectedFilePath = __DIR__ . '/fixtures/expected.json';
        $content1 = $parser->parseFile($testFilePath1)->getContent();
        $content2 = $parser->parseFile($testFilePath2)->getContent();
        $expectedValue = $parser->parseFile($expectedFilePath)->getContent();
        $diffResult = $report->compare($content1, $content2);
        $this->assertEquals($expectedValue, $diffResult);
    }

    public function testGetReport()
    {
        //string output
        $report = new Report();
        $testValues = [
            ['testKey' => ['-' => 'test_value', '+' => 'test_value']],
            ['testKey' => "test_value"],
            ['testKey' => ['-' => 'test_value']],
            ['testKey' => ['+' => 'test_value']],
        ];
        $expectedValues = [
            "- testKey : test_value\n+ testKey : test_value",
            "  testKey : test_value",
            "- testKey : test_value",
            "+ testKey : test_value"
        ];
        for ($i = 0; $i < count($testValues); $i++) {
            $result = $report->getReport($testValues[$i]);
            $this->assertEquals($expectedValues[$i], $result);
        }
    }
}
