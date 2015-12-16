<?php

namespace UWDOEM\Person\Test;

trait MockPersonTrait
{
    protected static function makeConnection($baseUrl)
    {
        return new \UWDOEM\Connection\Test\MockConnection(
            "http://localhost/",
            getcwd() . "",
            getcwd() . "/test/test-certs/self.signed.test.certs.crt",
            $baseUrl
        );
    }
}
