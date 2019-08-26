<?php
namespace Differ;

use Differ\Parser;

class Report
{
    const OPTIONS = [];
    private $options = [];

    public function __construct(array $option = self::OPTIONS)
    {
        $this->options = array_merge(self::OPTIONS, $option);
    }
    
    public function genDiff($path1, $path2)
    {
        $parser = new Parser();
        $content1 = $parser->parseFile($path1)->getContent();
        $content2 = $parser->parseFile($path2)->getContent();
        $diff = $this->compare($content1, $content2);
        $report = $this->getReport($diff);
        return $report;
    }

    public function compare($content1, $content2)
    {
        $keys = array_keys(array_merge($content1, $content2));
        $diffResult = array_reduce($keys, function ($diff, $item) use ($content1, $content2) {
            $value1 = array_key_exists($item, $content1) ? $content1[$item] : null;
            $value2 = array_key_exists($item, $content2) ? $content2[$item] : null;

            if (array_key_exists($item, $content1) && array_key_exists($item, $content2)) {
                if ($value1 == $value2) {
                    $diff[$item] = $value1;
                } else {
                    $diff[$item] = ['-' => $value1, '+' => $value2];
                }
            } elseif (array_key_exists($item, $content1)) {
                $diff[$item] = ['-' => $value1];
            } elseif (array_key_exists($item, $content2)) {
                $diff[$item] = ['+' => $value2];
            }
            return $diff;
        }, []);
        return $diffResult;
    }

    public function getReport($result, $format = '')
    {
        $report = [];
        foreach ($result as $key => $value) {
            if (!is_array($result[$key])) {
                $report[] = "  $key : $value";
            } elseif (count($result[$key]) == 2) {
                $report[] = "- {$key} : {$result[$key]['-']}";
                $report[] = "+ {$key} : {$result[$key]['+']}";
            } elseif (count($result[$key]) == 1) {
                $keyName = array_key_exists('+', $result[$key]) ? '+' : '-';
                $report[] = "{$keyName} {$key} : {$result[$key][$keyName]}";
            }
        }
        return implode("\n", $report);
    }
}
