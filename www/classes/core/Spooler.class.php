<?php

class Spooler extends Model
{
    function Spooler()
    {
        $connection_settings = array(
            'dbhost' => APP_MAILER_DBHOST,
            'dbname' => APP_MAILER_DBNAME,
            'dbuser' => APP_MAILER_DBUSER,
            'dbpass' => APP_MAILER_DBPASS,
            'charset' => 'utf8'
        );
        
        Model::Model('spooler', $connection_settings);
    }
}
