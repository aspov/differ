<?php
namespace Differ\formatters\plainFormatter;

use \Funct\Collection;

function getCorrectValue($value)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    } elseif (is_null($value)) {
        return 'null';
    } else {
        return $value;
    }
}

function plainFormat($diff, $path = '')
{
    $result = array_reduce($diff, function ($report, $item) use ($path) {
        switch ($item->type) {
            case 'added':
                $value = property_exists($item, 'value') ? getCorrectValue($item->value) : 'complex value';
                $report[] = "Property '{$path}{$item->key}' was added with value: '$value'";
                break;
            case 'removed':
                $report[] = "Property '{$path}{$item->key}' was removed";
                break;
            case 'changed':
                $oldValue = getCorrectValue($item->value['old']);
                $newValue = getCorrectValue($item->value['new']);
                $report[] = "Property '{$path}{$item->key}' was changed. From '$oldValue' to '$newValue'";
                break;
            case 'unchanged':
                $report[] = property_exists($item, 'children') ? plainFormat($item->children, "$item->key.") : [];
        }
        return $report;
    }, []);
    return implode("\n", Collection\compact($result));
}
