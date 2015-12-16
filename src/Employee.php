<?php

namespace UWDOEM\Person;

/**
 * Container class for person and employee-specific information received from Person Web Service and Student Web Service
 *
 * @package UWDOEM\Person
 */
class Employee extends Person
{

    /** @var string */
    protected static $AFFILIATION_TYPE = "employee";

    /**
     * @param Person $person
     * @param array  $attrs
     * @return Employee
     */
    protected static function fill(Person $person, array $attrs)
    {
        $attrs = array_merge(
            $attrs,
            $attrs["PersonAffiliations"]["EmployeePersonAffiliation"],
            $attrs["PersonAffiliations"]["EmployeePersonAffiliation"]["EmployeeWhitePages"]
        );

        return parent::fill($person, $attrs);
    }

    /**
     * @param string $employeeID
     * @return null|Employee
     */
    public static function fromEmployeeID($employeeID)
    {
        return static::fromIdentifier("employee_id", $employeeID);
    }
}
