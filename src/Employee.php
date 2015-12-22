<?php

namespace UWDOEM\Person;

/**
 * Container class for person and employee-specific information received from Person Web
 * Service and Student Web Service
 *
 * @method static null|Employee fromUWNetID() fromUWNetID(string $uwnetid)
 *                              Queries PWS/SWS to generate a Employee, given a UWNetID.
 * @method static null|Employee fromUWRegID() fromUWRegID(string $uwregid)
 *                              Queries PWS/SWS to generate a Employee, given a UWRegID.
 * @method static null|Employee fromIdentifier() fromIdentifier(string $identifierKey, string $identifierValue)
 *                              Queries PWS/SWS to generate a Person, given an identifier type and value.
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
