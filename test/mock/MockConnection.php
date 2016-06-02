<?php

namespace UWDOEM\Person\Test;

class MockConnection extends \UWDOEM\Connection\Test\MockConnection
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
}
