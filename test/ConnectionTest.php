<?php

use UWDOEM\Person\Connection;

class MockConnection extends Connection {

    public $curl;

    public function getCurl() {
        return $this->curl;
    }
    
}

class ConnectionTest extends PHPUnit_Framework_TestCase
{

    /**
     * @expectedException              \Exception
     * @expectedExceptionMessageRegExp #called before initialization.*#
     */
    public function testErrorGetPersonInstanceBeforeCreate() {
        MockConnection::getPersonInstance();
    }

    /**
     * @expectedException              \Exception
     * @expectedExceptionMessageRegExp #called before initialization.*#
     */
    public function testErrorGetStudentInstanceBeforeCreate() {
        MockConnection::getStudentInstance();
    }

    /**
     * @expectedException              \Exception
     * @expectedExceptionMessageRegExp #No such file found for SSL key at.*#
     */
    public function testErrorNoSuchSSLKey() {
        MockConnection::createInstance(
            "http://localhost/",
            getcwd() . "/" . (string)rand() . ".key",
            getcwd() . "/test/test-certs/self.signed.test.certs.crt",
            "self-signed-password"
        );
    }

    /**
     * @expectedException              \Exception
     * @expectedExceptionMessageRegExp #No such file found for SSL certificate at.*#
     */
    public function testErrorNoSuchSSLCert() {
        MockConnection::createInstance(
            "http://localhost/",
            getcwd() . "/test/test-certs/self.signed.test.certs.crt",
            getcwd() . "/" . (string)rand() . ".crt",
            "self-signed-password"
        );
    }

    public function testCreateInstance() {

        MockConnection::createInstance(
            "http://localhost/",
            getcwd() . "/test/test-certs/self.signed.test.certs.key",
            getcwd() . "/test/test-certs/self.signed.test.certs.crt",
            "self-signed-password"
        );

        $personInstance = MockConnection::getPersonInstance();
        $studentInstance = MockConnection::getStudentInstance();

        $this->assertTrue($personInstance instanceof Connection);
        $this->assertTrue($studentInstance instanceof Connection);
    }

    /**
     * @expectedException              \Exception
     * @expectedExceptionMessageRegExp #Only one connection may be created.*#
     */
    public function testErrorCreateInstanceTwice() {
        MockConnection::createInstance(
            "http://localhost/",
            getcwd() . "/test/test-certs/self.signed.test.certs.key",
            getcwd() . "/test/test-certs/self.signed.test.certs.crt",
            "self-signed-password"
        );
    }
}