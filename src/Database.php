<?php
namespace DI;

/**
 * @package    DI
 * @author     Miguel Vega <miguel@vega.dev>
 * 
 * Database class to provide saving functionality
 */

require '../config/config.php';

class Database {

    // instance of connection
    protected static $connection = false;

    /**
     * Establishes a database connection handler and assigns it to connection property
     * 
     * @return  null  
     */
    private static function connect(){
        $dsn = sprintf( 'mysql:host=%s;dbname=%s;charset=utf8mb4', DB_SERVER, DB_NAME);
        try {
            $dbh = new \PDO( $dsn, DB_USER, DB_PASS );
            
            // for debugging purposes
            //$dbh->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );

            self::$connection = $dbh;
        } catch( \PDOException $error ){
            throw new \PDOException( $error->getMessage() );
        }
    }

    /**
     * Returns singleton instance of database
     * 
     * @return  PDO pdo database object  
     */
    public static function get_instance(){
        if( !self::$connection ) self::connect();
        return self::$connection;
    }
}