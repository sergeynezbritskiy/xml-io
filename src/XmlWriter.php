<?php declare(strict_types=1);

namespace SergeyNezbritskiy\XmlIo;

use DomDocument;
use DOMNode;

/**
 * Class XmlWriter
 * @package SergeyNezbritskiy\XmlIo
 */
class XmlWriter extends AbstractCore
{

    /**
     * @param array $data
     * @param array $map
     * @return string
     */
    public function toXml(array $data, array $map): string
    {
        $document = new DOMDocument();
        foreach ($map as $nodeName => $nodeMap) {
            $this->appendElement($document, $document, $nodeName, $data, $nodeMap);
        }
        return $document->saveXML();
    }

    /**
     * @param DomDocument $document
     * @param DOMNode $parentNode
     * @param string $nodeName
     * @param $data
     * @param $map
     * @return void
     */
    private function appendElement(DOMDocument $document, DOMNode $parentNode, $nodeName, $data, $map)
    {
        /*
         * if node was set like ['user' => 'user']
         */
        if (is_string($map)) {
            $map = ['text' => $map];
        }
        /*
         * if node was set like ['user']
         */
        if (is_numeric($nodeName)) {
            $nodeName = $map['text'];
        }

        if (isset($map['dataProvider'])) {
            $data = $this->getValue($data, $map['dataProvider']);
        }
        if ($this->isArray($nodeName)) {
            $nodeName = substr($nodeName, 0, -2);
        } else {
            $data = [$data];
        }
        foreach ($data as $item) {
            $node = $document->createElement($nodeName);
            if (isset($map['text'])) {
                $text = $this->getValue($item, $map['text']);
                $textNode = $document->createTextNode($text);
                $node->appendChild($textNode);
            }
            if (isset($map['attributes'])) {
                foreach ($map['attributes'] as $attributeKey => $attributeConfig) {
                    $attributeNode = $this->createAttribute($document, $attributeKey, $attributeConfig, $item);
                    $node->appendChild($attributeNode);
                }
            }
            $parentNode->appendChild($node);
        }
    }

    /**
     * @param DomDocument $document
     * @param string $attributeName
     * @param array|string $attributeConfig
     * @param array $data
     * @return DOMNode
     */
    private function createAttribute(DOMDocument $document, $attributeName, $attributeConfig, $data): DOMNode
    {
        /*
         * if attribute was set like 'attributes' => ['attributeName' => 'dataKey']
         */
        if (is_string($attributeConfig)) {
            $attributeConfig = ['text' => $attributeConfig];
        }
        /*
         * if attribute was set like 'attributes' => ['attributeName']
         */
        if (is_numeric($attributeName)) {
            $attributeName = $attributeConfig['text'];
        }
        $attributeValue = $this->getValue($data, $attributeConfig['text']);
        $attributeNode = $document->createAttribute($attributeName);
        $textNode = $document->createTextNode($attributeValue);
        $attributeNode->appendChild($textNode);
        return $attributeNode;
    }

    /**
     * @param array $data
     * @param string $key
     * @return mixed
     */
    private function getValue($data, string $key)
    {
        return is_array($data) ? $data[$key] : $data;
    }

    /**
     * Returns true either $key is array or is string with suffix `[]`
     *
     * @param string $key
     * @return bool
     */
    protected function isArray($key): bool
    {
        return substr((string)$key, -2) === '[]';
    }

    /**
     * @param DomDocument $document
     * @param string $nodeName
     * @param array $map
     * @param array $data
     * @return DOMNode
     */
    private function arrayToNode(DOMDocument $document, string $nodeName, $map, $data): DOMNode
    {
        $node = $document->createElement($nodeName);
        if (isset($map['data'])) {
            $text = $this->getValue($data, $map['data']);
            $textNode = $document->createTextNode((string)$text);
            $node->appendChild($textNode);
        }
        if (isset($map['attributes'])) {
            foreach ($map['attributes'] as $attributeName => $attributeConfig) {
                $this->appendAttribute($document, $node, $attributeName, $attributeConfig, $data);
            }
        }
        if (isset($map['items'])) {
            foreach ($map['items'] as $childNodeName => $childNodeMap) {
                $this->appendElement($document, $node, $childNodeName, $data, $childNodeMap);
            }
        }
        return $node;
    }

}