<?php declare(strict_types=1);

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
    private function createNode(DOMDocument $document, string $nodeName, array $data, array $map): DOMNode
    {
        $result = $document->createElement($nodeName);
        foreach ($map as $key => $value) {

            if ($this->isArray($key)) {

            } elseif ($this->isArray($value)) {

            } elseif (is_string($key)) {
                if ($this->isAttribute($key)) {
                    $key = substr($key, 1);
                    $childNode = $document->createAttribute($key);
                    $attributeValue = $document->createTextNode((string)$data[$value]);
                    $childNode->appendChild($attributeValue);
                } else {
                    $childNode = $document->createElement($key, (string)$data[$value]);
                }
                $result->appendChild($childNode);
            } else {
                $childNode = $document->createTextNode($data[$value]);
                $result->appendChild($childNode);
            }
        }
        return $result;
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