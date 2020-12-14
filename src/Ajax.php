<?php

/**
 * @package    DI
 * @author     Miguel Vega <miguel@vega.dev>
 * 
 * Provides functionality for working with Ajax Requests
 */

namespace DI;

use DI\ContactForm;

class Ajax {

    /**
     * Processess AJAX requests for contact form submissions
     * 
     * @return  string json encoded response
     */
    public static function handleAjaxRequest(){

        // kill request if not ajax or post request//
        if( !self::is_ajax() ) self::response( [ 
            'success'   =>  false,
            'data'      =>  'invalid AJAX request'
        ] );

        // valid ajax request but no form submitted
        if( empty( $_REQUEST ) ) self::response( [ 
            'success'   =>  false,
            'data'      =>  'no form data submitted'
        ] );

        // here we could handle different types of ajax requests
        // for now, only contact form
        $contact_form = new ContactForm( $_REQUEST );
        self::response( $contact_form->process() );
    }

    /**
     * Determines whether or not the current request is an Ajax request
     * Because headers can be spoofed, this is not a secure way to determine origin
     * 
     * @return  bool
     */
    private static function is_ajax(){
        return isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH'] ) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ;
    }

    /**
     * Determines whether or not the current request is an Ajax request
     * 
     * @param   array     array of values to be encoded
     * @return  string
     */
    public static function response( $data = [] ){
        echo json_encode( $data );
        die();
    }
}