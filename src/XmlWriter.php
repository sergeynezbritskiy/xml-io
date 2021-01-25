<?php

declare(strict_types=1);

namespace SergeyNezbritskiy\XmlIo;

use DomDocument;
use DOMNode;

/**
 * Class XmlWriter
 * @package SergeyNezbritskiy\XmlIo
 */
class XmlWriter
{

    /**
     * @param array $data
     * @param array $map
     * @return DomDocument
     */
    public function toXml(array $data, array $map): DOMDocument
    {
        $document = new DOMDocument();
        foreach ($map as $nodeName => $nodeMap) {
            $this->appendElement($document, $document, $nodeName, $data, $nodeMap);
        }
        return $document;
    }

    /**
     * @param array $data
     * @param array $map
     * @return string
     */
    public function toXmlString(array $data, array $map): string
    {
        return $this->toXml($data, $map)->saveXML();
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

        if (substr($nodeName, -2) === '[]') {
            $nodeName = substr($nodeName, 0, -2);
        } else {
            $data = [$data];
        }

        foreach ($data as $item) {

            $node = $document->createElement($nodeName);

            if (isset($map['text'])) {
                $text = (string)$this->getValue($item, $map['text']);
                $textNode = $document->createTextNode($text);
                $node->appendChild($textNode);
            }

            if (isset($map['attributes'])) {
                foreach ($map['attributes'] as $attributeKey => $attributeConfig) {
                    $attributeNode = $this->createAttribute($document, $attributeKey, $attributeConfig, $item);
                    $node->appendChild($attributeNode);
                }
            }

            if (isset($map['children'])) {
                foreach ($map['children'] as $childNodeName => $childNodeMap) {
                    $this->appendElement($document, $node, $childNodeName, $item, $childNodeMap);
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
        $textNode = $document->createTextNode((string)$attributeValue);
        $attributeNode->appendChild($textNode);
        return $attributeNode;
    }

    /**
     * @param mixed $data
     * @param string $key
     * @return mixed
     */
    private function getValue($data, string $key)
    {
        if ($key == '{self}') {
            return $data;
        }
        return is_array($data) ? $data[$key] : $data;
    }

}