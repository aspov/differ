<?php
namespace Differ;

use function Differ\Parsers\parseFile;

class Differ
{
    const OPTIONS = [];
    private $options = [];

    public function __construct($option = self::OPTIONS)
    {
        $this->options = array_merge(self::OPTIONS, $option);
    }

    public function genDiff($filePath1 = null, $filePath2 = null, $format = null)
    {
        $path1 = $filePath1 ?? $this->options['<firstFile>'];
        $path2 = $filePath2 ?? $this->options['<secondFile>'];
        $format = $format ?? $this->options['--format'] ?? 'pretty';
        $content1 = parseFile($path1);
        $content2 = parseFile($path2);
        $diff = $this->compare($content1, $content2);
        if ($format == 'pretty') {
            return new formatters\PrettyFormatter($diff);
        } elseif ($format == 'plain') {
            return new formatters\PlainFormatter($diff);
        } elseif ($format == 'json') {
            return new formatters\JsonFormatter($diff);
        }
    }

    public function compare($content1, $content2)
    {
        $result = array_map(function ($key) use ($content1, $content2) {
            $value1 = array_key_exists($key, $content1) ? $content1[$key] : null;
            $value2 = array_key_exists($key, $content2) ? $content2[$key] : null;
            is_object($value1) ? $children = self::compare(get_object_vars($value1), get_object_vars($value1)) : null;
            is_object($value2) ? $children = self::compare(get_object_vars($value2), get_object_vars($value2)) : null;
            if (is_object($value1) && is_object($value2)) {
                $children = self::compare(get_object_vars($value1), get_object_vars($value2));
            } elseif (array_key_exists($key, $content1) && array_key_exists($key, $content2)) {
                if ($value1 == $value2) {
                    $itemValue = $value1;
                } else {
                    $astAction = ['action' => 'change'];
                    $itemValue = ['removed' => $value1, 'added' => $value2];
                }
            } elseif (array_key_exists($key, $content1)) {
                    $astAction = ['action' => 'remove'];
                    $itemValue =  $value1;
            } elseif (array_key_exists($key, $content2)) {
                    $astAction = ['action' => 'add'];
                    $itemValue = $value2;
            }
            $astValue = ($children ?? false) ? ['children' => $children] : ['value' => $itemValue];
            return (object)array_merge(['key' => $key], $astAction ?? [], $astValue);
        }, array_keys(array_merge($content1, $content2)));
        return $result;
    }
}
