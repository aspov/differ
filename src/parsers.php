<?php
namespace Differ\parsers;

use Symfony\Component\Yaml\Yaml;

function parseFile($filePath)
{
    $pathToFile = $filePath[0] == '/' ? $filePath : $_SERVER['PWD'] . '/' . $filePath;
    $extension = pathinfo($pathToFile, PATHINFO_EXTENSION);
    $data = file_get_contents($pathToFile);
    switch ($extension) {
        case "json":
            return  normalize(json_decode($data, false));
        case "yml":
            return normalize(Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP));
        default:
            return $data;
    }
}

function normalize($data, $result = [])
{
    $data = get_object_vars($data);
    foreach ($data as $key => $value) {
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }
        if (is_null($value)) {
            $value = 'null';
        }
        if (is_object($data[$key])) {
            $result[$key] = (object)normalize($data[$key]);
        } else {
            $result[$key] = $value;
        }
    }
    return $result;
}
