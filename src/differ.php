<?php
namespace Differ\differ;

use function Differ\parsers\parse;
use function Differ\formatters\prettyFormatter\prettyFormat;
use function Differ\formatters\plainFormatter\plainFormat;
use function Differ\formatters\jsonFormatter\jsonFormat;
use Funct\Collection;

function format($diff, $format)
{
    switch ($format) {
        case 'pretty':
            return prettyFormat($diff);
        case 'plain':
            return plainFormat($diff);
        case 'json':
            return jsonFormat($diff);
    }
}

function getAbsolutePath($path)
{
    return $path[0] == '/' ? $path : getcwd() . '/' . $path;
}

function genDiff($path1, $path2, $format = 'pretty')
{
    $filePath1 = getAbsolutePath($path1);
    $filePath2 = getAbsolutePath($path2);
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
    $result = array_map(function ($key) use ($content1, $content2) {
        $value1 = $content1[$key] ?? null;
        $value2 = $content2[$key] ?? null;
        $cont1HasKey = array_key_exists($key, $content1);
        $cont2HasKey = array_key_exists($key, $content2);
        $hasChildren = is_object($value1) || is_object($value2);
        $keysExists = $cont1HasKey && $cont2HasKey;
        if ($keysExists && $hasChildren) {
            $node = ['type' => 'unchanged nested','children' => compare($value1, $value2)];
        } elseif ($keysExists && $value1 == $value2) {
            $node = ['type' => 'unchanged', 'value' => $value1];
        } elseif ($keysExists && $value1 != $value2) {
            $node = ['type' => 'changed', 'value' => ['old' => $value1, 'new' => $value2]];
        } elseif ($cont1HasKey && $hasChildren) {
            $node = ['type' => 'removed nested', 'children' => compare($value1, $value1)];
        } elseif ($cont1HasKey) {
            $node = ['type' => 'removed', 'value' => $value1];
        } elseif ($cont2HasKey && $hasChildren) {
            $node = ['type' => 'added nested', 'children' => compare($value2, $value2)];
        } elseif ($cont2HasKey) {
            $node = ['type' => 'added', 'value' => $value2];
        }
        return (object)array_merge(['key' => $key], $node);
    }, array_values($keys));
    return $result;
}
