<?php

namespace Plugide\Define\Support\Concerns;

use Illuminate\Support\Str;

trait HasRouting
{
    /**
     * Get the value of the prototype's route key.
     *
     * @return mixed
     */
    public function getRouteKey()
    {
        return $this->getAttribute($this->getRouteKeyName());
    }

    /**
     * Get the route key for the prototype.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return $this->getKeyName();
    }

    /**
     * Retrieve the prototype for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Plugide\Define\Prototype|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? $this->getRouteKeyName(), $value)->first();
    }

    /**
     * Retrieve the child prototype for a bound value.
     *
     * @param  string  $childType
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Plugide\Define\Prototype|null
     */
    public function resolveChildRouteBinding($childType, $value, $field)
    {
        return $this->{Str::plural($childType)}()->where($field, $value)->first();
    }
}
