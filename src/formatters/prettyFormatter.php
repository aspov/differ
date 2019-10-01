<?php
namespace Differ\formatters;

function prettyFormatter($diff, $depth = 0)
{
    $reportResult = array_reduce($diff, function ($report, $item) use ($depth) {
        $indent = str_repeat("    ", $depth);
        $type = $item->type ?? '';
        $value = isset($item->children) ? "{" : $item->value;
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }
        if ($type) {
            $type == 'added' ? $report[] = $indent . "  + $item->key: $value" : null;
            $type == 'removed' ? $report[] = $indent .  "  - $item->key: $value" : null;
            $type == 'changed' ? $report[] = $indent .  "  + $item->key: $value[new]" : null;
            $type == 'changed' ? $report[] = $indent .  "  - $item->key: $value[old]" : null;
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
