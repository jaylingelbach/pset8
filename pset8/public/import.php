#!/usr/bin/env php
<?php

    // TODO
    // configuration
require(__DIR__ . "/../includes/config.php");
// query the database
$rows;

//open file of postal codes
$fp = fopen("/../vhosts/pset8/US.txt", r);
//make sure the file exists and is readable
if(file_exists("$fp")=== true && is_readable("$fp")=== true)
{
    while(($line = fgetcsv($fp, 1000, "\t"))!== FALSE)
    {
        $rows = query("INSERT INTO places (country_code, postal_code, place_name, admin_name1, admin_code1,
        admin_name2, admin_code2, admin_name3, admin_code3, latitude, longitude, accuracy) VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,)", $line[1], $line[2], $line[3], $line[4], $line[5], $line[6], $line[7],
        $line[8], $line[9], $line[10], $line[11], $line[12], $line[13]);
        
    }
    
    fclose($fp);
}
//if doesn't exist or isn't readable do something lol



?>
