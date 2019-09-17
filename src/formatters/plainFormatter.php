<?php
namespace Differ\formatters;

use \Funct\Collection;

function plainFormatter($diff, $depthKey = '')
{
    $reportResult = array_reduce($diff, function ($report, $item) use ($depthKey) {
        $action = $item->action ?? '';
        $key = "'{$depthKey}{$item->key}'";
        $value = isset($item->children) ? 'complex value' : $item->value;
        if ($action) {
            ($action == 'add') ?
            $report[] = "Property {$key} was added with value: '$value'" : null;
            ($action == 'remove') ?
            $report[] = "Property {$key} was removed" : null;
            ($action == 'change') ?
            $report[] = "Property {$key} was changed. From '$value[removed]' to '$value[added]'" : null;
        }
        if (isset($item->children)) {
            $depthKey = "$item->key.";
            $report[] = plainFormatter($item->children, $depthKey);
        }
        return $report;
    }, []);
    return implode("\n", Collection\compact($reportResult));
}
