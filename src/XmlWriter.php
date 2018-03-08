<?php declare(strict_types=1);

namespace SergeyNezbritskiy\XmlIo;

use DomDocument;
use DOMElement;
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
        $document->appendChild($this->createNode($rootElement, $array, $map));
        return $document->saveXML();
    }

    private function createNode($nodeName, array $data, array $map): DOMNode
    {
        $result = new DOMElement($nodeName);
        return $result;
    }

}