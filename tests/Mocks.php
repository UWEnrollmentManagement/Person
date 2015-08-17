<?php

use UWDOEM\Person\Person;
use UWDOEM\Person\Student;


class MockConnectionInstance {

    public $lastUrl;
    public $lastParams;

    function execGET($url, $params = []) {
        $this->lastUrl = $url;
        $this->lastParams = $params;
        return file_get_contents(getcwd() . "/tests/StaffStudentPerson.json");
    }

    function execPOST($url, $params = []) {
        $this->lastUrl = $url;
        $this->lastParams = $params;
        return file_get_contents(getcwd() . "/tests/StaffStudentPerson.json");
    }
}

$myMockConnectionInstance = new MockConnectionInstance();

trait MockPersonTrait {
    protected static function getConn() {
        global $myMockConnectionInstance;
        return $myMockConnectionInstance;
    }
}

class MockPerson extends Person {
    use MockPersonTrait;

}

class MockStudent extends Student {
    use MockPersonTrait;
}