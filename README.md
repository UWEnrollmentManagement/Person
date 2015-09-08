[![Build Status](https://travis-ci.org/UWEnrollmentManagement/Person.svg?branch=master)](https://travis-ci.org/UWEnrollmentManagement/Person)
[![Code Climate](https://codeclimate.com/github/UWEnrollmentManagement/Person/badges/gpa.svg)](https://codeclimate.com/github/UWEnrollmentManagement/Person)
[![Test Coverage](https://codeclimate.com/github/UWEnrollmentManagement/Person/badges/coverage.svg)](https://codeclimate.com/github/UWEnrollmentManagement/Person/coverage)
[![Latest Stable Version](https://poser.pugx.org/uwdoem/person/v/stable)](https://packagist.org/packages/uwdoem/person)

UWDOEM/Person
=============

Smoothly poll the University of Washington's [Person Web Service](https://wiki.cac.washington.edu/display/pws/Person+Web+Service) (PWS) and [Student Web Service](https://wiki.cac.washington.edu/display/SWS/Student+Web+Service) (SWS) to aggregate data on a given affiliate, using X.509 certificate authentication.

For example:

```
    // Intialize the connection
    Connection::createInstance(
        "https://ws.admin.washington.edu/",
        "/path/to/my/private.key",
        "/path/to/my/public_cert.pem",
        "myprivatekeypassword"
    );
    
    // Query the web services
    $student = Student::fromStudentNumber("1033334");
    
    echo $student->getAttr("RegisteredFirstMiddleName");
    // "JAMES AVERAGE"
    
    echo $student->getAttr("UWNetID");
    // "javerage"
    
    $employee = Employee::fromUWNetID("jschilz");
    
    echo $employee->getAttr("Department1");
    // "Student Financial Aid Office"
    
    echo $employee->getAttr("Title1");
    // "Web Developer"

```

Notice
======

This is *not* an official library, endorsed or supported by any party who manages or owns information accessed via PWS or SWS. This library is *not* endorsed or supported by the University of Washington Department of Enrollment Management.

Installation
===============

This library is published on packagist. To install using Composer, add the `"uwdoem/person": "0.1.*"` line to your "require" dependencies:

```
{
    "require": {
        "uwdoem/person": "0.1.*"
    }
}
```

Of course, if you're not using Composer then you can download the repository using the *Download ZIP* button at right.

Use
===

This client library provides a `Connection` class and four data-container classes: `Person`, `Student`, `Employee`, and `Alumni`.

If you have not already done so, follow PWS's instructions on [getting access to PWS](https://wiki.cac.washington.edu/display/pws/Getting+Access+to+PWS). A similar set of steps will allow you to [gain access to SWS](https://wiki.cac.washington.edu/display/SWS/Getting+Access+to+SWS). You'll need to place both the private private key you created and also the university-signed certificate on your web server, with read-accessibility for your web-server process.

Before querying the web services, you must first initialize the connection by calling `::createInstance`:

```
    // Intialize the connection
    Connection::createInstance(
        $base_service_url,
        $my_ssl_key_path,
        $my_ssl_cert_path,
        $my_ssl_key_passwd
    );
```

The arguments `$my_ssl_key_path` and `$my_ssl_cert_path` correspond to the absolute locations of your private key and university-signed certificate. The `$my_ssl_key_password` argument is OPTIONAL and should be provided only if you have a password associated with the provided private key file.

The argument `$base_service_url` corresponds to the base URL shared by UW web services. Currently this is either `"https://ws.admin.washington.edu/"` for the production-access web services, or `"https://wseval.s.uw.edu/"` for the testing/development-access web services.

You may now issue queries against the web service:

```
    // Queries PWS/SWS for a student with StudentNumber "1033334".
    $student = Student::fromStudentNumber("1033334");
    
    // If no such student was found, then $student is null
    if ($student != null) {
        echo $student->getAttr("RegisteredFirstMiddleName");
    }
```

In the case above, there does exist a student with StudentNumber "1033334": one of the university's notional test students. So this script will output "JAMES AVERAGE".

The following methods may be used to query the database:

```
    // Available to Person, and all subclasses of Person
    $person = Person::fromUWNetID($uwnetid);
    $person = Person::fromUWRegID($uwregid);
    $person = Person::fromIdentifier("uwregid", $uwregid);
    $person = Person::fromIdentifier("uwnetid", $uwnetid);
    $person = Person::fromIdentifier("employee_id", $employeeid);
    $person = Person::fromIdentifier("student_number", $studentnumber);
    $person = Person::fromIdentifier("student_system_key", $studentsystemkey);
    $person = Person::fromIdentifier("development_id", $developmentid);
    
    // Available only to Student
    $student = Student::fromStudentNumber($studentnumber);
    
    // Available only to Employee
    $employee = Employee::fromEmployeeID($employeeid);
    
    // Available only to Alumni
    $alumni = Alumni::fromDevelopmentID($developmentid);
```

You can cast between classes each of the container classes' `::fromPerson` method:

```
    $person = Person::fromUWNetID($uwnetid);
    
    // Cast the Person object into a Student
    $person = Student::fromPerson($person);
```

The `::hasAffiliation` method can tell you whether a given person is a student, employee, and/or alumni:

```
    $person = Person::fromUWNetID($uwnetid);
    
    // The ::hasAffiliation method check is useful, but not required:
    if ($person->hasAffiliation("employee") {
        $person = Employee::fromPerson($person);
    }
```

Use `::getAttr` to retrieve an attribute from a person:

```
    $person = Person::fromUWNetID($uwnetid);
    $displayName = $person->getAttr("DisplayName");
    
    $person = Student::fromPerson($person);
    $academicDepartment = $person->getAttr("Department1");

```

Exposed Attributes
==================

The container classes expose the following attributes, corresponding to those descibed in [this PWS glossary](https://wiki.cac.washington.edu/display/pws/PWS+Attribute+Glossary):

```
    Exposed by all classes:
        "DisplayName"
        "IsTestEntity"
        "RegisteredFirstMiddleName"
        "RegisteredName"
        "RegisteredSurname"
        "UIDNumber"
        "UWNetID"
        "UWRegID"
        "WhitepagesPublish"
        
    Exposed only by Employee:
        "EmployeeID"
        "Address1"
        "Address2"
        "Department1"
        "Department2"
        "Email1"
        "Email2"
        "Fax"
        "Name"
        "Phone1"
        "Phone2"
        "PublishInDirectory"
        "Title1"
        "Title2"
        "TouchDial"
        "VoiceMail"
    
    Exposed only by Student:
        "StudentNumber"
        "StudentSystemKey"
        "Class"
        "Department1"
        "Department2"
        "Department3"
        "Email"
        "Name"
        "Phone"
        "PublishInDirectory"
        
    Exposed only by Alumni:
        "DevelopmentID"

```
Troubleshooting
===============

This library *will* throw warnings and exceptions when it recognizes an error. Turn on error reporting to see these. The following conditions will halt execution:

cURL Error Code 77
------------------

**Problem**: cURL cannot find the UWCA root certificate to verify the identify of the PWS/SWS servers.

**Solution**: Download the [.crt root CA bundle](http://curl.haxx.se/docs/caextract.html) to your server, ensure that your web-server process has read access to this bundle, and uncomment/edit the `curl.cainfo` line in your *php.ini* to reflect the location of this bundle.

cURL Error Code 58
------------------

**Problem**: cURL is having a problem using your private key.

**Solution**: You may have provided an incorrect private key password to `::createConnection`. If your private key requires a password, provide one, and ensure that it is correct.

No such file found for SSL key/certificate
------------------------------------------

**Problem**: Connection cannot find the key and/or certificate at the path you provided to `::createConnection`.

**Solution**: Ensure that you provided the correct path to these files and that your web-server process has read-access to these files.

Script execution halts/no output
----------------------

**Problem**: This might be caused by an internal error in cURL while accessing your private key/certificate which causes PHP to die unexpectedly.

**Solution**: I was able to solve this by setting permissions on my key/certificate to read only. Specifically, I turned off write access for all parties.

Compatibility
=============

* PHP5
* Person Web Service v1
* Student Web Service v5

Todo
====

See GitHub [issue tracker](https://github.com/UWEnrollmentManagement/Person/issues/).

License
====

Employees of the University of Washington may use this code in any capacity, without reservation.

Getting Involved
================

Feel free to open pull requests or issues. [GitHub](https://github.com/UWEnrollmentManagement/Person) is the canonical location of this project.
