<?php

namespace DI;

class Mail {

    protected $to = TO_ADDRESS;

    protected $from = FROM_ADDRESS;

    protected $subject = '';

    protected $body = '';

    function __construct( $body = '', $subject = 'New contact form submission!' ){
        $this->body = $this->format_body( $body );
        $this->subject = $subject;
    }

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
                            <td><?php echo $body['contact_name']; ?></td>
                            <td><?php echo $body['contact_email'] ?></td>
                            <td><?php echo $body['contact_phone'] ?></td>
                            <td><?php echo $body['contact_message'] ?></td>
                        </tr>
                    </table>
                </body>
            </html>
        <?php
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    public function send(){
        return mail( $this->to, $this->subject, $this->body, $this->get_headers() );
    }

    protected function get_headers(){
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "Reply-To: $this->from" . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        return $headers;
    }

}