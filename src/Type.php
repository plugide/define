<?php

namespace Plugide\Define;

use Plugide\Define\Contracts\Typable;

/**
 * @property mixed plugin
 * @property mixed id
 */
class Type extends Prototype implements Typable
{
    /**
     * Collect the types
     *
     * @return array|null
     */
    public function collect()
    {
        return Plug::types();
    }

    /**
     * Get the namespace
     *
     * @param null $key
     * @return mixed
     */
    public function namespace($key = null)
    {
        $namespace = $this->get('namespace');

        return $key ? $namespace."\\".$key : $namespace;
    }

    /**
     * Get plugin with type.
     *
     * @return \Plugide\Define\Contracts\Plugable|null
     */
    public function plugin()
    {
        return (new $this->plugin())->type($this->id);
    }
}
