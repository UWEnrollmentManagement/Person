<?php

namespace UWDOEM\Person;

/**
 * Container class for person and alumni-specific information received from Person Web Service and Student Web Service
 *
 * @package UWDOEM\Person
 */
class Alumni extends Person
{

    /** @var string */
    protected static $AFFILIATION_TYPE = "student";

    /**
     * @param Person $person
     * @param array  $attrs
     * @return Person
     */
    protected static function fill(Person $person, array $attrs)
    {
        $attrs = array_merge(
            $attrs,
            $attrs["PersonAffiliations"]["AlumPersonAffiliation"]
        );

        return parent::fill($person, $attrs);
    }

    /**
     * @param string $developmentID
     * @return null|Person
     */
    public static function fromDevelopmentID($developmentID)
    {
        return static::fromIdentifier("development_id", $developmentID);
    }
}
