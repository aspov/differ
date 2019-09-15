<?php
namespace Differ\functions;

use Differ\Report;

function genDiff($path1, $path2)
{
    $report = new Report();
    $diff = $report->genDiff($path1, $path2);
    $result = $report->getReport($diff, 'pretty');
    return $result;
}
