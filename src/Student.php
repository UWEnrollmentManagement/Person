<?php

namespace UWDOEM\Person;

use UWDOEM\Person\Connection;

/**
 * Container class for person and student-specific information received from Person Web Service and Student Web Service
 *
 * @package UWDOEM\Person
 */
class Student extends Person {

    protected static $AFFILIATION_TYPE = "student";

    protected static function fill(Person $person, array $attrs) {


        $attrs = array_merge(
            $attrs,
            $attrs["PersonAffiliations"]["StudentPersonAffiliation"],
            $attrs["PersonAffiliations"]["StudentPersonAffiliation"]["StudentWhitePages"]);

        return parent::fill($person, $attrs);
    }

    public static function fromStudentNumber($studentNumber) {
        return static::fromIdentifier("student_number", $studentNumber);
    }

}