<?php

use Cyberduck\MailGrasp\MailGrasp;
use Cyberduck\MailGrasp\Support\Message;
use Illuminate\Support\Facades\Mail;

class MailGraspTest extends TestCase
{
    public function test_mailer_is_mailgrasp()
    {
        $this->assertInstanceOf(MailGrasp::class, app()->make('mailer'));
    }

    public function test_mailgrasp_can_capture_email()
    {
        Mail::send('email.template', [], function ($m) {
            $m->from('hello@app.com', 'Your Application');
            $m->to('hi@app.com', 'Your User');
            $m->subject('Your Reminder!');
        });
        Mail::plain('Lorem ipsum', [], function ($m) {
            $m->from('hello@app.com', 'Your Application');
            $m->to('hi@app.com', 'Your User');
            $m->subject('Your Reminder!');
        });
        Mail::raw('Lorem ipsum', function ($m) {
            $m->from('hello@app.com', 'Your Application');
            $m->to('hi@app.com', 'Your User');
            $m->subject('Your Reminder!');
        });

        $this->assertEquals(3, count($this->mailgrasp->getAllEmails()));
    }

    public function test_mailgrasp_can_capture_email_in_queue()
    {
        Mail::queue('email.template', [], function ($m) {
            $m->from('hello@app.com', 'Your Application');
            $m->to('hi@app.com', 'Your User');
            $m->subject('Your Reminder!');
        });
        Mail::later(10, 'email.template', [], function ($m) {
            $m->from('hello@app.com', 'Your Application');
            $m->to('hi@app.com', 'Your User');
            $m->subject('Your Reminder!');
        });

        $this->assertEquals(2, count($this->mailgrasp->getAllQueuedEmails()));
    }

    public function test_can_find_an_email()
    {
        Mail::send('test::email', [], function ($m) {
            $m->from('hello@app.com', 'Your Application');
            $m->to('hi@app.com', 'Your User');
            $m->subject('Your Reminder!');
        });
        $mail = $this->mailgrasp->getEmail(function ($m) {
            $m->from('hello@app.com', 'Your Application');
            $m->to('hi@app.com', 'Your User');
        });

        $this->assertInstanceOf(Message::class, $mail);
        $this->assertEquals('Your Reminder!', $mail->getEmailData()['subject']);
    }

    public function test_can_find_a_queued_email()
    {
        Mail::queue('test::email', [], function ($m) {
            $m->from('hello@app.com', 'Your Application');
            $m->to('hi@app.com', 'Your User');
            $m->subject('Your Reminder!');
        });
        $mail = $this->mailgrasp->getQueuedEmail(function ($m) {
            $m->from('hello@app.com', 'Your Application');
            $m->to('hi@app.com', 'Your User');
        });

        $this->assertInstanceOf(Message::class, $mail);
        $this->assertEquals('Your Reminder!', $mail->getEmailData()['subject']);
    }
}
