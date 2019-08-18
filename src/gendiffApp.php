<?php
namespace gendiffApp;

use Docopt;

$doc = <<<DOC
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)

Options:
  -h --help                     Show this screen
  -v --version                  Show version

DOC;

// short form, simple API
#$args = Docopt::handle($doc);

// short form (5.4 or better)
#$args = (new \Docopt\Handler)->handle($sdoc);

/*
// long form, simple API (equivalent to short)
$params = array(
    'argv' => array_slice($_SERVER['argv'], 1),
    'help' => true,
    'version' => null,
    'optionsFirst' => false,
);
$args = Docopt::handle($doc, $params);
*/

// long form, full API
$handler = new \Docopt\Handler(array(
    'argv' => array_slice($_SERVER['argv'], 1),
    'help' => true,
    'version' => 'Generate diff v0.1',
    'optionsFirst' => false,
));

$handler->handle($doc, $argv);
