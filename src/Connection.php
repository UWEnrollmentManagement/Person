<?php

namespace UWDOEM\Person;


/**
 * Class Connection
 *
 * Singleton class representing our connections to Person Web Service and Student Web Service
 *
 * @package UWDOEM\Person
 */
class Connection {
    private static $personInstance;
    private static $studentInstance;

    private $baseurl;
    private $curl;

    /**
     * @return Connection Curl connection container.
     * @throws \Exception if ::getInstance is called before connection is intialized via ::createInstance
     */
    public static function getPersonInstance() {
        if (empty(self::$personInstance)) {
            throw new \Exception(
                'Connection::getInstance() called before initialization. ' .
                'Call Connection::createInstance($sslkey, $sslcert, $sslkeypasswd) before ::getInstance().'
            );
        }
        return self::$personInstance;
    }

    /**
     * @return Connection Curl connection container.
     * @throws \Exception if ::getInstance is called before connection is intialized via ::createInstance
     */
    public static function getStudentInstance() {
        if (empty(self::$studentInstance)) {
            throw new \Exception(
                'Connection::getInstance() called before initialization. ' .
                'Call Connection::createInstance($sslkey, $sslcert, $sslkeypasswd) before ::getInstance().'
            );
        }
        return self::$studentInstance;
    }

    /**
     * @param string $sslkey Absolute path to the private SSL key used to authenticate your app to PWS or SWS.
     * @param string $sslcert Absolute path to the certificate file used to authenticate your app to PWS or SWS.
     * @param string|null $sslkeypasswd (Optional) Password for your private key file.
     * @throws \Exception if you attempt to intialize the connection more than one time in a page-load via ::createInstance
     */
    public static function createInstance($baseurl, $sslkey, $sslcert, $sslkeypasswd = null) {
        if (!empty(self::$personInstance)) {
            throw new \Exception(
                'Connection::createInstance() called more than once. ' .
                'Only one connection may be created. '
            );
        }

        self::$personInstance = new Connection($baseurl . "identity/v1/", $sslkey, $sslcert, $sslkeypasswd);
        self::$studentInstance = new Connection($baseurl . "person/v5/", $sslkey, $sslcert, $sslkeypasswd);
    }

    private function __construct($baseurl, $sslkey, $sslcert, $sslkeypasswd = null) {

        $this->baseurl = $baseurl;

        if (!file_exists($sslkey)) {
            throw new \Exception("No such file found for SSL key at $sslkey.");
        }

        if (!file_exists($sslcert)) {
            throw new \Exception("No such file found for SSL certificate at $sslcert.");
        }

        // Get cURL resource
        $this->curl = curl_init();

        // Set cURL parameters
        curl_setopt_array($this->curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSLKEY => $sslkey,
            CURLOPT_SSLCERT => $sslcert,
        ));

        if (!is_null($sslkeypasswd)) {
            curl_setopt_array($this->curl, array(
                CURLOPT_SSLKEYPASSWD => $sslkeypasswd,
            ));
        }
    }

    function __destruct() {
        curl_close($this->curl);
    }

    /**
     * Execute a GET request to a given URL, with optional parameters.
     *
     * @param string $url
     * @param string[] $params Array of query parameter $key=>$value pairs
     * @return mixed The server's response
     */
    public function execGET($url, $params = []) {
        $url = $this->baseurl . $url;

        // Build the query from the parameters
        if ($params) {
            $url .= '?' . http_build_query($params);
        }

        // Set request options
        curl_setopt_array($this->curl, array(
            CURLOPT_URL => $url,
        ));

        return $this->exec();
    }

    /**
     * Execute a POST request to a given URL, with optional parameters.
     *
     * @param string $url
     * @param string[] $params Array of POST parameter $key=>$value pairs
     * @return mixed The server's response
     */
    public function execPOST($url, $params = []) {
        $url = $this->baseurl . $url;

        // Set request options
        curl_setopt_array($this->curl, array(
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params,
        ));

        // Execute the request
        $resp = $this->exec();

        // Unset POST related options
        curl_setopt_array($this->curl, array(
            CURLOPT_POST => false,
        ));

        return $resp;
    }

    protected function exec() {
        $resp = curl_exec($this->curl);

        if(curl_errno($this->curl)){
            throw new \Exception('Request Error:' . curl_error($this->curl));
        }

        return $resp;
    }
}