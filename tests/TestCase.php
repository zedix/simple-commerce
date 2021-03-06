<?php

namespace DoubleThreeDigital\SimpleCommerce\Tests;

use App\User;
use Barryvdh\DomPDF\ServiceProvider as DomPDFServiceProvider;
use DoubleThreeDigital\SimpleCommerce\ServiceProvider;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Statamic\Extend\Manifest;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Fieldset;
use Statamic\Providers\StatamicServiceProvider;
use Statamic\Statamic;

abstract class TestCase extends OrchestraTestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker;

    protected $shouldFakeVersion = true;

    protected function setUp(): void
    {
        require_once(__DIR__.'/__fixtures__/app/User.php');

        parent::setUp();

        $this->withFactories(realpath(__DIR__.'/../database/factories'));
        $this->loadMigrationsFrom(__DIR__.'/__fixtures__/database/migrations');

        if ($this->shouldFakeVersion) {
            \Facades\Statamic\Version::shouldReceive('get')->andReturn('3.0.0-testing');
            $this->addToAssertionCount(-1); // Dont want to assert this
        }
    }

    protected function getPackageProviders($app)
    {
        return [
            StatamicServiceProvider::class,
            ServiceProvider::class,
            DomPDFServiceProvider::class, // Our PDF package
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Statamic' => Statamic::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app->make(Manifest::class)->manifest = [
            'doublethreedigital/simple-commerce' => [
                'id' => 'doublethreedigital/simple-commerce',
                'namespace' => 'DoubleThreeDigital\\SimpleCommerce\\',
            ],
        ];

        Statamic::pushActionRoutes(function() {
            return require_once realpath(__DIR__.'/../routes/actions.php');
        });

        Statamic::pushCpRoutes(function() {
            return require_once realpath(__DIR__.'/../routes/cp.php');
        });

        Blueprint::setDirectory(__DIR__.'/../resources/blueprints');
        Fieldset::setDirectory(__DIR__.'/../resources/fieldsets');
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $configs = [
            'assets', 'cp', 'forms', 'static_caching', 'sites', 'stache', 'system', 'users'
        ];

        foreach ($configs as $config) {
            $app['config']->set("statamic.$config", require(__DIR__."/../vendor/statamic/cms/config/{$config}.php"));
        }

        $app['config']->set('statamic.stache', require(__DIR__.'/__fixtures__/config/statamic/stache.php'));
        $app['config']->set('statamic.users', require(__DIR__.'/__fixtures__/config/statamic/users.php'));
        $app['config']->set('auth', require(__DIR__.'/__fixtures__/config/auth.php'));
        $app['config']->set('simple-commerce', require(__DIR__.'/../config/simple-commerce.php'));
        $app['config']->set('filesystems', require(__DIR__.'/__fixtures__/config/filesystems.php'));
        $app['config']->set('database.connections.mysql.strict', false);
    }

    public function actAsUser()
    {
        return $this->actingAs(factory(User::class)->create());
    }

    public function actAsSuper()
    {
        return $this->actingAs(factory(User::class)->create(['super' => true]));
    }
}
