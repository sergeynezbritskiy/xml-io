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
    private function appendChild(DOMDocument $document, DOMNode $parentNode, string $nodeName, $data, $map)
    {
        $node = $document->createElement($nodeName);
        if (isset($map['data'])) {
            $text = $data[$map['data']];
            $textNode = $document->createTextNode($text);
            $node->appendChild($textNode);
        }
        $parentNode->appendChild($node);
    }

}