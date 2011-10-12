<?php
/**
* Flickr Helper
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
* @version 1.0
*/

/**
* FlickrHelper class
*
* Flickr Helper class for easy display of Flickr photos
*
* @category Helpers
* @package CakePHP
* @subpackage PHP
* @copyright Copyright (c) 2011 Signified (http://signified.com.au)
* @license http://www.opensource.org/licenses/bsd-license New BSD License
*/
class FlickrHelper extends AppHelper
{
    /**
    * Flickr API Key
    *
    * @var string
    * @access protected
    */
    protected $_apiKey = null;

    /**
    * Flickr Photo Sizes
    *
    * @var array
    * @access protected
    * @link http://www.flickr.com/services/api/misc.urls.html
    */
    protected $_sizes = array(
        's', // small square 75x75
        't', // thumbnail, 100 on longest side
        'm', // small, 240 on longest side
        '-', // medium, 500 on longest side
        'z', // medium 640, 640 on longest side
        'b', // large, 1024 on longest side*
        'o', // original image, either a jpg, gif or png, depending on source format
    );

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
    * Photo
    *
    * @param int $id Flickr photo ID.
    * @param string $size Flickr photo size.
    * @param array $attributes Array of HTML attributes.
    * @return string completed img tag
    * @access public
    * @link http://www.flickr.com/services/api/flickr.photos.getInfo.htm
    */
    public function photo($id = null, $size = null, $options = array())
    {
        if (!$this->_apiKey) {
            return;
        }
        if (!$id || !is_numeric($id)) {
            return;
        }
        if (!$size || !in_array($size, $this->_sizes)) {
            $size = '-';
        }
        $params = array(
            'method' => 'flickr.photos.getInfo',
            'api_key' => $this->_apiKey,
            'photo_id' => $id,
            'format' => 'php_serial',
        );
        $query = http_build_query($params);
        $url = 'http://api.flickr.com/services/rest/?' . $query;
        if ($response = file_get_contents($url)) {
            if ($result = unserialize($response)) {
                if (isset($result['stat']) && $result['stat'] == 'ok') {
                    $farm = $result['photo']['farm'];
                    $originalSecret = $result['photo']['originalsecret'];
                    $originalformat = $result['photo']['originalformat'];
                    $secret = $result['photo']['secret'];
                    $server = $result['photo']['server'];
                    switch ($size) {
                        case '-':
                            $path = "http://farm{$farm}.static.flickr.com/{$server}/{$id}_{$secret}.jpg";
                            break;
                        case 'o':
                            $path = "http://farm{$farm}.static.flickr.com/{$server}/{$id}_{$originalSecret}_o.{$originalformat}";
                            break;
                        default:
                            $path = "http://farm{$farm}.static.flickr.com/{$server}/{$id}_{$secret}_{$size}.jpg";
                    }
                    $options = array_merge(array(
                        'alt' => $result['photo']['title']['_content']
                    ), $options);
                    return $this->Html->image($path, $options);
                }
            }
        }
        return;
    }
}