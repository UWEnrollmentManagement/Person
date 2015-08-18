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

    public function setAttr($key, $value) {
        $this->attrs[$key] = $value;
    }

    public function getAttr($key) {
        return $this->attrs[$key];
    }

    public function hasAffiliation($affiliation) {
        return in_array($affiliation, $this->_affiliations);
    }

    public static function fromUWNetID($uwnetid) {
        return static::fromSimpleIdentifier($uwnetid);
    }

    public static function fromUWRegID($uwregid) {
        return static::fromSimpleIdentifier($uwregid);
    }

    protected static function fromSimpleIdentifier($identifier) {
        $resp = static::getConn()->execGET(
            "https://ws.admin.washington.edu/identity/v1/person/$identifier/full.json"
        );

        $person = new static();
        return static::fill($person,  static::parse($resp));
    }

    public static function fromIdentifier($identifierKey, $identifierValue) {
        $validIdentifierKeys = ["uwregid", "uwnetid", "employeeid", "studentnumber", "studentsystemkey", "developmentid"];
        if (!in_array($identifierKey,$validIdentifierKeys)) {
            throw new \Exception("Identifier key '$identifierKey' must be one of [" . implode(", ", $validIdentifierKeys) . "].");
        }

        $resp = static::getConn()->execGET(
            "https://ws.admin.washington.edu/identity/v1/person.json?$identifierKey=$identifierValue"
        );
        $resp = static::parse($resp);

        $uwnetid = $resp["Current"]["UWNetID"];
        return static::fromSimpleIdentifier($uwnetid);
    }

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