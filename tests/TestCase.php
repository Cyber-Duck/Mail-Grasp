<?php

use Cyberduck\MailGrasp\MailGrasp;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    protected $mailgrasp;
    protected $currentPage;

    public function setUp()
    {
        $app = new Application('');
        Facade::setFacadeApplication($app);

        $configKeys = [
            'mail.from.name' => 'First Last',
            'mail.from.address' => 'foo@bar.tld',
        ];
        $config = Mockery::mock(Repository::class);
        $config->shouldReceive('has')
            ->andReturnUsing(function ($key) use ($configKeys) {
                if (isset($configKeys[$key])) {
                    return true;
                }
                return false;
            });
        $config->shouldReceive('get')
            ->andReturnUsing(function ($key) use ($configKeys) {
                if (isset($configKeys[$key])) {
                    return $configKeys[$key];
                }
                return null;
            });
        Config::swap($config);

        $factory = Mockery::mock(Factory::class);
        $factory->shouldReceive('make')
            ->andReturnUsing(function ($template, $data) {
                return new view_helper();
            });
        View::swap($factory);

        $this->mailgrasp = new MailGrasp();
    }

    public function visit($uri)
    {
        $this->currentPage = $uri;
    }
}
