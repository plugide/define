<?php

namespace Plugide\Define\Support\Concerns;

trait HasEvents
{
    /**
     * Dispatch an event
     *
     * @param string $event
     */
    public function event(string $event)
    {
        $this->dispatcher($event);
    }

    /**
     * Fire the given event for the prototype.
     *
     * @param string $event
     * @return mixed|void
     */
    protected function dispatcher(string $event)
    {
        if ($this->common('observer') && method_exists($this->common('observer'), $event)) {
            return $this->common('observer')->{$event}($this);
        }
    }
}
