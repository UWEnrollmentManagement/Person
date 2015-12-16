<?php


class AlumniTest extends PHPUnit_Framework_TestCase
{
    public function testAlumniFill() {
        $uwnetid = "javerage";

        $p = MockAlumni::fromUWNetID($uwnetid);
        $this->assertEquals($p->getAttr("DevelopmentID"), "123456789");
    }

    public function testFromDevelopmentID() {
        $p = MockAlumni::fromDevelopmentID("123456789");

        $this->assertEquals($p->getAttr("DevelopmentID"), "123456789");
    }

}