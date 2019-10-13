<?php
namespace Differ\formatters\jsonFormatter;

function jsonFormat($diff)
{
    return json_encode($diff, JSON_PRETTY_PRINT);
}
