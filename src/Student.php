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


    /**
     * Perform a registration search on SWS, per https://wiki.cac.washington.edu/display/SWS/Registration+Search+Resource+v5
     *
     * @param string|int $year numeric year between 1950 and 2100 inclusive
     * @param string $quarter one of ["autumn", "winter", "spring", "summer"]
     * @param string[] $extraSearchTerms associative array of search terms ex: ["course_number" => "100"]
     * @return mixed associative array object of registration search results
     * @throws \Exception if provided with invalid year, invalid quarter, or invalid search terms
     */
    public function registrationSearch($year, $quarter, array $extraSearchTerms = []) {

        if (!is_numeric($year) || $year < 1950 || $year > 2100) {
            throw new \Exception("Please provide a numeric year between 1950 and 2100");
        }

        $validQuarters = ["autumn", "winter", "spring", "summer"];
        if (!in_array("$quarter", $validQuarters)) {
            throw new \Exception("Quarter must be one of [" . implode(", $validQuarters") . "], case sensitive.");
        }

        $validSearchKeys = [
            "curriculum_abbreviation", "course_number", "section_id", "instructor_reg_id",
            "is_active", "verbose", "changed_since_date", "transcriptable_course", "grading_system"
        ];
        $invalidSearchKeys = array_diff(array_keys($extraSearchTerms), $validSearchKeys);
        if ($invalidSearchKeys) {
            throw new \Exception("Invalid search keys [" . implode(", ", $invalidSearchKeys) . "]" .
            "provided as extra search terms. Only [" . implode(", ", $validSearchKeys) . "] allowed");
        }

        $defaultSearchTerms = [
            "curriculum_abbreviation"=> "",
            "course_number" => "",
            "section_id" => "",
        ];

        $searchTerms = array_merge($defaultSearchTerms, $extraSearchTerms);
        $searchTerms["reg_id"] = $this->getAttr("UWRegID");

        $resp = static::getStudentConn()->execGET(
            "registration.json?" . http_build_query($searchTerms)
        );

        return static::parse($resp);
    }

    protected static function fromSimpleIdentifier($identifier) {

        $person = parent::fromSimpleIdentifier($identifier);

        $uwregid = $person->getAttr("UWRegID");

        $resp = static::getStudentConn()->execGET(
            "person/$uwregid.json"
        );

        $resp = static::parse($resp);

        $person->attrs = array_merge($person->attrs, $resp);

        return $person;
    }

    protected static function fill(Person $person, array $attrs) {

        $attrs = array_merge(
            $attrs,
            $attrs["PersonAffiliations"]["StudentPersonAffiliation"],
            $attrs["PersonAffiliations"]["StudentPersonAffiliation"]["StudentWhitePages"]);

        $student = parent::fill($person, $attrs);

        return $student;
    }

    public static function fromStudentNumber($studentNumber) {
        return static::fromIdentifier("student_number", $studentNumber);
    }

}