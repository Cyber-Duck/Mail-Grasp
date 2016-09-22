<?php
namespace Cyberduck\MailGrasp\Testing;

use Symfony\Component\DomCrawler\Crawler;
use Cyberduck\MailGrasp\MailGrasp;
use Cyberduck\MailGrasp\Support\Message;

trait InteractsWithEmails
{
    protected $mailer;

    public function message()
    {
        return new Message();
    }

    public function visit($uri)
    {
        if (!$this->mailer instanceof MailGrasp) {
            $this->mailer = new MailGrasp();
        }
        return parent::visit($uri);
    }

    /**
     * Assert that $count emails have been sent.
     *
     * @param integer $count number of email which are expected to be sent.
     */
    public function seeEmails($count, $queue = MailGrasp::UNQUEUED)
    {
        $count = intval($count);
        if ($queue) {
            $emailsSent = count($this->mailer->getAllQueuedEmails());
        } else {
            $emailsSent = count($this->mailer->getAllEmails());
        }

        $this->assertEquals(
            $count,
            $emailsSent,
            "Expected $count emails, found $emailsSent."
        );

        return $this;
    }

    public function seeEmailsInQueue($count)
    {
        return $this->seeEmails($count, MailGrasp::QUEUED);
    }

    /**
     * Assert that no email has been sent.
     */
    public function dontSeeEmails($queue = MailGrasp::UNQUEUED)
    {
        if ($queue) {
            $emailsSent = count($this->mailer->getAllQueuedEmails());
        } else {
            $emailsSent = count($this->mailer->getAllEmails());
        }

        $this->assertEmpty(
            $this->mailer->getAllEmails(),
            "Did not expect any emails, found $emailsSent"
        );

        return $this;
    }

    /**
     * Alias for dontSeeEmails().
     */
    public function notSeeEmails()
    {
        return $this->dontSeeEmails();
    }

    public function dontSeeEmailsInQueue()
    {
        return $this->dontSeeEmails(MailGrasp::QUEUED);
    }

    public function notSeeEmailsQueue()
    {
        return $this->dontSeeEmails(MailGrasp::QUEUED);
    }

    /**
     * Assert that an email matching the given criterias has been sent.
     */
    public function seeEmail($email, $queued = MailGrasp::UNQUEUED)
    {
        if ($queued) {
            $found = $this->mailer->getQueuedEmail($email) ? true : false;
        } else {
            $found = $this->mailer->getEmail($email) ? true : false;
        }

        $this->assertTrue($found, $this->mailer->getError($email));

        return $this;
    }

    public function seeEmailInQueue($email)
    {
        return $this->seeEmail($email, MailGrasp::QUEUED);
    }

    /**
     * Visit the link on the email matching the given criteria.
     *
     * @param string $from The expected sender.
     * @param string $to The expected receiver.
     * @param string $subject The expected subject.
     * @param string $selector A css selector to identify the link.
     */
    protected function clickInEmail($email)
    {
        $this->seeEmail($email);
        $found = $this->mailer->getEmail($email);
        $this->crawler = new Crawler($email->getBody(), 'https://mail.test');
        $links = $this->crawler->filter($selector);

        //Todo check if the first element is a link.

        $this->assertNotEmpty($links, 'No link found in email.');

        $this->visit($links->link()->getUri());

        return $this;
    }
}
