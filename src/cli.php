<?php
namespace Differ;

use function Differ\differ\genDiff;

const DOC = <<<DOC
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
function run()
{
    $handler = new \Docopt\Handler(array('version' => 'Generate diff v0.1'));
    $args = $handler->handle(DOC)->args;
    $path1 = $args['<firstFile>'];
    $path2 = $args['<secondFile>'];
    $format = $args['--format'];
    $diff = genDiff($path1, $path2, $format);
    echo($diff . "\n");
}
