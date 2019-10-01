<?php
namespace Differ\formatters;

use \Funct\Collection;

function plainFormatter($diff, $depthKey = '')
{
    $reportResult = array_reduce($diff, function ($report, $item) use ($depthKey) {
        $type = $item->type ?? '';
        $key = "'{$depthKey}{$item->key}'";
        $value = isset($item->children) ? 'complex value' : $item->value;
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }
        if ($type) {
            ($type == 'added') ?
            $report[] = "Property {$key} was added with value: '$value'" : null;
            ($type == 'removed') ?
            $report[] = "Property {$key} was removed" : null;
            ($type == 'changed') ?
            $report[] = "Property {$key} was changed. From '$value[old]' to '$value[new]'" : null;
        }
        if (isset($item->children)) {
            $depthKey = "$item->key.";
            $report[] = plainFormatter($item->children, $depthKey);
        }
        return $report;
    }, []);
    return implode("\n", Collection\compact($reportResult));
}
