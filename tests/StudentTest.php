<?php


class StudentTest extends PHPUnit_Framework_TestCase
{
    public function testStudentFill() {
        $uwnetid = "javerage";

        $p = MockStudent::fromUWNetID($uwnetid);
        $this->assertEquals("1033334", $p->getAttr("StudentNumber"));
        $this->assertEquals("000083856", $p->getAttr("StudentSystemKey"));
        $this->assertEquals("2024", $p->getAttr("Class"));
        $this->assertEquals("Non Matriculated", $p->getAttr("Department1"));
        $this->assertEquals("Average, James A", $p->getAttr("Name"));
        $this->assertEquals("+1 555 555-5555", $p->getAttr("Phone"));
        $this->assertEquals(false, $p->getAttr("PublishInDirectory"));

        global $myMockConnectionInstance;
        $this->assertContains($uwnetid, $myMockConnectionInstance->lastUrl);
    }

    public function testFromStudentNumber() {
        $p = MockStudent::fromStudentNumber("1033334");
        $this->assertEquals("Non Matriculated", $p->getAttr("Department1"));
    }

}