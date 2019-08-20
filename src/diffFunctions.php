<?php
namespace Differ\diffFunctions;

use Funct\Collection;
use SplFileObject;

function genDiff($path1, $path2)
{
    $pathToFile1 = $path1[0] == '/' ? $path1 : $_SERVER['PWD'] . '/' . $path1;
    $pathToFile2 = $path2[0] == '/' ? $path2 : $_SERVER['PWD'] . '/' . $path2;
    $file1 = new SplFileObject($pathToFile1);
    $file2 = new SplFileObject($pathToFile2);

    if (!$file1->isFile() && !$file1->isFile()) {
        echo ('error: this is not a files');
        exit;
    }

    #if (!$file1->isReadable() && !$file1->isReadable()) {
        #echo ('error: can\'t read the file');
        #exit;
    #}

    if ($file1->getExtension() != $file2->getExtension()) {
        echo ('error: different extension of the files');
        exit;
    }

    $content1 = json_decode($file1->fread($file1->getSize()), true);
    $content2 = json_decode($file2->fread($file2->getSize()), true);
    $Keys = array_keys(array_merge($content1, $content2));

    $diff = array_reduce($Keys, function ($diff, $item) use ($content1, $content2) {
        if (isset($content1[$item]) && isset($content2[$item])) {
            if ($content1[$item] == $content2[$item]) {
                $diff[] = "  {$item} : {$content1[$item]}";
            } else {
                $diff[] = "- {$item} : {$content1[$item]}";
                $diff[] = "+ {$item} : {$content2[$item]}";
            }
        } elseif (isset($content1[$item])) {
            $diff[] = "- {$item} : {$content1[$item]}";
        } elseif (isset($content2[$item])) {
            $diff[] = "+ {$item} : {$content2[$item]}";
        }
        return $diff;
    }, []);

    return implode("\n", $diff);
}
