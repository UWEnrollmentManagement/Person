<?php

namespace UWDOEM\Person\Test;

use UWDOEM\Connection\Connection;

class MockConnection extends Connection
{
    protected function makeSlug($url)
    {
        $baseURLReplacement = "";
        if (strpos($this->baseUrl, "identity") !== false) {
            $baseURLReplacement = "i-";
        } elseif (strpos($this->baseUrl, "student") !== false) {
            $baseURLReplacement = "s-";
        }
        $url = str_replace([$this->baseUrl], [$baseURLReplacement], $url);
        $url = str_replace(["?", "&", "/", ".", "="], ["-q-", "-and-", "-", "-", "-"], $url);

        if (strlen($url) > 63) {
            $url = md5($url);
        }

        return $url;
    }

    protected function doExec()
    {
        $url = curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL);
        $info = curl_getinfo($this->curl);
        $data = file_get_contents(getcwd() . "/responses/{$this->makeSlug($url)}");
        return new \UWDOEM\Connection\ConnectionReturn($data, $info);
    }
}
