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

function prettyFormat($diff, $depth = 0)
{
    $result = array_reduce($diff, function ($report, $item) use ($depth) {
        switch ($item->type) {
            case 'added':
                $nodeValue = property_exists($item, 'value') ?
                getCorrectValue($item->value) : prettyFormat($item->children, $depth + 1);
                $resultValue[] = INDENT_FOR_ADDED . "$item->key: $nodeValue";
                break;
            case 'removed':
                $nodeValue = property_exists($item, 'value') ?
                getCorrectValue($item->value) : prettyFormat($item->children, $depth + 1);
                $resultValue[] = INDENT_FOR_REMOVED . "$item->key: $nodeValue";
                break;
            case 'changed':
                $resultValue[] = INDENT_FOR_ADDED . "$item->key: " . getCorrectValue($item->value['new']);
                $resultValue[] = INDENT_FOR_REMOVED . "$item->key: " . getCorrectValue($item->value['old']);
                break;
            case 'unchanged':
                $nodeValue = property_exists($item, 'value') ?
                getCorrectValue($item->value) : prettyFormat($item->children, $depth + 1);
                $resultValue[] = DEFAULT_INDENT . "$item->key: $nodeValue";
                break;
        }
        return implode("\n" . str_repeat(DEFAULT_INDENT, $depth), array_merge([$report], $resultValue));
    }, '');
    return "{" . $result . "\n" . str_repeat(DEFAULT_INDENT, $depth) . "}";
}
