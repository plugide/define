<?php

namespace Plugide\Define\Tests;

use Orchestra\Testbench\TestCase as OrchestraTest;
use Plugide\Define\Plug;

abstract class TestCase extends OrchestraTest
{
    public function setUp(): void
    {
        parent::setUp();

        Plug::start(realpath(__DIR__ .'/root'));
    }
}
