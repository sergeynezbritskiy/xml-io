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
        foreach ($map as $mapKey => $mapData) {
            if ($this->isArray($mapKey)) {
                $mapKey = substr($mapKey, 0, -2);
                $this->appendArray($result, $xml, $mapKey, $mapData);
            } elseif ($this->isArray($mapData)) {
                $this->appendNodes($result, $xml, $mapKey, $mapData);
            } else {
                $this->appendNode($result, $xml, $mapKey, $mapData);
            }
        }
        return $result;

    }

    /**
     * @param array $result
     * @param \SimpleXMLElement $xml
     * @param string $key
     * @param array $map
     */
    private function appendNodes(array &$result, \SimpleXMLElement $xml, string $key, array $map)
    {
        foreach ($map as $mapKey => $mapData) {
            if ($this->isArray($mapKey)) {
                $mapKey = substr($mapKey, 0, -2);
                $result[$mapKey] = [];
                $this->appendArray($result[$mapKey], $xml, $mapKey, $mapData);
            } elseif ($this->isArray($mapData)) {
                $result[$key] = [];
                $this->appendNodes($result[$key], $xml, $key, $mapData);
            } else {
                $this->appendNode($result, $xml, $mapKey, $mapData);
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
            $this->appendNodes($item, $itemData, $key, $map);
            $result[] = $item;
        }
    }

    /**
     * @param array $result
     * @param \SimpleXMLElement $xml
     * @param string $arrayKey
     * @param string $xmlKey
     */
    private function appendNode(array &$result, \SimpleXMLElement $xml, string $arrayKey, string $xmlKey)
    {
        if ($this->isAttribute($xmlKey)) {
            $data = substr($xmlKey, 1);
            $result[$arrayKey] = (string)$xml[$data];
        } else {
            $result[$arrayKey] = (string)$xml->$xmlKey;
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    private function isArray(string $key): bool
    {
        return is_array($key) || (substr($key, -2) === '[]');
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