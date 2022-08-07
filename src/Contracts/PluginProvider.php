<?php

namespace Plugide\Define\Contracts;

interface PluginProvider
{
    /**
     * Define plugin in service provider.
     *
     * @param null $plugin
     * @return static|Plugable
     */
    public function plugin($plugin = null);
}
