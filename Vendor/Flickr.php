<?php
/**
* Flickr Library
*
* LICENSE
*
* This source file is subject to the new BSD license that is bundled with this
* package in the file LICENSE. It is also available through the world-wide-web
* at this URL: http://www.opensource.org/licenses/bsd-license
*
* @category Vendor
* @package CakePHP
* @subpackage PHP
* @copyright Copyright (c) 2011 Signified (http://signified.com.au)
* @license http://www.opensource.org/licenses/bsd-license New BSD License
* @version 1.1
*/

class Flickr
{
    /**
    * Flickr Response Array
    *
    * @var array
    * @access public
    */
    public $response;

    /**
    * Flickr REST URL
    *
    * @var string
    * @access private
    */
    private $_restUrl = 'https://api.flickr.com/services/rest';

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
        'q', // large sqaure, 150x150
        'm', // small, 240 on longest side
        '-', // medium, 500 on longest side
        'z', // medium 640, 640 on longest side
        'b', // large, 1024 on longest side*
        'o', // original image, either a jpg, gif or png, depending on source format
    );

    public function __construct() {}

    /**
    * Get Result
    *
    * @param int $id Flickr photo ID.
    * @return mixed Array of results on success, boolean false on failure
    * @access protected
    */
    protected function _getResult($id = null)
    {
        $params = array(
            'method' => 'flickr.photos.getInfo',
            'api_key' => $this->_apiKey,
            'photo_id' => $id,
            'format' => 'php_serial',
        );
        $query = http_build_query($params);
        $url = $this->_restUrl . '?' . $query;
        $cacheKey = md5($url);
        if (!Configure::read('Cache.disable') && Configure::read('Cache.check') && ($this->response = Cache::read($cacheKey))) {
            return $this->response;
        } else {
            if ($result = file_get_contents($url)) {
                if($this->response = unserialize($result)) {
                    if (isset($this->response['stat']) && $this->response['stat'] == 'ok') {
                        if (!Configure::read('Cache.disable') && Configure::read('Cache.check')) {
                            Cache::write($cacheKey, $this->response);
                        }
                        return $this->response;
                    }
                }
            }
        }
        return false;
    }

    /**
    * Flickr Url
    *
    * @param int $id Flickr photo ID.
    * @param string $size Flickr photo size.
    * @return string complete static flickr farm url
    * @access public
    * @link http://www.flickr.com/services/api/flickr.photos.getInfo.htm
    */
    public function url($id = null, $size = null)
    {
        if (!$this->_apiKey) {
            return false;
        }
        if (!$id || !is_numeric($id)) {
            return false;
        }
        if (!$size || !in_array($size, $this->_sizes)) {
            $size = '-';
        }
        if ($result = $this->_getResult($id)) {
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
            return $path;
        }
        return;
    }
}

?>
