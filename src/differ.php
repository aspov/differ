<?php
namespace Differ\differ;

use function Differ\parsers\parseFile;
use function Differ\formatters\prettyFormatter;
use function Differ\formatters\plainFormatter;
use function Differ\formatters\jsonFormatter;

function genDiff($filePath1, $filePath2, $format = 'pretty')
{
    $content1 = normalize(parseFile($filePath1));
    $content2 = normalize(parseFile($filePath2));
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
    $result = array_map(function ($key) use ($content1, $content2) {
        $value1 = array_key_exists($key, $content1) ? $content1[$key] : null;
        $value2 = array_key_exists($key, $content2) ? $content2[$key] : null;
        is_object($value1) ? $children = compare(get_object_vars($value1), get_object_vars($value1)) : null;
        is_object($value2) ? $children = compare(get_object_vars($value2), get_object_vars($value2)) : null;
        if (is_object($value1) && is_object($value2)) {
            $children = compare(get_object_vars($value1), get_object_vars($value2));
        } elseif (array_key_exists($key, $content1) && array_key_exists($key, $content2)) {
            if ($value1 == $value2) {
                $itemValue = $value1;
            } else {
                $astAction = ['action' => 'change'];
                $itemValue = ['removed' => $value1, 'added' => $value2];
            }
        } elseif (array_key_exists($key, $content1)) {
                $astAction = ['action' => 'remove'];
                $itemValue =  $value1;
        } elseif (array_key_exists($key, $content2)) {
                $astAction = ['action' => 'add'];
                $itemValue = $value2;
        }
        $astValue = ($children ?? false) ? ['children' => $children] : ['value' => $itemValue];
        return (object)array_merge(['key' => $key], $astAction ?? [], $astValue);
    }, array_keys(array_merge($content1, $content2)));
    return $result;
}

function normalize($data)
{
    $data = get_object_vars($data);
    $keys = array_keys($data);
    $result = array_reduce($keys, function ($result, $key) use ($data) {
        if (is_bool($data[$key])) {
            $data[$key] = $data[$key] ? 'true' : 'false';
        }
        if (is_null($data[$key])) {
            $data[$key] = 'null';
        }
        if (is_object($data[$key])) {
            $result[$key] = (object)normalize($data[$key]);
        } else {
            $result[$key] = $data[$key];
        }
        return $result;
    }, []);
    return $result;
}
