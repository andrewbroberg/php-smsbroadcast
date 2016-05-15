<?php
require_once "src/kravock/SmsBroadcast.php";
use kravock\SmsBroadcast;

class SmsBroadcastTests extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->sms = new SmsBroadcast(
            array(
                'username'      => 'test',
                'password'      => 'test',
                'sender_name'   => 'test' 
                )
            );
    }

    public function testGetBalance()
    {
        $balance = $this->sms->getBalance();

        $this->assertInternalType('int', $balance);
    }

    public function testSetSender()
    {
        $sender = 'Sender';

        $this->sms->setSender($sender);

        $this->assertEquals($sender, $this->sms->getSender());
    }

    public function testGetSender()
    {
        $sender = 'Sender';

        $this->sms->setSender($sender);

        $this->assertEquals($this->sms->getSender(), $sender);
    }

    public function testSendSms()
    {
        $result = $this->sms->sendSms('Hello world',array('0400534986','0400534986'));

        $this->assertInternalType('string', $result);
    }
    // ...
}
