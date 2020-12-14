<?php
namespace DI;

require '../config/config.php';

class Database {

    protected static $connection = false;

    private static function connect(){
        $dsn = sprintf( 'mysql:host=%s;dbname=%s;charset=utf8mb4', DB_SERVER, DB_NAME);
        try {
            $dbh = new \PDO( $dsn, DB_USER, DB_PASS );
            
            //$dbh->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );

            self::$connection = $dbh;
        } catch( \PDOException $error ){
            throw new \PDOException( $error->getMessage() );
        }
    }

    public static function get_instance(){
        if( !self::$connection ) self::connect();
        return self::$connection;
    }
}