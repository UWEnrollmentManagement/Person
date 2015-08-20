<?php

namespace UWDOEM\Person;

/**
 * Container class for data received from Person Web Service and Student Web Service
 *
 * @package UWDOEM\Person
 */
class Person {

    protected $attrs = [];
    protected $_raw = [];
    protected $_affiliations = [];

    protected static $AFFILIATION_TYPE = "person";

    /**
     * Sets a value on your LOCAL COPY of the person.
     *
     * SWS/PWS do not support UPDATING person/student models.
     * @param $key
     * @param $value
     */
    public function setAttr($key, $value) {
        $this->attrs[$key] = $value;
    }

    /**
     * Gets a value from your local copy of the person.
     *
     * @param $key
     * @return mixed
     */
    public function getAttr($key) {
        return $this->attrs[$key];
    }

    /**
     * Returns whether the given person has the given affiliation.
     *
     * @param $affiliation
     * @return bool
     */
    public function hasAffiliation($affiliation) {
        return in_array($affiliation, $this->_affiliations);
    }

    /**
     * Returns an arry of the affiliate's attributes.
     *
     * @return array
     */
    public function getAttrs() {
        return $this->attrs;
    }

    /**
     * Queries PWS/SWS to generate a Person, given a UWNetID.
     * @param $uwnetid
     * @return null|Person
     */
    public static function fromUWNetID($uwnetid) {
        return static::fromSimpleIdentifier($uwnetid);
    }

    /**
     * Queries PWS/SWS to generate a Person, given a UWRegID.
     *
     * @param $regid
     * @return null|Person
     */
    public static function fromUWRegID($uwregid) {
        return static::fromSimpleIdentifier($uwregid);
    }

    protected static function fromSimpleIdentifier($identifier) {
        $resp = static::getConn()->execGET(
            "https://ws.admin.washington.edu/identity/v1/person/$identifier/full.json"
        );
        $resp = static::parse($resp);

        if (array_key_exists("StatusCode", $resp) && $resp["StatusCode"] == "404") {
            return null;
        } else {
            $person = new static();
            return static::fill($person,  $resp);
        }
    }

    /**
     * Queries PWS/SWS to generate a Person, given an identifier type and value.
     *
     * Identifier type must be one of ["uwregid", "uwnetid", "employee_id", "student_number", "student_system_key", "development_id"]
     *
     * @param string $identifierKey
     * @param string $identifierValue
     * @return null|Person
     * @throws \Exception if $identifierKey is not one of ["uwregid", "uwnetid", "employee_id", "student_number", "student_system_key", "development_id"]
     */
    public static function fromIdentifier($identifierKey, $identifierValue) {
        $validIdentifierKeys = ["uwregid", "uwnetid", "employee_id", "student_number", "student_system_key", "development_id"];
        if (!in_array($identifierKey,$validIdentifierKeys)) {
            throw new \Exception("Identifier key '$identifierKey' must be one of [" . implode(", ", $validIdentifierKeys) . "].");
        }

        $resp = static::getConn()->execGET(
            "https://ws.admin.washington.edu/identity/v1/person.json?$identifierKey=$identifierValue"
        );
        $resp = static::parse($resp);

        if (sizeof($resp["Persons"]) == 0) {
            return null;
        } else {
            $uwnetid = $resp["Persons"][0]["PersonFullURI"]["UWNetID"];
            return static::fromSimpleIdentifier($uwnetid);
        }
    }

    /**
     * Casts one subclass of Person into another.
     *
     * Ex:
     * $p = Person::fromUWNetId($uwnetid); // $p is a Person
     * $p = Employee::fromPerson($p); // $p is now cast into an employee
     * @param Person $oldPerson
     * @return Person
     */
    public static function fromPerson(Person $oldPerson) {
        $newPerson = new static();
        return static::fill($newPerson,  $oldPerson->_raw);
    }

    protected static function fill(Person $person, array $attrs) {
        $person->_raw = $attrs;
        $person->_affiliations = $attrs["EduPersonAffiliations"];

        foreach ($attrs as $key => $value) {
            if (is_string($value) || is_null($value) || is_bool($value)) {
                $person->setAttr($key, $value);
            }
        }

        return $person;
    }

    protected static function getConn() {
        return Connection::getInstance();
    }

    protected static function parse($resp) {
        return Parser::parse($resp);
    }

}