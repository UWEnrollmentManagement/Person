<?php

require_once getcwd() . "/tests/mocks.php";

class StudentTest extends PHPUnit_Framework_TestCase
{
    public function testStudentFill()
    {
        $uwnetid = "javerage";

        $p = MockStudent::fromUWNetID($uwnetid);
        $this->assertEquals($p->getAttr("StudentNumber"), "1033334");
        $this->assertEquals($p->getAttr("StudentSystemKey"), "000083856");
        $this->assertEquals($p->getAttr("Class"), "2024");
        $this->assertEquals($p->getAttr("Department1"), "Non Matriculated");
        $this->assertEquals($p->getAttr("Name"), "Average, James A");
        $this->assertEquals($p->getAttr("Phone"), "+1 555 555-5555");
        $this->assertEquals($p->getAttr("PublishInDirectory"), false);

        global $myMockConnectionInstance;
        $this->assertContains($uwnetid, $myMockConnectionInstance->lastUrl);
    }

}