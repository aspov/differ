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
            case 'unchanged nested':
                $report[] = plainFormat($item->children, "$item->key.");
                break;
            case 'added':
                $value = getCorrectValue($item->value);
                $report[] = "Property '{$path}{$item->key}' was added with value: '$value'";
                break;
            case 'added nested':
                $report[] = "Property '{$path}{$item->key}' was added with value: 'complex value'";
                break;
            case 'removed':
                $report[] = "Property '{$path}{$item->key}' was removed";
                break;
            case 'removed nested':
                $report[] = "Property '{$path}{$item->key}' was removed";
                break;
            case 'changed':
                $oldValue = getCorrectValue($item->value['old']);
                $newValue = getCorrectValue($item->value['new']);
                $report[] = "Property '{$path}{$item->key}' was changed. From '$oldValue' to '$newValue'";
        }
        return $report;
    }, []);
    return implode("\n", Collection\compact($result));
}
