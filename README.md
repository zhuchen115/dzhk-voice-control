Voice Control
===================
This project is used to add authentication and voice control (instead of the build in) function to Smart Home,
This project is request by Amels and DZHK.

Notice
-------
This project is not under maintaince. 

Smart Home Software
-------------------
This Gateway implements the JSON API of HomeSeer due to the requirement of the whole projectat this monents, but it is not a free software.
HomeSeer is only be used as the driver of Z-Wave, [OpenZwave](https://github.com/OpenZWave/open-zwave) is suggested to take place of it.
To do this please rewrite the homeseer related class.

Multi-Language Support
-----------------------
This gateway fully support multi-language, except when the script has a error.
Translation is placed under data/language/
[gettext](http://php.net/manual/en/function.gettext.php) is used for localize the PHP scripts, the .mo file under
data/language/(Language Code)/LC_MESSAGE/
[Smarty](http://www.smarty.net/) is used for localize the template, you can translate the HTML file by config file of smarty under
data/language/(Language Code)/smarty/
It is also possible to translate the Javascript file, see the json file under
data/language/(Language Code/js/

To change the language of the system, you make sure all the language file is ready, or something strange may happen.
See [gettext document](http://www.gnu.org/software/gettext/manual/gettext.html) of the language code

Database Support
-----------------
In this project MariaDB is used, you can replace it with mysql.

Development Notice
-------------------

###Language Processing

It's better to replace the language process system, now is using preg_* function.
You can use [GoogleSyntax Net](https://github.com/tensorflow/models/tree/master/syntaxnet) instead of this.

###Connect Device
You can make all the health monitor device connected. Database table is created, but no code on it

License
-------
Released under GPL v3