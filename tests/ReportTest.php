<?php

namespace Differ\tests;

use \PHPUnit\Framework\TestCase;
use Differ\Report;

class ReportTest extends TestCase
{
    public function testGetFromJson()
    {
        $report = new Report();
        $file1 = $report->getFile(__DIR__ . '/fixtures/testFile1.php');
        $file2 = $report->getFile(__DIR__ . '/fixtures/testFile2.php');
        $assertFile = $report->getFile(__DIR__ . '/fixtures/assertingText.php');
        $assertingText = $assertFile->fread(($assertFile->getSize()) - 1); // - 1 to remove '\n' at the and of the file
        $diffResult = $report->getFromJson($file1, $file2);
        $this->assertEquals($assertingText, $diffResult);
    }
}
