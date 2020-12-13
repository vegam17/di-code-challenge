<?php

/**
 * @package    DI
 * @author     Miguel Vega <miguel@vega.dev>
 * 
 * Provides functionality for working with Ajax Requests
 */

namespace DI;

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
            'errors'    =>  $this->validate()
        ];

        if( !empty( $output['errors'] ) ) {

            $output['data'] = 'Please correct the following errors: ';
            $output['data'] .= implode( ', ', $this->get_field_names( $output['errors'] ) );
            rtrim( $output['data'], ', ');

        } else {

            $this->save();
            $this->send();

            $output['success'] = true;
            $output['data'] = 'Your message has been received!';
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
            if( $schema[ 'required' ] ){
                if( strlen( $value ) === 0 ) $errors[] = $field;
            }

            // if not valid email, add to errors
            if( $schema[ 'type' ] === 'email' ){
                if( !filter_var( $value, FILTER_VALIDATE_EMAIL ) && !in_array( $field, $errors ) )  $errors[] = $field;
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
        return;
    }

    private function send(){
        return;
    }

    private function get_field_names( $fields ){
        foreach( $fields as $field ){
            $names[] = str_replace( $this->name . '_', '', $field );
        }
        return $names;
    }
}