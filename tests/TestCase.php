<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function createApplication()
    {
        $createApp = function () {
            $app = require __DIR__ . '/../bootstrap/app.php';
            $app->make(Kernel::class)->bootstrap();
            return $app;
        };

        $app = $createApp();
        if ($app->environment() !== 'testing') {
            $this->clearCache();
            $app = $createApp();
        }

        return $app;
    }

    /**
     * Clears Laravel Cache.
     */
    protected function clearCache()
    {
        $commands = ['clear-compiled', 'cache:clear', 'config:clear', 'route:clear', 'view:clear',];
        foreach ($commands as $command) {
            Artisan::call($command);
        }
    }

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('--env=testing migrate --seed');
        Artisan::call('passport:install --force');
    }
}
