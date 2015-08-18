<?php

use UWDOEM\Person\Person;
use UWDOEM\Person\Student;
use UWDOEM\Person\Employee;
use UWDOEM\Person\Alumni;


class MockConnectionInstance {

    public $lastUrl;
    public $lastParams;

    protected function makeSlug($url) {
        $url = str_replace(["https://ws.admin.washington.edu/identity/v1/"], [""], $url);
        $url = str_replace(["?", "/", ".", "="], ["-", "-", "-", "-"], $url);

        return $url;
    }

    public function execGET($url, $params = []) {
        $this->lastUrl = $url;
        $this->lastParams = $params;

        return file_get_contents("tests/responses/{$this->makeSlug($url)}.json");
    }

    public function execPOST($url, $params = []) {
        $this->lastUrl = $url;
        $this->lastParams = $params;
        return file_get_contents("tests/StaffStudentPerson.json");
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

class MockEmployee extends Employee {
    use MockPersonTrait;
}

class MockAlumni extends Alumni {
    use MockPersonTrait;
}