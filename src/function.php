<?php
namespace Differ\DifferFunction;

use Differ\Differ;

function genDiff($path1, $path2, $format = null)
{
    $differ = new Differ();
    return $differ->genDiff($path1, $path2, $format)->report;
}
