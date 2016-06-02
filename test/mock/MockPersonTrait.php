<?php

namespace UWDOEM\Person\Test;

trait MockPersonTrait
{
    protected static function makeConnection($baseUrl)
    {
        return new MockConnection(
            $baseUrl,
            getcwd() . "",
            getcwd() . "/test/test-certs/self.signed.test.certs.crt",
            $baseUrl
        );
    }
}
