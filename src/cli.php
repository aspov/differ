<?php
namespace Differ;

use Differ\Differ;

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
$differ = new Differ($handler->handle($doc)->args);
$result = $differ->genDiff()->report;
echo($result . "\n");
