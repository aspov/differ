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

function getStringValue($itemValue, $depth = 0)
{
    $itemValue = is_object($itemValue) ? get_object_vars($itemValue) : $itemValue;
    if (is_array($itemValue)) {
        $result = array_map(function ($key) use ($itemValue, $depth) {
            $value = is_array($itemValue[$key]) ? getStringValue($itemValue[$key], $depth + 1) :
            getCorrectValue($itemValue[$key]);
            return str_repeat(DEFAULT_INDENT, $depth + 1) . "$key: $itemValue[$key]";
        }, array_keys($itemValue));
        return "{\n" . implode("\n", $result) . "\n" . str_repeat(DEFAULT_INDENT, $depth) . "}";
    } else {
        return getCorrectValue($itemValue);
    }
}

function prettyFormat($diff, $depth = 0)
{
    $result = array_reduce($diff, function ($report, $item) use ($depth) {
        switch ($item->type) {
            case 'nested':
                $resultValue[] = DEFAULT_INDENT . "$item->key: " . prettyFormat($item->children, $depth + 1);
                break;
            case 'unchanged':
                $resultValue[] = DEFAULT_INDENT . "$item->key: " . getStringValue($item->value, $depth + 1);
                break;
            case 'added':
                $resultValue[] = INDENT_FOR_ADDED . "$item->key: " . getStringValue($item->value, $depth + 1);
                break;
            case 'removed':
                $resultValue[] = INDENT_FOR_REMOVED . "$item->key: " . getStringValue($item->value, $depth + 1);
                break;
            case 'changed':
                $resultValue[] = INDENT_FOR_ADDED . "$item->key: " . getStringValue($item->value['new'], $depth + 1);
                $resultValue[] = INDENT_FOR_REMOVED . "$item->key: " . getStringValue($item->value['old'], $depth + 1);
        }
        return implode("\n" . str_repeat(DEFAULT_INDENT, $depth), array_merge([$report], $resultValue));
    }, '');
    return "{" . $result . "\n" . str_repeat(DEFAULT_INDENT, $depth) . "}";
}
