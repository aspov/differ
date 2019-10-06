<?php
namespace Differ\parsers;

use Symfony\Component\Yaml\Yaml;

function parse($data, $format)
{
    switch ($format) {
        case "json":
            return json_decode($data, false);
        case "yml":
            return Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP);
    }
}
