<?php
/**
* FlickrHelper class
*
* Flickr Helper class for easy display of Flickr photos
*
* LICENSE
*
* This source file is subject to the new BSD license that is bundled with this
* package in the file LICENSE. It is also available through the world-wide-web
* at this URL: http://www.opensource.org/licenses/bsd-license
*
* @category Helpers
* @package CakePHP
* @subpackage PHP
* @copyright Copyright (c) 2011 Signified (http://signified.com.au)
* @license http://www.opensource.org/licenses/bsd-license New BSD License
* @version 1.1
*/

App::import('Vendor', 'Flickr', ['file' => 'Flickr.php']);

class FlickrHelper extends AppHelper
{
    /**
    * Helpers used by FlickrHelper
    *
    * @var array
    * @access public
    */
    public $helpers = array(
        'Html'
    );

    /**
    * Html Image Tag
    * @param int $id Flickr photo ID.
    * @param string $size Flickr photo size.
    * @param array $attributes Array of HTML attributes.
    * @return string completed img tag
    * @access public
    */
    public function photo($id = null, $size = null, $options = array())
    {
        $flickr = new Flickr();
        $url = $flickr->url($id, $size);

        $options = array_merge(array(
            'alt' => $flickr->response['photo']['title']['_content'],
            'title' => $flickr->response['photo']['description']['_content']
        ), $options);

        return $this->Html->image($url, $options);
    }

    /**
    * Flickr farm url
    * @param int $id Flickr photo ID.
    * @param string $size Flickr photo size.
    * @return string static flickr farm url
    * @access public
    */
    public function url($id = null, $size = null)
    {
        $flickr = new Flickr();
        return $flickr->url($id, $size);
    }
}

?>
