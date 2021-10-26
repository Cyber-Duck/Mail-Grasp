# 🚨 Discontinued 🚨
This functionality is redily available in later releases of Laravel.

# MailGrasp

[![Build Status](https://travis-ci.org/Cyber-Duck/Mail-Grasp.svg?branch=master)](https://travis-ci.org/Cyber-Duck/Mail-Grasp)
[![Latest Stable Version](https://poser.pugx.org/cyber-duck/mailgrasp/v/stable)](https://packagist.org/packages/cyber-duck/mailgrasp)
[![Total Downloads](https://poser.pugx.org/cyber-duck/mailgrasp/downloads)](https://packagist.org/packages/cyber-duck/mailgrasp)
[![License](https://poser.pugx.org/cyber-duck/mailgrasp/license)](https://raw.githubusercontent.com/Cyber-Duck/Mail-Grasp/master/LICENSE)

MailGrasp is a package for Laravel applications (5.1+) to add support for email testing in your test classes.

Author: [Simone Todaro](https://github.com/SimoTod)

Made with :heart: by [Cyber-Duck Ltd](http://www.cyber-duck.co.uk)

## Installation

```
composer require cyber-duck/mailgrasp --dev
```

## Usage

Add the `InteractsWithEmails` to your test class. That's it!

```php
use \Cyberduck\MailGrasp\Testing\InteractsWithEmails;
```

The custom mailer will be initialised as soon as the visit() method is called.  

### seeEmails

It checks if exactly `$count` emails have been sent or enqueued.

```php
$this->visit('/route/which/sends/2/emails')
    ->seeEmails(2);
```

### seeEmailsInQueue

It checks if exactly `$count` emails have been enqueued.

```php
$this->visit('/route/which/enqueues/2/emails')
    ->seeEmailsInQueue(2);
```

### dontSeeEmails / notSeeEmails

It checks that no email has been sent or enqueued.

```php
$this->visit('/route/with/no/emails')
    ->dontSeeEmails();

// OR

$this->visit('/route/with/no/emails')
    ->notSeeEmails();
```

### dontSeeEmailsInQueue / notSeeEmailsInQueue

It checks that no email has been enqueued.

```php
$this->visit('/route/with/no/emails')
    ->dontSeeEmailsInQueue();

// OR

$this->visit('/route/with/no/emails')
    ->notSeeEmailsInQueue();
```

### seeEmail

It checks that an email matching given critaria has been sent or enqueued.

```php
$this->visit('/route/which/sends/emails')
    ->seeEmail(function($m) {
        $m->from('from@test.com');
        $m->to('to@test.com');
        $m->subject('Subject');
    });

// OR

$this->visit('/route/which/sends/emails')
    ->seeEmail($this->message()
        ->from('from@test.com')
        ->to('to@test.com')
        ->subject('Subject');
    });

```

### dontSeeEmail

Complete opposite of seeEmail.

```php
$this->visit('/route/which/sends/emails')
    ->dontSeeEmail(function($m) {
        $m->from('from@test.com');
        $m->to('to@test.com');
        $m->subject('Subject');
    });

// OR

$this->visit('/route/which/sends/emails')
    ->dontSeeEmail($this->message()
        ->from('from@test.com')
        ->to('to@test.com')
        ->subject('Subject');
    });

```

### seeEmailInQueue

It checks that an email matching given critaria has been enqueued.

```php
$this->visit('/route/which/enqueues/emails')
    ->seeEmailInQueue(function($m) {
        $m->from('from@test.com');
        $m->to('to@test.com');
        $m->subject('Subject');
    });

// OR

$this->visit('/route/which/enqueues/emails')
    ->seeEmailInQueue($this->message()
        ->from('from@test.com')
        ->to('to@test.com')
        ->subject('Subject');
    });
```

### seeInEmail

It checks that an email matching the given critaria contains the given string.

```php
$this->visit('/route/which/sends/emails')
    ->seeInEmail(function($m) {
        $m->from('from@test.com');
        $m->to('to@test.com');
        $m->subject('Subject');
    }, 'Lorem ipsum dolor sit amet');

// OR

$this->visit('/route/which/sends/emails')
    ->seeInEmail($this->message()
        ->from('from@test.com')
        ->to('to@test.com')
        ->subject('Subject');
    }, 'Lorem ipsum dolor sit amet);

```

### clickInEmail

Visit the page in the email link. Useful to test activation links.

```php
$this->visit('/route/which/enqueues/emails')
    ->clickInEmail(function($m) {
        $m->from('from@test.com');
        $m->to('to@test.com');
        $m->subject('Subject');
    });

// OR

$this->visit('/route/which/enqueues/emails')
    ->clickInEmail($this->message()
        ->from('from@test.com')
        ->to('to@test.com')
        ->subject('Subject');
    });
```

If there is more than one link in the email, it's possible to select the link passing a css selector as second parameter.

```php
$this->visit('/route/which/enqueues/emails')
    ->clickInEmail(function($m) {
        $m->from('from@test.com');
        $m->to('to@test.com');
        $m->subject('Subject');
    }, 'a.activation-link');

// OR

$this->visit('/route/which/enqueues/emails')
    ->clickInEmail($this->message()
        ->from('from@test.com')
        ->to('to@test.com')
        ->subject('Subject');
    }, 'a.activation-link');
```
