<?php
namespace Differ\differ;

use function Differ\parsers\parse;
use function Differ\formatters\prettyFormatter;
use function Differ\formatters\plainFormatter;
use function Differ\formatters\jsonFormatter;
use Funct\Collection;

function getDirectPath($path)
{
    return $path[0] == '/' ? $path : getcwd() . '/' . $path;
}

function format($diff, $format)
{
    if ($format == 'pretty') {
        return prettyFormatter($diff);
    } elseif ($format == 'plain') {
        return plainFormatter($diff);
    } elseif ($format == 'json') {
        return jsonFormatter($diff);
    }
}

function genDiff($filePath1, $filePath2, $format = 'pretty')
{
    $fileData1 = file_get_contents($filePath1);
    $fileData2 = file_get_contents($filePath2);
    $fileExtension1 = pathinfo($filePath1, PATHINFO_EXTENSION);
    $fileExtension2 = pathinfo($filePath2, PATHINFO_EXTENSION);
    $content1 = parse($fileData1, $fileExtension1);
    $content2 = parse($fileData2, $fileExtension2);
    $diff = compare($content1, $content2);
    return format($diff, $format);
}

function compare($content1, $content2)
{
    $content1 = get_object_vars($content1);
    $content2 = get_object_vars($content2);
    $keys = Collection\union(array_keys($content1), array_keys($content2));
    $result = Collection\flatten(array_map(function ($key) use ($content1, $content2) {
        $value1 = array_key_exists($key, $content1) ? $content1[$key] : null;
        $value2 = array_key_exists($key, $content2) ? $content2[$key] : null;
        $hasChildren = is_object($value1) || is_object($value2) ? true : false;
        if ($value1 && !$value2) {
            $node = ['type' => 'removed', 'value' => $value1];
            $value2 = $value1;
        } elseif (!$value1 && $value2) {
            $node = ['type' => 'added', 'value' => $value2];
            $value1 = $value2;
        } elseif ($value1 != $value2 && !$hasChildren) {
            $node = ['type' => 'changed', 'value' => ['old' => $value1, 'new' => $value2]];
        } else {
            $node = ['value' => $value1];
        }
        if ($hasChildren) {
            unset($node['value']);
            $node['children'] = compare($value1, $value2);
        }
        return (object)array_merge(['key' => $key], $node);
    }, $keys));
    return $result;
}
