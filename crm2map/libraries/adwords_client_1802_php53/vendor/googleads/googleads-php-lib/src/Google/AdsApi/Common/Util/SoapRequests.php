<?php
/**
 * Copyright 2016 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Google\AdsApi\Common\Util;

use DOMDocument;
use DOMException;
use DOMXPath;

/**
 * Static utility methods for working with SOAP requests.
 */
final class SoapRequests
{

    /**
     * Iterates through all nodes with an 'href' attribute in the specified
     * request SOAP XML string and replaces them with the node they are
     * referencing.
     *
     * If replacement fails in any way, return the original request so any
     * issues bubble up.
     *
     * @param string $request
     * @return string the request SOAP XML string with references replaced
     */
    public static function replaceReferences($request)
    {
        $requestDom = new DOMDocument();
        try {
            set_error_handler(array(__CLASS__ , 'handleLoadXmlWarnings'));
            $requestDom->loadXML($request);
        } catch (DOMException $e) {
            return $request;
 /*       } finally {
            restore_error_handler();
*/        }

        $xpath = new DOMXPath($requestDom);
        $references = $xpath->query('//*[@href]');
        // Cache of referenced element IDs to their nodes.
        $referencedElementsCache = array();

        // Replace each reference.
        foreach ($references as $reference) {
            // References begin with a hash, e,g., #ref1, remove the hash sign.
            $id = substr($reference->getAttribute('href'), 1);

            // If we haven't seen this referenced node before we need to find it and
            // cache it.
            if (!array_key_exists($id, $referencedElementsCache)) {
                $referencedElements = $xpath->query(sprintf("//*[@id='%s']", $id));
                // There may be more than one element with the same ID if we replaced
                // a reference to an element that has a child node that something else
                // is referencing.
                if ($referencedElements->length > 0) {
                    $referencedElementsCache[$id] = $referencedElements->item(0);
                } else {
                    // If for some reason we can't find it, it's probably a malformed SOAP
                    // request XML and we skip this reference so the issue bubbles up.
                    continue;
                }
            }

            // Deep copy all the nodes from the referenced element.
            foreach ($referencedElementsCache[$id]->childNodes as $childNode) {
                $reference->appendChild($childNode->cloneNode(true));
            }
            $reference->removeAttribute('href');
        }

        // Once all references are replaced, remove the obsolete ID tags from each
        // referenced element.
        foreach (array_keys($referencedElementsCache) as $id) {
            // For the same reason stated above, there may be more than one element
            // with the same ID due to replacement.
            $referencedElements = $xpath->query(sprintf("//*[@id='%s']", $id));
            foreach ($referencedElements as $referencedElement) {
                $referencedElement->removeAttribute('id');
            }
        }

        return $requestDom->saveXML();
    }

    /**
     * Translates warnings generated by `DOMDocument::loadXML` into a
     * DOMException.
     *
     * @see http://php.net/manual/en/function.set-error-handler.php#refsect1-function.set-error-handler-parameters
     * @throws DOMException if `DOMDocument::loadXML` generated an `E_WARNING`
     */
    public static function handleLoadXmlWarnings($errno, $errstr)
    {
        switch ($errno) {
            case E_WARNING:
                if (strpos($errstr, 'DOMDocument::loadXML()') !== false) {
                    throw new DOMException($errstr);
                }
                break;
            default:
                return false;
        }
    }
}
