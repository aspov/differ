<?php
namespace Differ\formatters;

function prettyFormatter($diff, $depth = 0)
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
            $report[] = prettyFormatter($item->children, $depth);
            $report[] = str_repeat("    ", $depth) . "}";
        }
        return $report;
    }, []);
    return $depth == 0 ? "{\n" . implode("\n", $reportResult) . "\n}" : implode("\n", $reportResult);
}
