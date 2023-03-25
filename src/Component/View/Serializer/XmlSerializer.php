<?php

namespace Fraction\Component\View\Serializer;

use SimpleXMLElement;

class XmlSerializer extends AbstractSerializer {
  public function serialize($data): string {
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><data></data>');
    array_walk_recursive($data, [$xml, 'addChild']);
    return $xml->asXML();
  }
}
