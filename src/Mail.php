<?php

namespace DI;

/**
 * @package    DI
 * @author     Miguel Vega <miguel@vega.dev>
 * 
 * Mailer class for sending emails using PHP sendmail
 */

class Mail {

    // receipient address
    protected $to = TO_ADDRESS;

    // server from address
    protected $from = FROM_ADDRESS;

    // email subject
    protected $subject = '';

    // html form body
    protected $body = '';

    /**
     * Initializes email and sets the subject and body
     * 
     * @param   array   $body       array of values to be included in email
     * @param   string  $subject    email subject line
     */
    function __construct( $body = [], $subject = 'New contact form submission!' ){
        $this->body = $this->format_body( $body );
        $this->subject = $subject;
    }

    /**
     * Uses object buffering to build html template for email
     * 
     * @param   array   $body       array of values to be included in email
     * 
     * @return  string  $output     html email template
     */
    private function format_body( $body ){
        ob_start(); ?>
            <html>
                <head>
                    <title>Contact Form Submission</title>
                </head>
                <body>
                    <table>
                        <tr>
                            <th>Full name:</th>
                            <th>Email:</th>
                            <th>Phone:</th>
                            <th>Message:</th>
                        </tr>
                        <tr>
                            <td><?php echo isset( $body['contact_name'] ) ? $body[ 'contact_name' ] : ''; ?></td>
                            <td><?php echo isset( $body['contact_email'] ) ? $body[ 'contact_email' ] : ''; ?></td>
                            <td><?php echo isset( $body['contact_phone'] ) ? $body[ 'contact_phone' ] : ''; ?></td>
                            <td><?php echo isset( $body['contact_message'] ) ? $body[ 'contact_message' ] : ''; ?></td>
                        </tr>
                    </table>
                </body>
            </html>
        <?php
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    /**
     * Uses PHP sendmail to send email address
     * Immediately returns false if sendmail is disabled (mail function will not exist)
     * 
     * @return  boolean  $sent     whether or not theemail was successfully sent
     */
    public function send(){
        
        // sendmail is disabled
        if( !function_exists( 'mail' ) ) return false;

        return mail( $this->to, $this->subject, $this->body, $this->get_headers() );
    }

    /**
     * Builds the HTTP headers for the email
     * Mime type needs to be set to text/html to be able to use HTML template
     * 
     * @return  boolean  $sent     whether or not theemail was successfully sent
     */
    protected function get_headers(){
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "Reply-To: $this->from" . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        return $headers;
    }

}