<?php
namespace Differ;

use function Differ\Parsers\parseFile;
use \Funct\Collection;

class Report
{
    const OPTIONS = [];
    private $options = [];

    public function __construct($option = self::OPTIONS)
    {
        $this->options = array_merge(self::OPTIONS, $option);
    }

    public function genDiff($filePath1 = null, $filePath2 = null)
    {
        $path1 = $filePath1 ?? $this->options['<firstFile>'];
        $path2 = $filePath2 ?? $this->options['<secondFile>'];
        $content1 = parseFile($path1);
        $content2 = parseFile($path2);
        return $this->compare($content1, $content2);
    }

    public function getReport($diff, $format = null)
    {
        $format = $format ?? $this->options['--format'];
        if ($format == 'pretty') {
            return $this->getPrettyReport($diff);
        }
    }
    
    public function compare($content1, $content2)
    {
        $keys = array_keys(array_merge($content1, $content2));
        $diffResult = array_map(function ($key) use ($content1, $content2) {
            $value1 = isset($content1[$key]) ? $content1[$key] : null;
            $value2 = isset($content2[$key]) ? $content2[$key] : null;
            if (is_object($value1) && is_object($value2)) {
                $children = self::compare(get_object_vars($value1), get_object_vars($value2));
            } elseif (array_key_exists($key, $content1) && array_key_exists($key, $content2)) {
                if ($value1 == $value2) {
                    $itemValue = $value1;
                } else {
                    $action = 'change';
                    $itemValue = ['removed' => $value1, 'added' => $value2];
                }
            } elseif (array_key_exists($key, $content1)) {
                    $action = 'remove';
                    $children = is_object($value1) ?
                    self::compare(get_object_vars($value1), get_object_vars($value1)) : null;
                    $itemValue = ($children ?? null) ? '' : $value1;
            } elseif (array_key_exists($key, $content2)) {
                    $action = 'add';
                    $children = is_object($value2) ?
                    self::compare(get_object_vars($value2), get_object_vars($value2)) : null;
                    $itemValue = ($children ?? null) ? '' : $value2;
            }
            $astAction = ($action ?? false) ? ['action' => $action] : [];
            $astValue = ($children ?? false) ? ['children' => $children] : ['value' => $itemValue];
            return (object)array_merge(['key' => $key], $astAction, $astValue);
        }, $keys);
        return $diffResult;
    }

    public function getPrettyReport($diff, $depth = 0)
    {
        $reportResult = array_reduce($diff, function ($report, $item) use ($depth) {
            $indent = str_repeat("    ", $depth);
            $key = $item->key;
            $action = $item->action ?? '';
            $value = isset($item->children) ? "{" : $item->value;
            switch ($action) {
                case 'add':
                    $report[] = $indent . "  + $key: $value";
                    break;
                case 'remove':
                    $report[] = $indent . "  - $key: $value";
                    break;
                case 'change':
                    $report[] = $indent . "  + $key: $value[added]" ;
                    $report[] = $indent . "  - $key: $value[removed]" ;
                    break;
                default:
                    $report[] = $indent . "    $key: $value";
                    break;
            }
            if (isset($item->children)) {
                $depth++;
                $report[] = self::getPrettyReport($item->children, $depth);
                $report[] = str_repeat("    ", $depth) . "}";
            }
            return $report;
        }, []);
        return $depth == 0 ? "{\n" . implode("\n", $reportResult) . "\n}" : implode("\n", $reportResult);
    }
}
