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
            return json_decode($data, false);
        case "yml":
            return Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP);
        default:
            return $data;
    }
}
