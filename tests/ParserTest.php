<?php
namespace Differ\tests;

use \PHPUnit\Framework\TestCase;
use Differ\Report;
use Differ\Parser;

class ParserTest extends TestCase
{
    public function testParseYaml()
    {
        $report = new Report();
        $parser = new Parser();
        $expectedFilePath = __DIR__ . '/fixtures/expected.json';
        $testFilePath = __DIR__ . '/fixtures/expected.yml';
        $expectedValue = $parser->parseFile($expectedFilePath)->getContent(); //array from json
        $parsedYml = $parser->parseFile($testFilePath)->getContent();         //array from yaml
        $this->assertEquals($expectedValue, $parsedYml);
    }

    public function testNormalize()
    {
        $parser = new Parser();
        $this->assertEquals(['item' => 'true'], $parser->normalize(['item' => (bool) 1]));
        $this->assertEquals(['item' => 'true'], $parser->normalize(['item' => (bool) true]));
        $this->assertEquals(['item' => '1'], $parser->normalize(['item' => (int) 1]));
        $this->assertEquals(['item' => 'true'], $parser->normalize(['item' => (string) 'true']));
        $this->assertEquals(['item' => 'null'], $parser->normalize(['item' => null]));
        $this->assertEquals(['item' => '/%*://\\'], $parser->normalize(['item' => '/%*://\\']));
    }
}
