<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GGC_HttpResponseHeader
 *
 * @author Gianni
 */
class GGC_HttpResponseHeader extends GGC_HttpHeader {
    /*
     * Costanti dei possibili nomi di response header.
     */
    const SHN_ACCESS_CONTROL_ALLOW_ORIGIN = 'Access-Control-Allow-Origin';
    const SHN_ACCEPT_RANGE = 'Accept-Ranges';
    const SHN_AGE = 'Age';
    const SHN_ALLOW = 'Allow';
    const SHN_CACHE_CONTROL = 'Cache-Control';
    const SHN_CONNECTION = 'Connection';
    const SHN_CONTENT_ENCODING = 'Content-Encoding';
    const SHN_CONTENT_LANGUAGE = 'Content-Language';
    const SHN_CONTENT_LENGTH = 'Content-Length';
    const SHN_CONTENT_LOCATION = 'Content-Location';
    const SHN_CONTENT_MD5 = 'Content-MD5';
    const SHN_CONTENT_DISPOSITION = 'Content-Disposition';
    const SHN_CONTENT_RANGE = 'Content-Range';
    const SHN_CONTENT_TYPE = 'Content-Type';
    const SHN_DATE = 'Date';
    const SHN_ETAG = 'ETag';
    const SHN_EXPIRES = 'Expires';
    const SHN_LAST_MODIFIED = 'Last-Modified';
    const SHN_LINK = 'Link';
    const SHN_LOCATION = 'Location';
    const SHN_P3P = 'P3P';
    const SHN_PRAGMA = 'Pragma';
    const SHN_PROXY_AUTHENTICATE = 'Proxy-Authenticate';
    const SHN_REFRESH = 'Refresh';
    const SHN_RETRY_AFTER = 'Retry-After';
    const SHN_SERVER = 'Server';
    const SHN_SET_COOKIE = 'Set-Cookie';
    const SHN_STRICT_TRANSPORT_SECURITY = 'Strict-Transport-Security';
    const SHN_TRAILER = 'Trailer';
    const SHN_TRANSFER_ENCODING = 'Transfer-Encoding';
    const SHN_VARY = 'Vary';
    const SHN_VIA = 'Via';
    const SHN_WARNING = 'Warning';
    const SHN_WWW_AUTHENTICATE = 'WWW-Authenticate';
    
    const NSHN_X_FRAME_OPTIONS = 'X-Frame-Options';
    const NSHN_X_XSS_PROTECTION = 'X-XSS-Protection';
    const NSHN_X_CONTENT_TYPE_OPTIONS = 'X-Content-Type-Options';
    const NSHN_X_POWERED_BY = 'X-Powered-By';
    const NSHN_X_UA_COMPATIBLE = 'X-UA-Compatible';
    
    /**
     * Costanti dei possibili codici di response header.
     */
    /*
     * Informational 1xx
     */
    const RC_100_CONTINUE = 100;
    const RC_101_SWITCHING_PROTOCOLS = 101;

    /*
     * Success 2xx
     */
    const RC_200_OK = 200;
    const RC_201_CREATED = 201;
    const RC_202_ACCEPTED = 202;
    const RC_203_NON_AUTHORITATIVE_INFORMATION = 203;
    const RC_204_NO_CONTENT = 204;
    const RC_205_RESET_CONTENT = 205;
    const RC_206_PARTIAL_CONTENT = 206;

    /*
     * Redirection 3xx
     */
    const RC_300_MULTIPLE_CHOICES = 300;
    const RC_301_MOVED_PERMANENTLY = 301;
    const RC_302_FOUND = 302; // 1.1
    const RC_303_SEE_OTHER = 303;
    const RC_304_NOT_MODIFIED = 304;
    const RC_305_USE_PROXY = 305;
    // 306 is deprecated but reserved
    const RC_307_TEMPORARY_REDIRECT = 307;

    /*
     * Client Error 4xx
     */
    const RC_400_BAD§_REQUEST = 400;
    const RC_401_UNAUTHORIZED = 401;
    const RC_402_PAYMENT_REQUIRED = 402;
    const RC_403_FORBIDEN = 403;
    const RC_404_NOT_FOUND = 404;
    const RC_405_METHOD_NOT_ALLOWD = 405;
    const RC_406_NOT_ACCEPTABLE = 406;
    const RC_407_PROXY_AUTHENTICATION_REQUIRED = 407;
    const RC_408_REQUEST_TIMEOUT = 408;
    const RC_409_CONFLICT = 409;
    const RC_410_GONE = 410;
    const RC_411_LENGTH_REQUIRED = 411;
    const RC_412_PRECONDITION_FAILED = 412;
    const RC_413_REQUEST_ENTITY_TOO_LARGE = 413;
    const RC_414_REQUEST_URI_TOO_LONG = 414;
    const RC_415_UNSUPPORTED_MEDIA_TYPE = 415;
    const RC_416_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const RC_417_EXPECTATION_FAILDE = 417;

    /*
     * Server Error 5xx
     */
    const RC_500_INTERNAL_SERVER_ERROR = 500;
    const RC_501_NOT_IMPLEMENTED = 501;
    const RC_502_BAD_GATEWAY = 502;
    const RC_503_SERVICE_UNAVAILABLE = 503;
    const RC_504_GATEWAY_TIMEOUT = 504;
    const RC_505_HTTP_VERSION_NOT_SUPPORTED = 505;
    const RC_509_BANDWIDTH_LIMIT_EXCEEDED = 509;
    
    /*
     * Stringhe associate ai codici di risposta
     */
    protected static $codeMessages = array(
        /*
         * Informational 1xx
         */
        100 => 'Continue',
        101 => 'Switching Protocols',

        /*
         * Success 2xx
         */
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',

        /*
         * Redirection 3xx
         */
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',  // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',

        /*
         * Client Error 4xx
         */
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',

        /*
         * Server Error 5xx
         */
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    );
    
    protected $code = NULL;
    protected $replace = true;

    /**
     * TODO :
     * Effettuare il controllo sui possibili nomi di response header,
     * e sui possibili codici.
     * 
     * @param type $name
     * @param type $value
     */
    function __construct($name = NULL, $value = NULL, $replace = true, $code = NULL) {
        parent::__construct($name, $value);
        
        $this->replace = $replace;
        $this->code = $code;
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
    
    function getReplace() {
        return $this->replace;
    }
    
    function setReplace($value) {
        $this->replace = (bool)$value;
    }
    
    function getCode() {
        return $this->code;
    }
    
    /**
     * TODO :
     * Effettuare il controllo dui possibili codici di response.
     * 
     * @param type $value
     */
    function setCode($value) {
        $this->code = $value;
    }
    
    function getCodeMessage($code) {
        $result = NULL;
        
        if (array_key_exists($code, self::$codeMessages)) {
            $result = self::$codeMessages[$code];
        }
            
        return $result;    
    }
    
    /**
     * TODO :
     * Effettuare il controllo sui possibili nomi di response header e
     * sul codice.
     * 
     * @param type $name
     * @param type $value
     */
    function set($name, $value, $replace = true, $code = NULL) {
        $this->name = $name;
        $this->value = $value;
        $this->replace = $replace;
        $this->code = $code;
    }
    
    function send() {
        header($this->name . ': ' . $this->value, $this->replace, $this->code);
    }
    
    /**
     * TODO :
     * Decidere cosa fare in caso di codice non presente.
     */
    function sendCode() {
        if (!empty($this->code)) {
            header($this->getCodeMessage($this->getCode()), true, $this->code);
            
        } else {
            
        }
    }
}

?>
<