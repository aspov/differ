<?php
namespace Differ\formatters;

function jsonFormatter($diff)
{
    return json_encode($diff, JSON_PRETTY_PRINT);
}
