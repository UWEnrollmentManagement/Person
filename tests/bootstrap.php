<?php

require_once dirname(__FILE__) ."/../src/Person.php";
require_once dirname(__FILE__) ."/../src/Alumni.php";
require_once dirname(__FILE__) ."/../src/Connection.php";
require_once dirname(__FILE__) ."/../src/Employee.php";
require_once dirname(__FILE__) ."/../src/Parser.php";
require_once dirname(__FILE__) ."/../src/Student.php";


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

        return file_get_contents(getcwd() . "/responses/{$this->makeSlug($url)}.json");
    }

    public function execPOST($url, $params = []) {
        $this->lastUrl = $url;
        $this->lastParams = $params;
        return file_get_contents(getcwd() . "/StaffStudentPerson.json");
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