<?php

namespace UWDOEM\Person;

use UWDOEM\Connection\Connection;

/**
 * Container class for data received from Person Web Service and Student Web Service
 *
 * @package UWDOEM\Person
 */
class Person
{

    /** @var array */
    protected $attrs = [];

    /** @var array */
    protected $raw = [];

    /** @var array */
    protected $affiliations = [];

    /** @var \UWDOEM\Connection\Connection */
    protected static $personConnection;

    /** @var \UWDOEM\Connection\Connection */
    protected static $studentConnection;

    /** @var string */
    protected static $AFFILIATION_TYPE = "person";

    /**
     * Sets a value on your LOCAL COPY of the person.
     *
     * SWS/PWS do not support UPDATING person/student models.
     * @param string $key
     * @param mixed  $value
     * @return void
     */
    public function setAttr($key, $value)
    {
        $this->attrs[$key] = $value;
    }

    /**
     * Gets a value from your local copy of the person.
     *
     * @param string $key
     * @return mixed
     */
    public function getAttr($key)
    {
        return $this->attrs[$key];
    }

    /**
     * Returns whether the given person has the given affiliation.
     *
     * @param string $affiliation
     * @return boolean
     */
    public function hasAffiliation($affiliation)
    {
        return in_array($affiliation, $this->affiliations);
    }

    /**
     * Returns an arry of the affiliate's attributes.
     *
     * @return array
     */
    public function getAttrs()
    {
        return $this->attrs;
    }

    /**
     * Queries PWS/SWS to generate a Person, given a UWNetID.
     * @param string $uwnetid
     * @return null|Person
     */
    public static function fromUWNetID($uwnetid)
    {
        return static::fromIdentifier("uwnetid", $uwnetid);
    }

    /**
     * Queries PWS/SWS to generate a Person, given a UWRegID.
     *
     * @param string $uwregid
     * @return null|Person
     */
    public static function fromUWRegID($uwregid)
    {
        return static::fromIdentifier("uwregid", $uwregid);
    }

    /**
     * Queries PWS/SWS to generate a Person, given an identifier type and value.
     *
     * Identifier type must be one of ["uwregid", "uwnetid", "employee_id", "student_number",
     *                                 "student_system_key", "development_id"].
     *
     * @param string $identifierKey
     * @param string $identifierValue
     * @return null|Person
     * @throws \Exception If $identifierKey is not one of ["uwregid", "uwnetid", "employee_id",
     *                                                     "student_number", "student_system_key",
     *                                                     "development_id"].
     */
    public static function fromIdentifier($identifierKey, $identifierValue)
    {
        /** @var null|Person $person */
        $person = null;

        /** @var string $simpleIdentifier */
        $simpleIdentifier = "";

        /** @var string[] $validIdentifierKeys */
        $validIdentifierKeys = [
            "uwregid", "uwnetid", "employee_id", "student_number", "student_system_key", "development_id"
        ];

        /** @var string[] $simpleIdentifierKeys */
        $simpleIdentifierKeys = ["uwregid", "uwnetid"];

        /** @var boolean $identifierKeyIsValid */
        $identifierKeyIsValid = in_array($identifierKey, $validIdentifierKeys);

        /** @var boolean $identifierKeyIsSimple */
        $identifierKeyIsSimple = in_array($identifierKey, $simpleIdentifierKeys);

        if ($identifierKeyIsValid === false) {
            throw new \Exception(
                "Identifier key '$identifierKey' must be one of [" . implode(", ", $validIdentifierKeys) . "]."
            );
        }

        if ($identifierKeyIsSimple === true) {
            $simpleIdentifier = $identifierValue;
        } else {
            $resp = static::getPersonConnection()->execGET(
                "person.json?$identifierKey=$identifierValue"
            );
            $resp = static::parse($resp);

            if (array_key_exists("Persons", $resp) === true && sizeof($resp["Persons"]) > 0) {
                $simpleIdentifier = $resp["Persons"][0]["PersonFullURI"]["UWNetID"];
            }
        }

        if ($simpleIdentifier !== "") {
            $resp = static::getPersonConnection()->execGET(
                "person/$simpleIdentifier/full.json"
            );
            $resp = static::parse($resp);

            if (array_key_exists("UWRegID", $resp) === true) {
                $person = new static();
                $person = static::fill($person, $resp);
            }
        }

        return $person;
    }

    /**
     * Casts one subclass of Person into another.
     *
     * Ex:
     * $p = Person::fromUWNetId($uwnetid); // $p is a Person
     * $p = Employee::fromPerson($p); // $p is now cast into an employee
     *
     * @param Person $oldPerson
     * @return Person
     */
    public static function fromPerson(Person $oldPerson)
    {
        $newPerson = new static();
        return static::fill($newPerson, $oldPerson->raw);
    }

    /**
     * @param Person $person
     * @param array  $attrs
     * @return Person
     */
    protected static function fill(Person $person, array $attrs)
    {
        $person->raw = $attrs;
        $person->affiliations = $attrs["EduPersonAffiliations"];

        foreach ($attrs as $key => $value) {
            if (is_string($value) === true || is_bool($value) === true || $value === null) {
                $person->setAttr($key, $value);
            }
        }

        return $person;
    }

    /**
     * @param string $baseUrl
     * @return Connection
     * @throws \Exception If any of the required constants have not been set.
     */
    protected static function makeConnection($baseUrl)
    {
        $requiredConstants = ["UW_WS_BASE_PATH", "UW_WS_SSL_KEY_PATH", "UW_WS_SSL_CERT_PATH", "UW_WS_SSL_KEY_PASSWD"];

        foreach ($requiredConstants as $constant) {
            if (defined($constant) === false) {
                throw new \Exception("You must define the constant $constant before using this library.");
            }
        }

        return new Connection(
            UW_WS_BASE_PATH . $baseUrl,
            UW_WS_SSL_KEY_PATH,
            UW_WS_SSL_CERT_PATH,
            UW_WS_SSL_KEY_PASSWD,
            defined("UW_WS_VERBOSE") && UW_WS_VERBOSE
        );
    }

    /**
     * @return Connection
     */
    protected static function getPersonConnection()
    {
        if (static::$personConnection === null) {
            static::$personConnection = static::makeConnection("identity/v1/");
        }
        return static::$personConnection;
    }

    /**
     * @return Connection
     */
    protected static function getStudentConnection()
    {
        if (static::$studentConnection === null) {
            static::$studentConnection = static::makeConnection("student/v5/");
        }
        return static::$studentConnection;
    }

    /**
     * @param string $resp
     * @return array
     */
    protected static function parse($resp)
    {
        return Parser::parse($resp);
    }
}
