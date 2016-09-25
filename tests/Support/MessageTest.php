<?php

use Cyberduck\MailGrasp\Support\Message;

class MessageTest extends PHPUnit_Framework_TestCase
{
    public function test_make_returns_an_instance_of_message()
    {
        $message = Message::make();
        $this->assertInstanceOf(Message::class, $message);
    }

    public function test_get_email_data_returns_the_raw_data()
    {
        $message = Message::make();

        $this->assertInternalType('array', $message->getEmailData());
        $this->assertEmpty($message->getEmailData());

        $message->from('sender@test.com', 'sender')
            ->to('receiver@test.com', 'receiver')
            ->subject('Lorem ipsum dolor sit amet');

        $emailData = $message->getEmailData();

        $this->assertArrayHasKey('from', $emailData);
        $this->assertArrayHasKey('sender@test.com', $emailData['from']);
        $this->assertEquals($emailData['from']['sender@test.com'], 'sender');
        $this->assertArrayHasKey('to', $emailData);
        $this->assertArrayHasKey('receiver@test.com', $emailData['to']);
        $this->assertEquals($emailData['to']['receiver@test.com'], 'receiver');
        $this->assertArrayHasKey('subject', $emailData);
        $this->assertEquals($emailData['subject'], 'Lorem ipsum dolor sit amet');
    }

    public function test_match_returns_true_if_the_message_matches_criteria()
    {
        $message = Message::make();
        $message->from('sender@test.com', 'sender')
            ->to('receiver@test.com', 'receiver')
            ->subject('Lorem ipsum dolor sit amet');

        $this->assertTrue($message->match($message));

        $partialMessage = Message::make();
        $partialMessage->from('sender@test.com', 'sender');

        $this->assertTrue($message->match($partialMessage));

        $notMatchingMessage = Message::make();
        $notMatchingMessage->from('sender@test.net', 'sender');

        $this->assertFalse($message->match($notMatchingMessage));
    }
}
