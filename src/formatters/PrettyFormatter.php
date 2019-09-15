<?php
namespace Differ\formatters;

use \Funct\Collection;

class PrettyFormatter
{
    public $report;

    public function __construct($diff)
    {
        $report = $this->getReport($diff);
        $this->report = $report;
    }

    public function getReport($diff, $depth = 0)
    {
        $reportResult = array_reduce($diff, function ($report, $item) use ($depth) {
            $indent = str_repeat("    ", $depth);
            $action = $item->action ?? '';
            $value = isset($item->children) ? "{" : $item->value;
            if ($action) {
                $action == 'add' ? $report[] = $indent . "  + $item->key: $value" : null;
                $action == 'remove' ? $report[] = $indent .  "  - $item->key: $value" : null;
                $action == 'change' ? $report[] = $indent .  "  + $item->key: $value[added]" : null;
                $action == 'change' ? $report[] = $indent .  "  - $item->key: $value[removed]" : null;
            } else {
                $report[] = $indent . "    $item->key: $value";
            }
            if (isset($item->children)) {
                $depth++;
                $report[] = self::getReport($item->children, $depth);
                $report[] = str_repeat("    ", $depth) . "}";
            }
            return $report;
        }, []);
        return $depth == 0 ? "{\n" . implode("\n", $reportResult) . "\n}" : implode("\n", $reportResult);
    }
}
