<?php

namespace Acme;

/**
 * Description of ContactFormTest
 *
 * @author Sedera
 */
//class ContactFormTest extends \PHPUnit_Framework_TestCase {

use Silex\WebTestCase;

class ContactFormTest extends WebTestCase {
    public function testInitialPage()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('h1:contains("Contact us")'));
        $this->assertCount(1, $crawler->filter('form'));
    }

    public function createApplication() {
        $app = require __DIR__.'/web/app.php';
        $app['debug'] = true;
        unset($app['exception_handler']);
        
        $app['session.test'] = true;

        return $app;
    }

}
