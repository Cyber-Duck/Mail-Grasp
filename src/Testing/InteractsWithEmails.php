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

    /**
     * Swap the mailer when the visit function is called.
     *
     * @param string $uri the page to visit.
     */
    public function visit($uri)
    {
        if (!$this->mailer instanceof MailGrasp) {
            $this->mailer = new MailGrasp();
        }
        return parent::visit($uri);
    }

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

    public function notSeeEmails()
    {
        return $this->dontSeeEmails();
    }

    public function dontSeeEmailsInQueue()
    {
        return $this->dontSeeEmails(MailGrasp::QUEUED);
    }

    public function notSeeEmailsInQueue()
    {
        return $this->dontSeeEmails(MailGrasp::QUEUED);
    }

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


    public function seeInEmail($email, $text)
    {
        $found = $this->mailer->getEmail($email) ? true : false;

        $this->assertTrue($found, $this->mailer->getError($email));

        $rawPattern = preg_quote($text, '/');
        $escapedPattern = preg_quote(e($text), '/');
        $pattern = $rawPattern == $escapedPattern
                ? $rawPattern : "({$rawPattern}|{$escapedPattern})";

        $this->assertRegExp(
            "/$pattern/i",
            $this->mailer->getEmail($email)->getBody()
        );
    }

    protected function clickInEmail($email, $selector = 'a')
    {
        $this->seeEmail($email);
        $found = $this->mailer->getEmail($email);
        $this->crawler = new Crawler($found->getBody(), 'https://mail.test');
        $links = $this->crawler->filter($selector);

        //Todo check if the first element is a link.

        $this->assertNotEmpty($links, 'No link found in email.');

        $this->visit($links->link()->getUri());

        return $this;
    }
}
