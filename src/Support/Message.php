<?php
namespace Cyberduck\MailGrasp\Support;

use Illuminate\Support\Arr;

class Message extends \Illuminate\Mail\Message
{
    protected $data;

    public function __construct()
    {
        $this->data = array();
    }

    public static function make()
    {
        return new Message();
    }

    public function from($address, $name = null)
    {
        $this->data['from'][$address] = $name;
        return $this;
    }

    public function sender($address, $name = null)
    {
        return $this->setFrom($address, $name);
    }

    public function returnPath($address)
    {
        $this->data['returnPath'] = $address;
        return $this;
    }

    public function to($address, $name = null, $override = false)
    {
        if ($override) {
            $this->data['to'] = [];
        }
        if (is_array($address)) {
            foreach ($address as $singleAddress) {
                $this->data['to'][$singleAddress] = $name;
            }
        } else {
            $this->data['to'][$address] = $name;
        }
        return $this;
    }

    public function cc($address, $name = null, $override = false)
    {   
        if ($override) {
            $this->data['cc'] = [];
        }
        if (is_array($address)) {
            foreach ($address as $singleAddress) {
                $this->data['cc'][$singleAddress] = $name;
            }
        } else {
            $this->data['cc'][$address] = $name;
        }
        return $this;
    }

    public function bcc($address, $name = null, $override = false)
    {   
        if ($override) {
            $this->data['bcc'] = [];
        }
        if (is_array($address)) {
            foreach ($address as $singleAddress) {
                $this->data['bcc'][$singleAddress] = $name;
            }
        } else {
            $this->data['bcc'][$address] = $name;
        }
        return $this;
    }

    public function replyTo($address, $name = null)
    {
        if (is_array($address)) {
            foreach ($address as $singleAddress) {
                $this->data['replyTo'][$singleAddress] = $name;
            }
        } else {
            $this->data['replyTo'][$address] = $name;
        }
        return $this;
    }

    public function subject($subject)
    {
        $this->data['subject'] = $subject;
        return $this;
    }

    public function priority($level)
    {
        $this->data['priority'] = $level;
        return $this;
    }

    public function attach($file, array $options = [])
    {
        $this->data['attachment'][$file] = true;
    }

    public function attachData($data, $name, array $options = [])
    {
        $this->data['attachment'][$name] = true;
    }

    public function embed($file)
    {
        $this->data['embed'][$file] = true;
    }

    public function embedData($data, $name, $contentType = null)
    {
        $this->data['embed'][$name] = true;
    }

    public function setBody($body, $type)
    {
        $this->data['body'] = $body;
    }

    public function addPart($body, $type)
    {
        $this->data['part'][$type] = $body;
    }

    public function getBody()
    {
        return $this->data['body'];
    }

    public function getSwiftMessage()
    {
        return new \RuntimeException('Method not available');
    }

    public function match(Message $message)
    {
        $criteria = $message->getEmailData();
        foreach ($criteria as $key => $value) {
            if (is_array($value)) {
                if (count($value) != count(array_intersect_key($this->data[$key], $value))) {
                    return false;
                }
            } else {
                if ($this->data[$key] != $value) {
                    return false;
                }
            }
        }
        return true;
    }

    public function getEmailData()
    {
        return $this->data;
    }

    public function build($view, array $data, $callback)
    {
        $this->data = [];
        $this->addContent($view, $data);
        $this->callMessageBuilder($callback, $this);
    }

    protected function addContent($view, $data)
    {
        list($view, $plain, $raw) = $this->parseView($view);

        if (isset($view)) {
            $this->setBody($this->getView($view, $data), 'text/html');
        }

        if (isset($plain)) {
            $method = isset($view) ? 'addPart' : 'setBody';
            $this->$method($this->getView($plain, $data), 'text/plain');
        }

        if (isset($raw)) {
            $method = (isset($view) || isset($plain)) ? 'addPart' : 'setBody';
            $this->$method($raw, 'text/plain');
        }
    }

    protected function parseView($view)
    {
        if (is_string($view)) {
            return [$view, null, null];
        }

        if (is_array($view) && isset($view[0])) {
            return [$view[0], $view[1], null];
        }

        if (is_array($view)) {
            return [
                Arr::get($view, 'html'),
                Arr::get($view, 'text'),
                Arr::get($view, 'raw'),
            ];
        }

        throw new \InvalidArgumentException('Invalid view.');
    }

    protected function getView($view, $data)
    {
        return app()->make('view')->make($view, $data)->render();
    }

    protected function callMessageBuilder($callback, $message)
    {
        if ($callback instanceof \Closure) {
            return call_user_func($callback, $message);
        }

        if (is_string($callback)) {
            return $this->container->make($callback)->mail($message);
        }

        throw new \InvalidArgumentException('Callback is not valid.');
    }

    public function __get($key)
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : null;
    }

    public function __toString()
    {
        $string = "Message\n";
        foreach ($this->data as $key => $values) {
            $string .= "\t".$key.":\n";
            if (is_array($values)) {
                foreach ($values as $key => $value) {
                    $string .= "\t\t".$key.' '.$value."\n";
                }
            } else {
                $string .= "\t\t".$values."\n";
            }
        }
        return $string;
    }
}
