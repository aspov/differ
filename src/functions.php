<?php
namespace Differ\functions;

use Differ\Report;

function genDiff($path1, $path2)
{
    $report = new Report();
    $result = $report->genDiff($path1, $path2);
    return implode("\n", $result);
}
