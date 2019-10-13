<?php
namespace Differ\formatters\plainFormatter;

use \Funct\Collection;

function getValue($item)
{
    if (property_exists($item, 'value')) {
        if (is_bool($item->value)) {
            return $item->value ? 'true' : 'false';
        } elseif (is_null($item->value)) {
            return 'null';
        } else {
            return $item->value;
        }
    }
}

function plainFormat($diff, $path = '')
{
    $result = array_reduce($diff, function ($report, $item) use ($path) {
        $property = "'{$path}{$item->key}'";
        $value = getValue($item) ?? 'complex value';
        switch ($item->type) {
            case 'added':
                $report[] = "Property {$property} was added with value: '$value'";
                break;
            case 'removed':
                $report[] = "Property {$property} was removed";
                break;
            case 'changed':
                $report[] = "Property {$property} was changed. From '$value[old]' to '$value[new]'";
                break;
            case $value == 'complex value':
                $report[] = plainFormat($item->children, "$item->key.");
                break;
        }
        return $report;
    }, []);
    return implode("\n", Collection\compact($result));
}
