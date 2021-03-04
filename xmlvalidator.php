<?php
libxml_use_internal_errors(true);
$res=simplexml_load_file('xmltv/xmltv.xml');
if ($res === false) {
    echo "XML non valide\n";
    foreach(libxml_get_errors() as $error) {
        echo "\t", $error->message;
    }
    exit(1);
}
exit(0);
