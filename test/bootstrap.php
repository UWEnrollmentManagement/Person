<?php

require_once dirname(__FILE__) ."/../vendor/uwdoem/connection/test/MockConnection.php";

require_once dirname(__FILE__) ."/../src/Person.php";
require_once dirname(__FILE__) ."/../src/Alumni.php";
require_once dirname(__FILE__) ."/../src/Employee.php";
require_once dirname(__FILE__) ."/../src/Parser.php";
require_once dirname(__FILE__) ."/../src/Student.php";

use UWDOEM\Connection\Test\MockConnection;

use UWDOEM\Person\Person;
use UWDOEM\Person\Student;
use UWDOEM\Person\Employee;
use UWDOEM\Person\Alumni;


trait MockPersonTrait {
    protected static function makeConnection($baseUrl)
    {
        return new MockConnection(
            "http://localhost/",
            getcwd() . "",
            getcwd() . "/test/test-certs/self.signed.test.certs.crt",
            $baseUrl
        );
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