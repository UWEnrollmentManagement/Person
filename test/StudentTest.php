<?php

namespace UWDOEM\Person\Test;

use PHPUnit_Framework_TestCase;

class StudentTest extends PHPUnit_Framework_TestCase
{
    public function testStudentFill()
    {
        $uwnetid = "javerage";

        $p = MockStudent::fromUWNetID($uwnetid);
        $this->assertEquals("1033334", $p->getAttr("StudentNumber"));
        $this->assertEquals("000083856", $p->getAttr("StudentSystemKey"));
        $this->assertEquals("2024", $p->getAttr("Class"));
        $this->assertEquals("Non Matriculated", $p->getAttr("Department1"));
        $this->assertEquals("Average, James A", $p->getAttr("Name"));
        $this->assertEquals("+1 555 555-5555", $p->getAttr("Phone"));
        $this->assertEquals(false, $p->getAttr("PublishInDirectory"));
    }

    public function testFromStudentNumber()
    {
        $p = MockStudent::fromStudentNumber("1033334");
        $this->assertEquals("Non Matriculated", $p->getAttr("Department1"));
    }

    public function testSWSAttributes()
    {
        $p = MockStudent::fromStudentNumber("1033334");
        $this->assertEquals("UW TOWER O-3 BOX 359565", $p->getAttr("LocalAddress")["Line2"]);
    }

    public function testRegistrationSearch()
    {
        $p = MockStudent::fromStudentNumber("1033334");
        $registrations = $p->registrationSearch("2009", "summer");

        $this->assertEquals(1, sizeof($registrations));
        $this->assertEquals("TRAIN", $registrations[0]["CurriculumAbbreviation"]);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessageRegExp #Please provide a numeric year between 1950 and 2100.*#
     */
    public function testRegistrationSearchWithBadYear()
    {
        $p = MockStudent::fromStudentNumber("1033334");
        $registrations = $p->registrationSearch("2101", "summer");
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessageRegExp #Quarter must be one of.*#
     */
    public function testRegistrationSearchWithBadQuarter()
    {
        $p = MockStudent::fromStudentNumber("1033334");
        $registrations = $p->registrationSearch("2000", "not a quarter");
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessageRegExp #Invalid search keys.*#
     */
    public function testRegistrationSearchWithBadExtraSearchTerms()
    {
        $p = MockStudent::fromStudentNumber("1033334");
        $registrations = $p->registrationSearch("2000", "summer", ["not-a-search-key" => "search-value"]);
    }
}
