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

    public static function handleAjaxRequest(){

        // kill request if not ajax or post request
        if( !self::is_ajax() || $_SERVER['REQUEST_METHOD'] !== 'POST' ) die();

        // here we could handle different types of ajax requests
        // for now, only contact form
        $contact_form = new ContactForm( $_REQUEST );
        self::response( $contact_form->process() );
    }

    /**
     * Determines whether or not the current request is an Ajax request
     * Because headers can be spoofed, this is not a secure way to determine origin
     * 
     * @return bool
     */
    private static function is_ajax(){
        return isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH'] ) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ;
    }

    public static function response( $data = [] ){
        echo json_encode( $data );
    }
}