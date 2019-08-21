<?php
namespace Differ;

use SplFileObject;

class Report
{
    const OPTIONS = [
    ];
    private $options = [];
    public $result;

    public function __construct(array $option = self::OPTIONS)
    {
        $this->options = array_merge(self::OPTIONS, $option);
    }
    
    public function genDiff($path1, $path2)
    {
        $pathToFile1 = $path1[0] == '/' ? $path1 : $_SERVER['PWD'] . '/' . $path1;
        $pathToFile2 = $path2[0] == '/' ? $path2 : $_SERVER['PWD'] . '/' . $path2;
        $file1 = new SplFileObject($pathToFile1);
        $file2 = new SplFileObject($pathToFile2);
        $diff = $this->getFromJson($file1, $file2);
        return implode("\n", $diff);
    }

    public function getFromJson($file1, $file2)
    {
        $content1 = json_decode($file1->fread($file1->getSize()), true);
        $content2 = json_decode($file2->fread($file2->getSize()), true);
        $Keys = array_keys(array_merge($content1, $content2));
        
        $result = array_reduce($Keys, function ($diff, $item) use ($content1, $content2) {
            $value1 = isset($content1[$item]) ? $this->normalize($content1[$item]) : null;
            $value2 = isset($content2[$item]) ? $this->normalize($content2[$item]) : null;
        
            if (isset($content1[$item]) && isset($content2[$item])) {
                if ($value1 == $value2) {
                    $diff[] = "  {$item} : {$value1}";
                } else {
                    $diff[] = "- {$item} : {$value1}";
                    $diff[] = "+ {$item} : {$value2}";
                }
            } elseif (isset($content1[$item])) {
                $diff[] = "- {$item} : {$value1}";
            } elseif (isset($content2[$item])) {
                $diff[] = "+ {$item} : {$value2}";
            }
            return $diff;
        }, []);
        return $result;
    }

    public function normalize($value)
    {
        if (is_bool($value)) {
            $result = $value ? 'true' : 'false';
        } else {
            return $value;
        }
        return $result;
    }
}
