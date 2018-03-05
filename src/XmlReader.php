<?php declare(strict_types=1);

namespace SergeyNezbritskiy\XmlIo;

/**
 * Class XmlReader
 * @package SergeyNezbritskiy\XmlIo
 */
class XmlReader
{

    /**
     * XmlReader constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param string $filePath
     * @param array $map
     * @return array
     */
    public function parseFile(string $filePath, array $map): array
    {
        return $this->parse(file_get_contents($filePath), $map);
    }

    /**
     * @param string $xml
     * @param array $map
     * @return array
     */
    public function parse(string $xml, array $map): array
    {
        $result = [];
        $xml = simplexml_load_string($xml);
        foreach ($map as $key => $data) {
            $this->appendNode($result, $xml, $key, $data);
        }
        return $result;

    }

    /**
     * @param array $result
     * @param \SimpleXMLElement $xml
     * @param string $key
     * @param array $map
     */
    private function appendNode(array &$result, \SimpleXMLElement $xml, string $key, array $map)
    {
        if ($this->isArray($key)) {
            $key = substr($key, 0, strlen($key) - 2);
            $result[$key] = [];
            $this->appendArray($result[$key], $xml, $key, $map);
        } else {
            foreach ($map as $key => $data) {
                if (is_string($data) && $this->isAttribute($data)) {
                    $data = substr($data, 1);
                    $result[$key] = (string)$xml[$data];
                } else {
                    $result[$key] = (string)$xml->$data;
                }
            }
        }
    }

    /**
     * @param array $result
     * @param \SimpleXMLElement $xml
     * @param string $key
     * @param array $map
     */
    private function appendArray(array &$result, \SimpleXMLElement $xml, string $key, array $map)
    {
        foreach ($xml->$key as $itemData) {
            $item = [];
            $this->appendNode($item, $itemData, $key, $map);
            $result[] = $item;
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    private function isArray(string $key): bool
    {
        return substr($key, -2) === '[]';
    }

    /**
     * @param string $key
     * @return bool
     */
    private function isAttribute(string $key): bool
    {
        return strpos($key, '@') === 0;
    }

}