<?php

namespace UWDOEM\Person;

/**
 * Container class for data received from Person Web Service and Student Web Service
 *
 * @package UWDOEM\Person
 */
class Person {

    protected $attrs = [];

    public function setAttr($key, $value) {
        $this->attrs[$key] = $value;
    }

    public function getAttr($key) {
        return $this->attrs[$key];
    }

    public static function fromUWNetID($uwnetid) {
        return static::fromBase($uwnetid);
    }

    public static function fromUWRegID($uwregid) {
        return static::fromBase($uwregid);
    }

    protected static function fromBase($identifier) {
        $person = new static();

        $resp = static::getConn()->execGET(
            "https://ws.admin.washington.edu/identity/v1/person/$identifier/full.json"
        );
        return static::fill($person,  static::parse($resp));
    }
    
    protected static function fill(Person $person, array $attrs) {
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