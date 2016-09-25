<?php

use Cyberduck\MailGrasp\MailGrasp;
use Cyberduck\MailGrasp\Support\Message;
use Cyberduck\MailGrasp\Testing\InteractsWithEmails;
use Illuminate\Support\Facades\Mail;

class AssertionsTest extends TestCase
{
    use InteractsWithEmails;

    public function test_message()
    {
        $message = $this->message();
        $this->assertInstanceOf(Message::class, $message);
    }

    public function test_see_emails()
    {
        $this->visit('/');
        Mail::send('email.template', [], function ($m) {
            $m->from('hello@app.com', 'Your Application');
            $m->to('hi@app.com', 'Your User');
            $m->subject('Your Reminder!');
        });
        Mail::send('email.template', [], function ($m) {
            $m->from('hello@app.com', 'Your Application');
            $m->to('hi@app.com', 'Your User');
            $m->subject('Your Reminder!');
        });
        $this->seeEmails(2);
    }

    public function test_see_emails_in_queue()
    {
        $this->visit('/');
        Mail::queue('email.template', [], function ($m) {
            $m->from('hello@app.com', 'Your Application');
            $m->to('hi@app.com', 'Your User');
            $m->subject('Your Reminder!');
        });
        Mail::queue('email.template', [], function ($m) {
            $m->from('hello@app.com', 'Your Application');
            $m->to('hi@app.com', 'Your User');
            $m->subject('Your Reminder!');
        });
        $this->seeEmailsInQueue(2);
    }

    public function test_dont_see_emails()
    {
        $this->visit('/');
        $this->dontSeeEmails();
        $this->notSeeEmails();
    }

    public function test_dont_see_emails_in_queue()
    {
        $this->visit('/');
        $this->dontSeeEmailsInQueue();
        $this->notSeeEmailsInQueue();
    }

    public function test_see_email()
    {
        $this->visit('/');
        Mail::send('email.template', [], function ($m) {
            $m->from('hello@app.com', 'Your Application');
            $m->to('hi@app.com', 'Your User');
            $m->subject('Your Reminder!');
        });
        $this->seeEmail(function ($m) {
            $m->from('hello@app.com', 'Your Application');
            $m->to('hi@app.com', 'Your User');
            $m->subject('Your Reminder!');
        });
    }

    public function test_see_email_in_queue()
    {
        $this->visit('/');
        Mail::queue('email.template', [], function ($m) {
            $m->from('hello@app.com', 'Your Application');
            $m->to('hi@app.com', 'Your User');
            $m->subject('Your Reminder!');
        });
        $this->seeEmailInQueue(function ($m) {
            $m->from('hello@app.com', 'Your Application');
            $m->to('hi@app.com', 'Your User');
            $m->subject('Your Reminder!');
        });
    }

    public function test_click_in_email()
    {
        $this->visit('/');
        Mail::queue('email.template', [], function ($m) {
            $m->from('hello@app.com', 'Your Application');
            $m->to('hi@app.com', 'Your User');
            $m->subject('Your Reminder!');
        });
        $this->assertEquals('/', $this->currentPage);
        $this->clickInEmail(function ($m) {
            $m->from('hello@app.com', 'Your Application');
            $m->to('hi@app.com', 'Your User');
            $m->subject('Your Reminder!');
        });
        $this->assertEquals('https://mail.test/test', $this->currentPage);
    }
}
