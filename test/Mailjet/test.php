<?php

namespace Mailjet;

require __DIR__.'/../../vendor/autoload.php';

class MailjetTest extends \PHPUnit_Framework_TestCase
{
    private function assertUrl($url, $response)
    {
        $this->assertEquals('https://api.mailjet.com/v3'.$url, $response->request->getUrl());
    }

    public function assertPayload($payload, $response)
    {
        $this->assertEquals($payload, $response->request->getBody());
    }

    public function assertFilters($shouldBe, $response)
    {
        $this->assertEquals($shouldBe, $response->request->getFilters());
    }


    public function testGet()
    {
        $client = new Client('', '', false);

        $this->assertUrl('/REST/contact', $client->get(Resources::$Contact));

        $this->assertFilters(array('id' => 2), $client->get(Resources::$Contact, array(
            'filters' => array('id' => 2)
        )));

        $response = $client->get(Resources::$ContactGetcontactslists, array('id' => 2));
        $this->assertUrl('/REST/contact/2/getcontactslists', $response);

        // error on sort !
        $response = $client->get(Resources::$Contact, array(
            'filters' => array('sort' => 'email+DESC')
        ));
        $this->assertUrl('/REST/contact', $response);

        $this->assertUrl('/REST/contact/2', $client->get(Resources::$Contact, array('id' => 2)));

        $this->assertUrl(
            '/REST/contact/test@mailjet.com',
            $client->get(Resources::$Contact, array('id' => 'test@mailjet.com'))
        );
    }

    public function testPost()
    {
        $client = new Client('', '', false);

        $email = array(
          'FromName'     => 'Mailjet PHP test',
          'FromEmail'    => 'dev@amo-soft.com',
          'Text-Part'    => 'Simple Email test',
          'Subject'      => 'PHPunit',
          'Html-Part'    => '<h3>Simple Email Test</h3>',
          'Recipients'   => array(array('Email' => 'test@mailjet.com')),
          'MJ-custom-ID' => 'Hello ID',
        );

        $ret = $client->post(Resources::$Email, array('body' => $email));
        $this->assertUrl('/send', $ret);
        $this->assertPayload($email, $ret);
    }
}
