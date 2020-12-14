<?php

/**
 * @package    DI
 * @author     Miguel Vega <miguel@vega.dev>
 * 
 * Provides functionality for working with Ajax Requests
 */

namespace DI;

use DI\Database;
use DI\Mail;

class Form {

    protected $name = '';

    protected $fields = [];

    protected $schema = [];

    function __construct( $fields ){
        $this->fields = $this->sanitize( $fields );
    }

    public function process(){
        $output = [
            'success'   =>  false,
            'errors'    =>  $this->validate(),
            'data'      =>  ''
        ];

        if( !empty( $output['errors'] ) ) {

            $output['data'] .= 'Please correct the following errors: ';
            $output['data'] .= implode( ', ', $this->get_field_names( $output['errors'] ) );
            rtrim( $output['data'], ', ');

        } else {

            // Save to database
            $saved = $this->save();
            
            $email = new Mail( $this->fields );
            $sent = $email->send();

            // Success
            if( $sent && $saved ){
                $output['success'] = true;
                $output['data'] = 'Your message has been received successfully!';
            } elseif( $sent ){
                $output['data'] = 'Your message was emailed but not saved to the database.';
            } elseif( $saved ){
                $output['data'] = 'Your message was saved to the database but not emailed.';
            } else {
                $output['data'] = 'Your message was neither saved nor emailed.';
            }
        }

        return $output;
    }


    private function validate(){
        
        $errors = [];

        // if no schema, do not validate
        if( empty( $this->schema ) ) return $errors;

        foreach( $this->fields as $field => $value ){
            
            $schema = $this->schema[ $field ];

            // field not found in schema
            if( !$schema ) continue;

            // if required and empty, add to errors
            if( isset( $schema['required'] ) && $schema[ 'required' ] ){
                if( strlen( $value ) === 0 ) $errors[] = $field;
            }

            // if not valid email, add to errors
            if( isset( $schema['type'] ) && $schema[ 'type' ] === 'email' ){
                if( !filter_var( $value, FILTER_VALIDATE_EMAIL ) && !in_array( $field, $errors ) )  {
                    $errors[] = $field;
                } else {
                    
                };
            }

            if( isset( $schema['type'] ) && $schema[ 'type' ] === 'text' ){
                if( !filter_var( $value, FILTER_SANITIZE_STRING ) && !in_array( $field, $errors ) )  $errors[] = $field;
            }

            // if value length exceeds limit
            if( isset( $schema['length'] ) && $schema['length'] > 0 ){
                if( strlen( $value ) > $schema['length'] && !in_array( $field, $errors ) )  $errors[] = $field;
            }
            
        }

        return $errors;
    }

    // We only have text fields to worry about
    private function sanitize( $fields ){
        foreach( $fields as $key => $value ){
            $sanitized_values[ $key ] = filter_var( $value, FILTER_SANITIZE_STRING );
        }
        return $sanitized_values;
    }

    private function save(){

        $db = Database::get_instance();
        $query = $db->prepare( 'INSERT INTO forms ( name, email, phone, message ) VALUES ( :name, :email, :phone, :message )' );
        $query->bindParam( 'name', $this->fields['contact_name'], \PDO::PARAM_STR );
        $query->bindParam( 'email', $this->fields['contact_email'], \PDO::PARAM_STR );
        $query->bindParam( 'phone', $this->fields['contact_phone'], \PDO::PARAM_STR );
        $query->bindParam( 'message', $this->fields['contact_message'], \PDO::PARAM_STR );

        return $query->execute();
    }

    private function get_field_names( $fields ){
        foreach( $fields as $field ){
            $names[] = str_replace( $this->name . '_', '', $field );
        }
        return $names;
    }
}