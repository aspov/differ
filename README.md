# Differ
[![Build Status](https://travis-ci.org/aspov/php-project-lvl2.svg?branch=master)](https://travis-ci.org/aspov/php-project-lvl2)
[![Maintainability](https://api.codeclimate.com/v1/badges/770ba18631330fdf088d/maintainability)](https://codeclimate.com/github/aspov/php-project-lvl2/maintainability)

Generates a difference between files

## Installation
```
$ composer global require aspov/differ
```
## Usage

```
$ gendiff --format=json path\To\file1.json path\to\file2.json

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  -v --version                  Show version
  --format <fmt>                Report format [default: pretty]
```
To your project, you can use the function
```php
<?php

use function Differ\DifferFunction\genDiff;

$diff = genDiff(path\To\file1.json, path\to\file2.json, 'json');
```
Supported extensions: json, yaml. Report formats: pretty, plain, json.

### Examples
Json
[![asciicast](https://asciinema.org/a/263630.svg)](https://asciinema.org/a/263630)
Yaml
[![asciicast](https://asciinema.org/a/264453.svg)](https://asciinema.org/a/264453)
AST
[![asciicast](https://asciinema.org/a/268568.svg)](https://asciinema.org/a/268568)
Plain format
[![asciicast](https://asciinema.org/a/268627.svg)](https://asciinema.org/a/268627)
Json format
[![asciicast](https://asciinema.org/a/269034.svg)](https://asciinema.org/a/269034)
