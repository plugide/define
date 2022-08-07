<?php

namespace Plugide\Define\Support;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Plugide\Define\Contracts\PluginProvider;

class ServiceProvider extends BaseServiceProvider implements PluginProvider
{
    use Concerns\Plugin;
}
