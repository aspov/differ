<?php
namespace Differ;

use Differ\Report;

$doc = <<<DOC
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  -v --version                  Show version
  --format <fmt>                Report format [default: pretty]
DOC;

$handler = new \Docopt\Handler();
$report = new Report($handler->handle($doc)->args);
$diff = $report->genDiff();
$result = $report->getReport($diff);
echo($result . "\n");
