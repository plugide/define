<?php

namespace Plugide\Define\Support\Concerns;

use Plugide\Define\Contracts\Plugable;

trait Plugin
{
    /**
     * @var Plugable
     */
    protected Plugable $plugin;

    /**
     * Define plugin in service provider.
     *
     * @param null $plugin
     * @return $this|Plugable
     */
    public function plugin($plugin = null)
    {
        if (is_null($plugin)) {
            return  $this->plugin;
        }

        $this->plugin = $plugin;

        return $this;
    }
}
