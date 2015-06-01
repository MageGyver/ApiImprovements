<?php
/**
 * MageGyver/ApiImprovements
 *
 * Avoids duplicate content-type headers due to api requests
 *
 * @category    Api
 * @package     MageGyver
 * @copyright   Copyright (c) 2013 MageGyver. (http://www.magegyver.de)
 * @license     http://creativecommons.org/licenses/by-sa/3.0/  CC-by-sa 3.0
 */

/**
 * Extend the default adapter to avoid sending duplicate content-type headers
 */
class MageGyver_ApiImprovements_Model_Server_Adapter_Soap extends Mage_Api_Model_Server_Adapter_Soap
{

    /**
     * "shortcut" to get the controller's response object
     *
     * @return object
     */
    public function getResponse()
    {
        return $this->getController()->getResponse();
    }



    /**
     * gets the latest defined content type header
     *
     * returns null if no content type header was found
     *
     * @return array
     */
    public function getContentTypeHeader()
    {
        $contentType = null;
        $headers = $this->getResponse()->getHeaders();
        foreach ($headers as $header) {
            if ('content-type' == strtolower($header['name'])) {
                $contentType = $header;
            }
        }
        return $contentType;
    }



    /**
     * consolidates all content type headers and sets optionally replacing mode
     *
     * @param  boolean $replace if not null, this replace mode will be used
     */
    public function consolidateContentTypeHeaders($replace = null)
    {
        if ($header = $this->getContentTypeHeader()) {
            $header['replace'] = is_null($replace)
                ? $header['replace']
                : $replace;
            $this->getResponse()
                ->clearHeader('Content-Type')
                ->setHeader(
                    $header['name'],
                    $header['value'],
                    $header['replace']
                );
        }
    }



    /**
     * make content type headers replacing each other
     */
    public function run()
    {
        parent::run();

        $this->consolidateContentTypeHeaders(true);

        return $this;
    }
}
