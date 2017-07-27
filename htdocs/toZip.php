<?php

echo __DIR__;

//shell_exec ( 'zip -r zipfile17062016.zip /srv/www/staging.diecrema.de/htdocs' )

//or 
shell_exec ('tar -zcvf archive-name.tar.gz /srv/www/staging.diecrema.de/htdocs');

?>