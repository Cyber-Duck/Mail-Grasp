<?php
namespace Cyberduck\MailGrasp;

use Cyberduck\MailGrasp\Support\Message;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Mail\MailQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class MailGrasp implements Mailer, MailQueue
{
    protected $emails;
    protected $queued;

    protected $from;
    protected $to;

    const QUEUED = true;
    const UNQUEUED = false;

    public function __construct()
    {
        $this->emails = new Collection();
        $this->queued = new Collection();

        $from = Config::get('mail.from');
        if (is_array($from) && isset($from['address'])) {
            $this->from = [
                'address' => $from['address'],
                'name' => $from['name']
            ];
        }

        $to = Config::get('mail.to');
        if (is_array($to) && isset($to['address'])) {
            $this->to = [
                'address' => $to['address'],
                'name' => $to['name']
            ];
        }

        Mail::swap($this);
        App::instance(\Illuminate\Mail\Mailer::class, $this);
    }

    protected function getEmailFromClosure($email)
    {
        if (is_callable($email)) {
            $callback = $email;
            $email = new Message();
            call_user_func($callback, $email);
        }

        return $email;
    }

    public function getEmail($email, $queued = self::UNQUEUED)
    {
        $email = $this->getEmailFromClosure($email);

        $collection = $queued ? $this->queued : $this->emails;

        $index = $collection->search(function ($item, $key) use ($email) {
            return $item->match($email) !== false;
        });

        if ($index !== false) {
            return $collection->get($index);
        }

        return null;
    }

    public function getQueuedEmail($email)
    {
        return $this->getEmail($email, self::QUEUED);
    }

    public function getAllEmails()
    {
        return $this->emails->toArray();
    }

    public function getAllQueuedEmails()
    {
        return $this->queued->toArray();
    }

    public function getError($email)
    {
        $email = $this->getEmailFromClosure($email);

        $message = "\nExpected an email";
        $message .= "\033[33m{".$email."\033[0m\n\n";

        if ($this->emails->count()) {
            $message .= "Founded:\n";
            foreach ($this->emails as $index => $email) {
                $message .= $email."\n\n";
            }
        } else {
            $message .= "None founded.\n";
        }

        return $message;
    }

    /** MAIL CONTRACT **/
    public function raw($text, $callback)
    {
        return $this->send(['raw' => $text], [], $callback);
    }

    public function plain($view, array $data, $callback)
    {
        return $this->send(['text' => $view], $data, $callback);
    }

    public function send($view, array $data = [], $callback = null)
    {
        $message = $this->buildMessage($view, $data, $callback);
        $this->emails->push($message);
    }

    public function failures()
    {
        return [];
    }

    /** QUEUE CONTRACT **/
    public function queue($view, array $data, $callback, $queue = null)
    {
        $message = $this->buildMessage($view, $data, $callback);
        $this->emails->push($message);
        $this->queued->push($message);
    }

    public function later($delay, $view, array $data, $callback, $queue = null)
    {
        $this->queue($view, $data, $callback, $queue);
    }

    protected function buildMessage($view, array $data, $callback)
    {
        $message = new Message();

        if (!empty($this->from['address'])) {
            $message->from($this->from['address'], $this->from['name']);
        }

        $message->build($view, $data, $callback);

        return $message;
    }
}
