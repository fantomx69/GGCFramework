<?php
/**
 * Description of GGC_HttpRequestHeader
 *
 * @author Gianni
 */
class GGC_HttpRequestHeader extends GGC_HttpHeader {
    /*
     * Costanti dei possibili nomi di request header.
     */
    const SHN_ACCEPT = 'Accept';
    const SHN_ACCEPT_CHARSET = 'Accept-Charset';
    const SHN_ACCEPT_ENCODING = 'Accept-Encoding';
    const SHN_ACCEPT_LANGUAGE = 'Accept-Language';
    const SHN_ACCEPT_DATETIME = 'Accept-Datetime';
    const SHN_AUTHORIZATION = 'Authorization';
    const SHN_CACHE_CONTROL = 'Cache-Control';
    const SHN_CONNECTION = 'Connection';
    const SHN_COOKIE = 'Cookie';
    const SHN_CONTENT_LENGTH = 'Content-Length';
    const SHN_CONTENT_MD5 = 'Content-MD5';
    const SHN_CONTENT_TYPE = 'Content-Type';
    const SHN_DATE = 'Date';
    const SHN_EXPECT = 'Expect';
    const SHN_FROM = 'From';
    const SHN_HOST = 'Host';
    const SHN_IF_MATCH = 'If-Match';
    const SHN_IF_MODIFIED_SINCE = 'If-Modified-Since';
    const SHN_IF_NONE_MATCH = 'If-None-Match';
    const SHN_IF_RANGE = 'If-Range';
    const SHN_IF_UNMODIFIED_SINCE = 'If-Unmodified-Since';
    const SHN_MAX_FORWARDS = 'Max-Forwards';
    const SHN_PRAGMA = 'Pragma';
    const SHN_PROXY_AUTHORIZATION = 'Proxy-Authorization';
    const SHN_RANGE = 'Range'; 
    const SHN_REFERER = 'Referer';
    const SHN_TE = 'TE';
    const SHN_UPGRADE = 'Upgrade';
    const SHN_USER_AGENT = 'User-Agent';
    const SHN_VIA = 'Via';
    const SHN_WARNING = 'Warning';
    
    const NSHN_X_REQUESTED_WITH = 'X-Requested-With';
    const NSHN_DNT = 'DNT';
    const NSHN_X_FORWARDED_FOR ='X-Forwarded-For';
    const NSHN_X_FORWARDED_PROTO = 'X-Forwarded-Proto';
    const NSHN_FRONT_END_HTTPS = 'Front-End-Https';
    const NSHN_X_ATT_DEVICEID = 'X-ATT-DeviceId';
    const NSHN_X_WAP_PROFILE = 'X-Wap-Profile';
    const NSHN_PROXY_CONNECTION = 'Proxy-Connection';
    
    /**
     * TODO :
     * Effettuare il controllo sui possibili nomi di request header.
     * 
     * @param type $name
     * @param type $value
     */
    function __construct($name, $value) {
        parent::__construct($name, $value);
    }
    
    /**
     * TODO :
     * Effettuare il controllo sui possibili nomi di request header.
     * 
     * @param type $value
     */
    function setName($value) {
        $this->name = $value;
    }
    
    function setValue($value) {
        $this->value = $value;
    }
    
    /**
     * TODO :
     * Effettuare il controllo sui possibili nomi di request header.
     * 
     * @param type $name
     * @param type $value
     */
    function set($name, $value) {
        $this->name = $name;
        $this->value = $value;
    }
}

?>
