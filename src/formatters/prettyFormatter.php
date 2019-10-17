<?php
namespace Differ\formatters\prettyFormatter;

const DEFAULT_INDENT = '    ';
const INDENT_FOR_ADDED = '  + ';
const INDENT_FOR_REMOVED = '  - ';

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

function prettyFormat($diff, $indent = '')
{
    $result = array_reduce($diff, function ($report, $item) use ($indent) {
        $hasValue = property_exists($item, 'value');
        $value = $hasValue ? getCorrectValue($item->value) : prettyFormat($item->children, $indent . DEFAULT_INDENT);
        $resultValue[] = $report;
        switch ($item->type) {
            case 'added':
                $resultValue[] = INDENT_FOR_ADDED . "$item->key: $value";
                break;
            case 'removed':
                $resultValue[] = INDENT_FOR_REMOVED . "$item->key: $value";
                break;
            case 'changed':
                $resultValue[] = INDENT_FOR_ADDED . "$item->key: $value[new]";
                $resultValue[] = INDENT_FOR_REMOVED . "$item->key: $value[old]";
                break;
            case 'unchanged':
                $resultValue[] = DEFAULT_INDENT . "$item->key: $value";
                break;
        }
        return implode("\n" . $indent, $resultValue);
    }, '');
    return "{{$result}\n{$indent}}";
}
