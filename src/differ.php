<?php
namespace Differ\differ;

use function Differ\parsers\parseFile;
use function Differ\formatters\prettyFormatter;
use function Differ\formatters\plainFormatter;
use function Differ\formatters\jsonFormatter;
use Funct\Collection;

function getDirectPath($path)
{
    return $path[0] == '/' ? $path : $_SERVER['PWD'] . '/' . $path;
}

function genDiff($filePath1, $filePath2, $format = 'pretty')
{
    $content1 = parseFile(getDirectPath($filePath1));
    $content2 = parseFile(getDirectPath($filePath2));
    $diff = compare($content1, $content2);
    if ($format == 'pretty') {
        return prettyFormatter($diff);
    } elseif ($format == 'plain') {
        return plainFormatter($diff);
    } elseif ($format == 'json') {
        return jsonFormatter($diff);
    }
}

function compare($content1, $content2)
{
    $content1 = get_object_vars($content1);
    $content2 = get_object_vars($content2);
    $result = Collection\flatten(array_map(function ($key) use ($content1, $content2) {
        $value1 = array_key_exists($key, $content1) ? $content1[$key] : null;
        $value2 = array_key_exists($key, $content2) ? $content2[$key] : null;
        $hasChildren = is_object($value1) || is_object($value2) ? true : false;
        $node = ['value' => $value1];
        if ($value1 && !$value2) {
            $node = ['type' => 'removed', 'value' => $value1];
            $value2 = $value1;
        } elseif (!$value1 && $value2) {
            $node = ['type' => 'added', 'value' => $value2];
            $value1 = $value2;
        } elseif ($value1 != $value2 && !$hasChildren) {
            $node = ['type' => 'changed', 'value' => ['old' => $value1, 'new' => $value2]];
        }
        if ($hasChildren) {
            unset($node['value']);
            $node['children'] = compare($value1, $value2);
        }
        return (object)array_merge(['key' => $key], $node);
    }, array_unique(array_merge(array_keys($content1), array_keys($content2)))));
    return $result;
}
