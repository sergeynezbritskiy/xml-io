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
        return $this->parseString(file_get_contents($filePath), $map);
    }

    /**
     * @param string $xml
     * @param array $map
     * @return array
     */
    public function parseString(string $xml, array $map): array
    {
        return $this->parse(simplexml_load_string($xml), $map);
    }

    /**
     * @param SimpleXMLElement $xml
     * @param $map
     * @return array
     */
    public function parse(SimpleXMLElement $xml, $map): array
    {
        $result = [];
        foreach ($map as $arrayKey => $xmlKey) {
            if ($this->isArray($arrayKey)) {
            } elseif ($this->isArray($xmlKey)) {
                $childXml = $this->getNode($xml, $arrayKey);
                $result[$arrayKey] = $this->parse($childXml, $xmlKey);
            } else {
                $result[$arrayKey] = (string)$this->getNode($xml, $xmlKey);
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
        $keyParts = explode(' as ', $key);
        if (count($keyParts) !== 2) {
            $keyParts = [$key, $key];
        }
        if ($keyParts[0] === '{assoc}') {
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