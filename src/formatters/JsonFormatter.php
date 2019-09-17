<?php
namespace Differ\formatters;

use \Funct\Collection;

class JsonFormatter
{
    public $report;

    public function __construct($diff)
    {
        $report = $this->getReport($diff);
        $this->report = $report;
    }

    public function getReport($diff)
    {
        return json_encode($diff, JSON_PRETTY_PRINT);
    }
}
