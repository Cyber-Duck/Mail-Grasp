#MailGrasp

MailGrasp is a package for Laravel applications (5.1) to add support for email testing in your test classes.

##Installation
```
composer require cyber-duck/cyber-duck/mailgrasp --dev
```

##Usage
Add the `InteractsWithEmails` to your test class. That's it!  
The custom Mailer will be swapped as soon as the visit() method has been called.  
If your testClass doesn't use the build in `InteractsWithPages` method, the custom mailer should be manually enabled by calling
```
MailGrasp::swap();
```

###seeEmails
It checks if exactly `$count` emails have been sent.

```
$this->visit('/route/which/sends/2/emails')
    ->seeEmails(2);
```

###seeEmailsInQueue
It checks if exactly `$count` emails have been enqueued.

```
$this->visit('/route/which/queues/2/emails')
    ->seeEmailsInQueue(2);
```

###dontSeeEmails / notSeeEmails
It checks that no email has been sent.

```
$this->visit('/route/with/no/emails')
    ->dontSeeEmails();

//OR

$this->visit('/route/with/no/emails')
    ->notSeeEmails();
```

###dontSeeEmailsInQueue / notSeeEmailsInQueue
It checks that no email has been enqueued.

```
$this->visit('/route/with/no/emails')
    ->dontSeeEmailsInQueue();

//OR

$this->visit('/route/with/no/emails')
    ->notSeeEmailsInQueue();
```

###seeEmail

###seeEmailInQueue

###clickInEmail
