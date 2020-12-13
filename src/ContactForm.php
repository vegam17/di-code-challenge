<?php 

/**
 * @package    DI
 * @author     Miguel Vega <miguel@vega.dev>
 * 
 * Provides functionality for working with Ajax Requests
 */

namespace DI;

use DI\Form;

class ContactForm extends Form {

    protected $name = 'contact';

    protected $schema = [
        'contact_name'      =>  [
            'type'      =>  'text',
            'required'  =>  true
        ],
        'contact_email'     =>  [
            'type'      =>  'email',
            'required'  =>  true
        ],
        'contact_phone'     =>  [
            'type'      =>  'text',
            'required'  =>  false
        ],
        'contact_message'   =>  [
            'type'      =>  'text',
            'required'  =>  true
        ]
    ];

}