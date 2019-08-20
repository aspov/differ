<?php
namespace Differ;

use function Differ\diffFunctions\genDiff;

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

#require(__DIR__ . '/../vendor/docopt//docopt/src/docopt.php');
#print_r(array_slice($_SERVER['argv'], 1));
$handler = new \Docopt\Handler(array(
    'help' => true,
    'version' => 'Generate diff v0.1',
    'optionsFirst' => false,
));

$response = $handler->handle($doc);
$path1 = $response['<firstFile>'];
$path2 = $response['<secondFile>'];
echo (genDiff($path1, $path2));
echo ("\n");
