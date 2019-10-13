<?php
namespace Differ\formatters\prettyFormatter;

const DEFAULT_INDENT = '    ';
const INDENT_FOR_ADDED = '  + ';
const INDENT_FOR_REMOVED = '  - ';

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

function prettyFormat($diff, $indent = '')
{
    $result = array_reduce($diff, function ($report, $item) use ($indent) {
        $value = getValue($item) ?? prettyFormat($item->children, $indent . DEFAULT_INDENT);
        switch ($item->type) {
            case 'added':
                return "{$report}{$indent}" . INDENT_FOR_ADDED . "$item->key: $value\n";
            case 'removed':
                return "{$report}{$indent}" . INDENT_FOR_REMOVED . "$item->key: $value\n";
            case 'changed':
                return "{$report}{$indent}" . INDENT_FOR_ADDED . "$item->key: $value[new]\n" .
                                "{$indent}" . INDENT_FOR_REMOVED . "$item->key: $value[old]\n";
            case 'unchanged':
                return "{$report}{$indent}" . DEFAULT_INDENT . "$item->key: $value\n";
        }
    }, "\n");
    return "{{$result}{$indent}}";
}
