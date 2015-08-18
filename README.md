
UWDOEM/Person
=============

Smoothly poll the University of Washington's Person Web Service and Student Web Service to aggregate data on a given affiliate, using X.509 certificate authentication.

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

This library is published on packagist. To install using Composer, add the `"uwdoem/person": "0.1.*"` line to your "require" dependencies:

```
{
    "require": {
        "uwdoem/person": "0.1.*"
    }
}
```

Of course, if you're not using Composer then you can download the repository using the buttons at right.

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
