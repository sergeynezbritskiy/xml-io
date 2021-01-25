<?php

declare(strict_types=1);

namespace SergeyNezbritskiy\XmlIo;

use SimpleXMLElement;

/**
 * Class XmlReader
 * @package SergeyNezbritskiy\XmlIo
 */
class XmlReader extends AbstractCore
{
    /**
     * @param string $xml
     * @param array $map
     * @return array
     */
    public function toArray(string $xml, array $map): array
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
}
