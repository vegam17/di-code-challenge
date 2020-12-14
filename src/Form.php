<?php

/**
 * @package    DI
 * @author     Miguel Vega <miguel@vega.dev>
 * 
 * Parent class for working with forms
 */

namespace DI;

require '../config/config.php';

use DI\Database;
use DI\Mail;

class Form {

    // defines name, used to parse field names from IDs
    protected $name = '';

    // sanitized form data
    protected $fields = [];

    // defines schema used for validation
    protected $schema = [];

    /**
     * Instantiates form and sanitizes values
     * 
     * @param   array   $fields     array of form fields
     * 
     * @return  null
     */
    function __construct( $fields ){
        $this->fields = $this->sanitize( $fields );
    }

    /**
     * Main method for processing forms
     * 1. validates form
     * 2. saves form to db
     * 3. emails copy of form
     * 
     * I could definitely improve the way the output is processed
     * 
     * @return  array   $output     array of values to be encoded and returned 
     */
    public function process(){
        $output = [
            'success'   =>  false,
            'errors'    =>  $this->validate(),
            'data'      =>  ''
        ];

        // validation errors occurred, stop processing
        if( !empty( $output['errors'] ) ) {

            $output['data'] .= 'Please correct the following errors: ';
            $output['data'] .= implode( ', ', $this->get_field_names( $output['errors'] ) );
            rtrim( $output['data'], ', ');

        } else {

            // validation successful

            // save to database
            if( ENABLE_SAVING_FORMS ) $this->save();
            
            // send email
            if( ENABLE_MAILING_FORMS ) {
                $email = new Mail( $this->fields );
                $email->send();
            }

            $output['success'] = true;
            $output['data'] = 'Your message has been received successfully!';
        }

        return $output;
    }

    /**
     * Validates the submitted form based on its schema
     * 
     * This validation function could be refactored as a class
     * 
     * @return  array   $errors     array of identified validation errors
     */
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
                if( !filter_var( $value, FILTER_VALIDATE_EMAIL ) && !in_array( $field, $errors ) ) $errors[] = $field;
            }

            // if value length exceeds limit
            if( isset( $schema['length'] ) && $schema['length'] > 0 ){
                if( strlen( $value ) > $schema['length'] && !in_array( $field, $errors ) )  $errors[] = $field;
            }
            
        }

        return $errors;
    }

    /**
     * Sanitizes raw submission input
     * 
     * @param   array   $fields     associative array of submitted form fields
     * 
     * @return  array   $sanitized_values   array of sanitized values to be set as class property
     */
    private function sanitize( $fields ){

        // We are only working with strings for this form, but this could be enhanced for other data types
        foreach( $fields as $key => $value ){
            $sanitized_values[ $key ] = filter_var( $value, FILTER_SANITIZE_STRING );
        }
        return $sanitized_values;
    }

    /**
     * Saves form submission to the database
     * 
     * @return  bool    $saved      whether or not the save was successful
     */
    private function save(){

        $db = Database::get_instance();
        $query = $db->prepare( 'INSERT INTO forms ( name, email, phone, message ) VALUES ( :name, :email, :phone, :message )' );
        $query->bindParam( 'name', $this->fields['contact_name'], \PDO::PARAM_STR );
        $query->bindParam( 'email', $this->fields['contact_email'], \PDO::PARAM_STR );
        $query->bindParam( 'phone', $this->fields['contact_phone'], \PDO::PARAM_STR );
        $query->bindParam( 'message', $this->fields['contact_message'], \PDO::PARAM_STR );

        return $query->execute();
    }

    /**
     * Helper method to clean field ids 
     * 
     * @param   array   $fields     array of field names
     * 
     * @return  array   $names      array of field name slugs
     */
    private function get_field_names( $fields ){
        foreach( $fields as $field ){
            $names[] = str_replace( $this->name . '_', '', $field );
        }
        return $names;
    }
}