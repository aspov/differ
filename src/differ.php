<?php
namespace Differ;

#use function Differ\functions\genDiff;
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

$handler = new \Docopt\Handler(array(
    'help' => true,
    'version' => 'Generate diff v0.1',
    'optionsFirst' => false,
));

$response = $handler->handle($doc);
$path1 = $response['<firstFile>'];
$path2 = $response['<secondFile>'];
$report = new Report($response->args); //config
$result = $report->genDiff($path1, $path2);
echo ($result);
echo ("\n");
