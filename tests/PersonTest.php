<?php

require_once getcwd() . "/tests/mocks.php";

class PersonTest extends PHPUnit_Framework_TestCase
{
    public function testSetGetAttr() {
        $p = new MockPerson();

        $p->setAttr("key1", "value1");
        $p->setAttr("key2", "value2");

        $this->assertEquals($p->getAttr("key1"), "value1");
        $this->assertEquals($p->getAttr("key2"), "value2");
    }

    public function testFromUWNetID() {
        $uwnetid = "javerage";

        $p = MockPerson::fromUWNetID($uwnetid);
        $this->assertEquals($p->getAttr("DisplayName"), "James Average Student");

        global $myMockConnectionInstance;
        $this->assertContains($uwnetid, $myMockConnectionInstance->lastUrl);
    }

    public function testFromUWRegID() {
        $uwregid = "9136CCB8F66711D5BE060004AC494FFE";

        $p = MockPerson::fromUWNetID($uwregid);
        $this->assertEquals($p->getAttr("DisplayName"), "James Average Student");

        global $myMockConnectionInstance;
        $this->assertContains($uwregid, $myMockConnectionInstance->lastUrl);
    }

    public function testHasAffiliation() {
        $uwnetid = "javerage";
        $p = MockPerson::fromUWNetID($uwnetid);

        $this->assertTrue($p->hasAffiliation("member"));
        $this->assertTrue($p->hasAffiliation("student"));
        $this->assertTrue($p->hasAffiliation("alum"));
        $this->assertTrue($p->hasAffiliation("staff"));
        $this->assertTrue($p->hasAffiliation("employee"));

        $this->assertFalse($p->hasAffiliation("sdfasdfjkl;sdfa"));
    }

    public function testClassCasting() {
        $uwnetid = "javerage";

        $p = MockPerson::fromUWNetID($uwnetid);
        $s = MockStudent::fromPerson($p);

        $this->assertEquals($s->getAttr("StudentNumber"), "1033334");
    }

    public function testIdentifierSearch() {
        $p = MockPerson::fromIdentifier("employeeid", "123456789");

        $this->assertEquals($p->getAttr("DisplayName"), "James Average Student");
    }

}
