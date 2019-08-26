<?php
namespace Differ;

use Symfony\Component\Yaml\Yaml;
use SplFileObject;

class Parser
{
    public $content = [];

    public function __construct($data = '', $format = '')
    {
        $this->content = $data;
        switch ($format) {
            case "json":
                $this->parseJson($data);
                break;
            case "yml":
                $this->parseYaml($data);
                break;
        }
    }

    public function getFile($filePath)
    {
        $pathToFile = $filePath[0] == '/' ? $filePath : $_SERVER['PWD'] . '/' . $filePath;
        $file = new SplFileObject($pathToFile);
        return $file;
    }
    
    public function parseFile($filePath)
    {
        $file = $this->getFile($filePath);
        $data = $file->fread($file->getSize());
        return new self($data, $file->getExtension());
    }

    public function parseYaml($content)
    {
        $data = Yaml::parse($content, 5, 10, Yaml::DUMP_OBJECT_AS_MAP);
        $parsedData = $this->normalize($data);
        $this->content = $parsedData;
        return new self($parsedData);
    }

    public function parseJson($content)
    {
        #var_dump(json_last_error());
        $data = json_decode($content, true);
        $parsedData = $this->normalize($data);
        $this->content = $parsedData;
        return new self($parsedData);
    }

    public function getContent()
    {
        return $this->content;
    }

    public function normalize($data)
    {
        foreach ($data as $key => $value) {
            if (is_bool($value)) {
                $data[$key] = $value ? 'true' : 'false';
            }
            if (is_null($value)) {
                $data[$key] = 'null';
            }
        }
        return $data;
    }
}
