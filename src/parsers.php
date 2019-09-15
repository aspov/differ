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
