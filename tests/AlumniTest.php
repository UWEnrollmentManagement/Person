<?php

require_once getcwd() . "/tests/mocks.php";

class AlumniTest extends PHPUnit_Framework_TestCase
{
    public function testAlumniFill()
    {
        $uwnetid = "javerage";

        $p = MockAlumni::fromUWNetID($uwnetid);
        $this->assertEquals($p->getAttr("DevelopmentID"), "123456789");

        global $myMockConnectionInstance;
        $this->assertContains($uwnetid, $myMockConnectionInstance->lastUrl);
    }

}