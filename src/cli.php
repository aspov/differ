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

$handler = new \Docopt\Handler(array('version' => 'Generate diff v0.1'));
$differ = new Differ($handler->handle($doc)->args);
echo($differ->genDiff()->report . "\n");
