<?php

namespace UWDOEM\Person;

/**
 * Container class for person and alumni-specific information received from Person Web
 * Service and Student Web Service
 *
 * @method static null|Alumni fromUWNetID() fromUWNetID(string $uwnetid)
 *                            Queries PWS/SWS to generate a Alumni, given a UWNetID.
 * @method static null|Alumni fromUWRegID() fromUWRegID(string $uwregid)
 *                            Queries PWS/SWS to generate a Alumni, given a UWRegID.
 * @method static null|Alumni fromIdentifier() fromIdentifier(string $identifierKey, string $identifierValue)
 *                            Queries PWS/SWS to generate a Person, given an identifier type and value.
 *
 * @package UWDOEM\Person
 */
class Alumni extends Person
{

    /** @var string */
    protected static $AFFILIATION_TYPE = "student";

    /**
     * Create an Alumni from a Person and a set of supplementary attributes.
     *
     * @param Person $person
     * @param array  $attrs
     * @return Alumni
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
     * Create an Alumni from a development ID.
     *
     * @param string $developmentID
     * @return null|Alumni
     */
    public static function fromDevelopmentID($developmentID)
    {
        return static::fromIdentifier("development_id", $developmentID);
    }
}
