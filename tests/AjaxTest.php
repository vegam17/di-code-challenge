<?php
namespace DI\Test;

require dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class AjaxTest extends TestCase {

    protected static $Client;

    protected function setUp(): void {
        self::$Client = new Client( [ 'base_uri' => 'http://127.0.0.1:9999' ] );
    }

    protected function tearDown(): void {
        self::$Client = null;
    }

    /**
     * Test to see if a non ajax request is successfully handled by server with JSON failure response
     */
    public function testInvalidAjaxRequest() {
        $response = self::$Client->request( 'POST', '/ajax.php' );
        $this->assertEquals( 200, $response->getStatusCode(), 'Successful rejection of non ajax request' );
        $this->assertEquals( 
            json_encode( [ 
                'success'   =>  false,
                'data'      =>  'invalid AJAX request'
            ] ), 
            $response->getBody(), 
            'Correct response is given in rejecting non ajax request'
        );
    }

    /**
     * Test to see if a valid ajax request returns a successful JSON response
     */
    public function testValidAjaxRequest() {
        $response = self::$Client->request( 'POST', '/ajax.php', [
            'headers' => [
                'X_REQUESTED_WITH' =>  'XMLHttpRequest'
            ] 
        ]);

        $body = json_decode( $response->getBody() );

        $this->assertEquals( 200, $response->getStatusCode(), 'Successful response of valid ajax request' );

        $this->assertEquals( 
            json_encode( [ 
                'success'   =>  false,
                'data'      =>  'no form data submitted'
            ] ), 
            $response->getBody(), 
            'Request is successful when correct headers are present'
        );
    }
}