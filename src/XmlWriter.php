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
            $this->appendChild($document, $document, $nodeName, $data, $nodeMap);
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
    private function appendChild(DOMDocument $document, DOMNode $parentNode, $nodeName, $data, $map)
    {
        /*
         * if node was set like ['user' => 'user']
         */
        if (is_string($map)) {
            $map = ['data' => $map];
        }
        /*
         * if node was set like ['user']
         */
        if (is_numeric($nodeName)) {
            $nodeName = $map['data'];
        }

        $node = $document->createElement($nodeName);
        if (isset($map['data'])) {
            $text = $data[$map['data']];
            $textNode = $document->createTextNode($text);
            $node->appendChild($textNode);
        }
        if (isset($map['attributes'])) {
            foreach ($map['attributes'] as $attributeName => $attributeConfig) {
                $this->appendAttribute($document, $node, $attributeName, $attributeConfig, $data);
            }
        }
        $parentNode->appendChild($node);
    }

    /**
     * @param DomDocument $document
     * @param DOMNode $node
     * @param string $attributeName
     * @param array|string $attributeConfig
     * @param array $data
     */
    private function appendAttribute(DOMDocument $document, DOMNode $node, $attributeName, $attributeConfig, $data)
    {
        /*
         * if attribute was set like 'attributes' => ['attributeName' => 'dataKey']
         */
        if (is_string($attributeConfig)) {
            $attributeConfig = ['data' => $attributeConfig];
        }
        /*
         * if attribute was set like 'attributes' => ['attributeName']
         */
        if (is_numeric($attributeName)) {
            $attributeName = $attributeConfig['data'];
        }
        $attributeValue = $this->getValue($data, $attributeConfig['data']);
        $attributeNode = $document->createAttribute($attributeName);
        $textNode = $document->createTextNode($attributeValue);
        $attributeNode->appendChild($textNode);
        $node->appendChild($attributeNode);
    }

    /**
     * @param array $data
     * @param string $key
     * @return mixed
     */
    private function getValue(array $data, string $key)
    {
        return $data[$key];
    }

}