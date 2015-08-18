<?php

namespace UWDOEM\Person;

/**
 * Container class for person and employee-specific information received from Person Web Service and Student Web Service
 *
 * @package UWDOEM\Person
 */
class Employee extends Person {

    protected static $AFFILIATION_TYPE = "employee";

    protected static function fill(Person $person, array $attrs) {
        $attrs = array_merge(
            $attrs,
            $attrs["PersonAffiliations"]["EmployeePersonAffiliation"],
            $attrs["PersonAffiliations"]["EmployeePersonAffiliation"]["EmployeeWhitePages"]);

        return parent::fill($person, $attrs);
    }

}