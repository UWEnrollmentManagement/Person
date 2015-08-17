UWDOEM/Person
=============

Smoothly poll the university's Person Web Service and Student Web Service to aggregate data on a given affiliate, using X.509 certificate authentication.

For example:

```
    // Intialize the connection
    Connection::createInstance($my_ssl_key_path, $my_ssl_cert_path, $my_ssl_key_passwd)
    
    // Query the web services
    $student = Student::fromUWNetID("javerage");
    
    echo $student->getAttr("DisplayName");
    // "James Average Student"
    
    echo $student->getAttr("StudentNumber");
    // "1033334"
    
    $employee = Employee::fromUWNetID("jschilz");
    
    echo $employee->getAttr("Department1");
    // "Student Financial Aid Office"
    
    echo $employee->getAttr("Title1");
    // "Web Developer"

```


Installation
===============

This library will be published to packagist shortly, at which point you will be able to install it via composer. Directions to follow.

How it Works
============

Instructions to follow.

Use
===

Instructions to follow.


Compatibility
=============

* PHP5


Todo
====

* Poll the Student Web Service for more information on students, as appropriate.
* Infer well-capitalized DisplayFirstName, DisplayLastName, DisplayMI, DisplayMiddleName attributes.

License
====

Employees of the University of Washington may use this code in any capacity, without reservation.

Getting Involved
================

Feel free to open pull requests or issues. GitHub is the canonical location of this project.
