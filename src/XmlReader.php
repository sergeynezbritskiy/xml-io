<?php declare(strict_types=1);

namespace SergeyNezbritskiy\XmlIo;

use SimpleXMLElement;

/**
 * Class XmlReader
 * @package SergeyNezbritskiy\XmlIo
 */
class XmlReader
{

    /**
     * This is a key for defining non associative arrays
     * Can be used as in keys as in values
     */
    const KEY_LIST = '{list}';

    /**
     * @param string $filePath
     * @param array $map
     * @return array
     */
    public function fileToArray(string $filePath, array $map): array
    {
        return $this->stringToArray(file_get_contents($filePath), $map);
    }

    /**
     * @param string $xml
     * @param array $map
     * @return array
     */
    public function stringToArray(string $xml, array $map): array
    {
        return $this->xmlToArray(simplexml_load_string($xml), $map);
    }

    /**
     * @param SimpleXMLElement $xml
     * @param $map
     * @return array
     */
    public function xmlToArray(SimpleXMLElement $xml, $map): array
    {
        $result = [];
        foreach ($map as $arrayKey => $xmlKey) {

            list($currentArrayKey, $currentXmlKey) = $this->parseKey($arrayKey);

            if ($this->isArray($arrayKey)) {

                $currentArray = [];
                $currentNode = $this->getNode($xml, $currentXmlKey);
                foreach ($currentNode as $xml) {
                    $currentArray[] = $this->xmlToArray($xml, $xmlKey);
                }
                if ($currentArrayKey === null) {
                    $result = array_merge($result, $currentArray);
                } else {
                    $result[$currentArrayKey] = $currentArray;
                }

            } elseif ($xmlKey === self::KEY_LIST) {

                $result[$currentArrayKey] = (array)$this->getNode($xml, $currentXmlKey);

            } elseif ($this->isArray($xmlKey)) {

                $childXml = $this->getNode($xml, $currentXmlKey);
                $result[$currentArrayKey] = $this->xmlToArray($childXml, $xmlKey);

            } else {

                $result[$currentArrayKey] = (string)$this->getNode($xml, $xmlKey);

            }
        }
        return $result;
    }

    /**
     * @param string $key
     * @return array
     */
    private function parseKey(string $key): array
    {
        if (substr($key, -2) === '[]') {
            $key = substr($key, 0, -2);
        }
        $keyParts = explode(' as ', $key);
        if (count($keyParts) !== 2) {
            $keyParts = [$key, $key];
        }
        if ($keyParts[0] === self::KEY_LIST) {
            $keyParts[0] = null;
        }
        return $keyParts;
    }

    /**
     * @param SimpleXMLElement $xml
     * @param string $key
     * @return SimpleXMLElement
     */
    private function getNode(SimpleXMLElement $xml, string $key): SimpleXMLElement
    {
        $key = explode('.', $key);
        foreach ($key as $level) {
            if ($this->isAttribute($level)) {
                $level = substr($level, 1);
                $xml = $xml[$level];
            } else {
                $xml = $xml->$level;
            }
        }
        return $xml;
    }

    /**
     * Returns true either $key is array or is string with suffix `[]`
     *
     * @param string $key
     * @return bool
     */
    private function isArray($key): bool
    {
        return is_array($key) || (substr((string)$key, -2) === '[]');
    }

    /**
     * Returns true if $key starts with `@` which means
     * that xml element attribute requested
     *
     * @param string $key
     * @return bool
     */
    private function isAttribute(string $key): bool
    {
        return strpos($key, '@') === 0;
    }

}