<?php declare(strict_types=1);

namespace SergeyNezbritskiy\XmlIo;

use DomDocument;
use DOMNode;

/**
 * Class XmlWriter
 * @package SergeyNezbritskiy\XmlIo
 */
class XmlWriter extends Core
{

    /**
     * @param array $array
     * @param string $rootElement
     * @param array $map
     * @return string
     */
    public function toXml(array $array, string $rootElement, array $map): string
    {
        $document = new DOMDocument();
        $document->appendChild($this->createNode($document, $rootElement, $array, $map));
        return $document->saveXML();
    }

    /**
     * @param DomDocument $document
     * @param string $nodeName
     * @param array $data
     * @param array $map
     * @return DOMNode
     */
    private function createNode(DOMDocument $document, string $nodeName, array $data, $map): DOMNode
    {
        $result = $this->createElement($document, $nodeName);
        foreach ($map as $childMapKey => $childMap) {

            list($childNodeKey, $childNodeName) = $this->parseKey((string)$childMapKey);

            if ($this->isArray($childMapKey)) {

                $childNode = $this->createElement($document, $childNodeKey);
                foreach ($this->getValue($data, $childNodeKey) as $item) {
                    if ($this->isArray($item)) {
                        $listItem = $this->createNode($document, $childNodeName, $item, $childMap);
                    } else {
                        $listItem = $this->createElement($document, $childNodeName, $item);
                    }
                    $childNode->appendChild($listItem);
                }
                $result->appendChild($childNode);

            } elseif ($this->isArray($childMap)) {

                $childNode = $this->createNode($document, $childNodeName, $data[$childNodeKey], $childMap);
                $result->appendChild($childNode);

            } elseif (is_string($childMapKey)) {

                $childNode = $this->createElement($document, $childNodeName, (string)$data[$childMap]);
                $result->appendChild($childNode);

            } else {

                $childNode = $document->createTextNode($data[$childMap]);
                $result->appendChild($childNode);

            }
        }
        return $result;
    }

    private function createElement(DOMDocument $document, $xmlKey, $data = null)
    {
        $nodeName = ltrim($xmlKey, '@');

        if ($this->isAttribute($xmlKey)) {
            $node = $document->createAttribute($nodeName);
        } else {
            $node = $document->createElement($nodeName);
        }

        if ($data !== null) {
            $nodeValue = $document->createTextNode((string)$data);
            $node->appendChild($nodeValue);
        }

        return $node;
    }

    /**
     * @param array $data
     * @param string $key
     * @return array|string
     */
    private function getValue(array $data, string $key)
    {
        $key = explode('.', $key);
        foreach ($key as $level) {
            $data = $data[$level];
        }
        return $data;
    }

}