********************************************************************************
# A Complete Authentication using RESTful server implementation for CodeIgniter.
********************************************************************************
@aurthor:            Richard Sunny

Recently I had to work on building up Restful Services for a Mobile App. 
Without reinventing the wheel I was able to find the basic implementation for restful architecture within codeigniter by Phil Sturgeon, Chris Kacerguis, @softwarespot.
This extended work includes most of the commanly used Authentication methods within any mobile application.   

# Getting started

1. Clone/Download the application 

2. Setup Base URL in config/config.php
e.g. Local Server http://localhost/ciAuth/api/

3. Import /ciauthdb.sql and setup Database configurations in config/database.php

4. Setup SMTP settings to get Email Notifications working

## Restful Web Services Includes

application\controllers\Api.php contains following services/methods: 

* Login
* Register
* Forgot Password
* Change Password
* and a couple of more helping Restful methods

*******************
Server Requirements
*******************

PHP version 5.4 or newer is recommended.

It should work on 5.2.4 as well, but we strongly advise you NOT to run
such old versions of PHP, because of potential security and performance
issues, as well as missing features.

************
Installation
************

Please see the `installation section <http://www.codeigniter.com/user_guide/installation/index.html>`_
of the CodeIgniter User Guide.

*******
Credits
*******

* @author          Phil Sturgeon, Chris Kacerguis
* @link            https://github.com/chriskacerguis/codeigniter-restserver

*******
License
*******

Please see the `license
agreement <https://github.com/bcit-ci/CodeIgniter/blob/develop/user_guide_src/source/license.rst>`_.
