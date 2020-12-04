# Logger Module

Logger module is based on `ErrorHeroModule` from Github : 
https://github.com/samsonasik/ErrorHeroModule

The difference is that logger instead of sending mail, 
he writes in a text file `data/log_error.txt` the errors if database 
is not available

In `config/autolad/mezzio-error-hero-module.local.php` on propery `logpath`
you can specify another path of log file

And some files are not use from Github project because use custom view for errors
and for implementing on `laminas-mvc`