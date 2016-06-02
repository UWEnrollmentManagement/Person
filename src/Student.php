<?php

namespace UWDOEM\Person;

/**
 * Container class for person and student-specific information received from Person Web
 * Service and Student Web Service
 *
 * @method static null|Student fromUWNetID() fromUWNetID(string $uwnetid)
 *                             Queries PWS/SWS to generate a Student, given a UWNetID.
 * @method static null|Student fromUWRegID() fromUWRegID(string $uwregid)
 *                             Queries PWS/SWS to generate a Student, given a UWRegID.
 *
 * @package UWDOEM\Person
 */
class Student extends Person
{

    /** @var string */
    protected static $AFFILIATION_TYPE = "student";

    /**
     * Perform a registration search on SWS, per
     *     https://wiki.cac.washington.edu/display/SWS/Registration+Search+Resource+v5
     *
     * @param integer  $year             Numeric year between 1950 and 2100 inclusive.
     * @param string   $quarter          One of ["autumn", "winter", "spring", "summer"].
     * @param string[] $extraSearchTerms Associative array of search terms ex: ["course_number" => "100"].
     * @return mixed Associative array object of registration search results
     * @throws \Exception If provided with invalid year, invalid quarter, or invalid search terms.
     */
    public function registrationSearch($year, $quarter, array $extraSearchTerms = [])
    {

        if (is_numeric($year) === false || $year < 1950 || $year > 2100) {
            throw new \Exception("Please provide a numeric year between 1950 and 2100");
        }

        $validQuarters = ["autumn", "winter", "spring", "summer"];
        if (in_array("$quarter", $validQuarters) === false) {
            throw new \Exception("Quarter must be one of [" . implode(", ", $validQuarters) . "], case sensitive.");
        }

        $validSearchKeys = [
            "curriculum_abbreviation", "course_number", "section_id", "instructor_reg_id",
            "is_active", "verbose", "changed_since_date", "transcriptable_course", "grading_system"
        ];
        $invalidSearchKeys = array_diff(array_keys($extraSearchTerms), $validSearchKeys);
        if ($invalidSearchKeys !== []) {
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
        $searchTerms["year"] = $year;
        $searchTerms["quarter"] = $quarter;

        $resp = static::getStudentConnection()->execGET(
            "registration.json?" . http_build_query($searchTerms)
        );

        return static::parse($resp)["Registrations"];
    }

    /**
     * @param string $identifierKey
     * @param string $identifierValue
     * @return null|Student
     * @throws \Exception If $identifierKey is not one of ["uwregid", "uwnetid", "employee_id",
     *                                                     "student_number", "student_system_key"].
     */
    public static function fromIdentifier($identifierKey, $identifierValue)
    {
        /** @var null|Person $student */
        $student = parent::fromIdentifier($identifierKey, $identifierValue);

        /** @var string[] $personKeyToStudentKey */
        $personKeyToStudentKey = [
            "uwregid" => "reg_id",
            "uwnetid" => "net_id",
            "student_number" => "student_number",
            "employee_id" => "employee_id",
            "student_system_key" => "student_system_key",
        ];

        /** @var string[] $validIdentifierKeys */
        $validIdentifierKeys = array_keys($personKeyToStudentKey);

        /** @var boolean $identifierKeyIsValid */
        $identifierKeyIsValid = in_array($identifierKey, $validIdentifierKeys);

        if ($identifierKeyIsValid === false) {
            throw new \Exception(
                "Identifier key '$identifierKey' must be one of [" . implode(", ", $validIdentifierKeys) . "]."
            );
        }

        $identifierKey = $personKeyToStudentKey[$identifierKey];

        $resp = static::getStudentConnection()->execGET(
            "person.json?$identifierKey=$identifierValue"
        );
        $resp = static::parse($resp);

        if (array_key_exists("Persons", $resp) === true && sizeof($resp["Persons"]) > 0) {
            if ($student === null) {
                $student = new Student();
            }
            $student->attrs = array_merge($student->attrs, $resp["Persons"][0]);
        }

        return $student;
    }

    /**
     * @param Person $person
     * @param array  $attrs
     * @return Student
     */
    protected static function fill(Person $person, array $attrs)
    {
        if (array_key_exists('StudentPersonAffiliation', $attrs["PersonAffiliations"]) === true) {
            $attrs = array_merge(
                $attrs,
                $attrs["PersonAffiliations"]["StudentPersonAffiliation"],
                $attrs["PersonAffiliations"]["StudentPersonAffiliation"]["StudentWhitePages"]
            );
        }

        $student = parent::fill($person, $attrs);

        return $student;
    }

    /**
     * @param string $studentNumber
     * @return null|Student
     */
    public static function fromStudentNumber($studentNumber)
    {
        return static::fromIdentifier("student_number", $studentNumber);
    }
}
