<?php

namespace Plugide\Define;

use ArrayAccess;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;
use Plugide\Define\Support\Concerns\HasRouting;

abstract class Prototype implements Arrayable, ArrayAccess, Jsonable, JsonSerializable, UrlRoutable
{
    use HasRouting,
        ForwardsCalls,
        Macroable {
            __call as macroCall;
        }

    /**
     * The prototype's attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The primary key for the prototype.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the prototype exists.
     *
     * @var bool
     */
    public $exists = false;

    /**
     * Create a new prototype instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }

    /**
     * Create a new instance of the given prototype.
     *
     * @param array $attributes
     * @param bool $exists
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $prototype = new static((array) $attributes);

        $prototype->exists = $exists;

        return $prototype;
    }

    /**
     * Create a new Prototypes Collection instance.
     *
     * @param  array  $prototypes
     * @return \Illuminate\Support\Collection
     */
    public function newCollection(array $prototypes = [])
    {
        return new Collection($prototypes);
    }

    /**
     * Get a new query builder for the prototype's.
     *
     * @return \Illuminate\Support\Collection
     */
    public function newQuery()
    {
        return $this->newCollection($this->collect());
    }

    /**
     * Collect the prototypes
     *
     * @return array|null
     */
    abstract public function collect();

    /**
     * Get the primary key for the prototype.
     *
     * @return string
     */
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    /**
     * Set the primary key for the prototype.
     *
     * @param  string  $key
     * @return $this
     */
    public function setKeyName($key)
    {
        $this->primaryKey = $key;

        return $this;
    }

    /**
     * Get the value of the prototype's primary key.
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->get($this->getKeyName());
    }

    /**
     * Get an attribute from the prototype.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }

        return value($default);
    }

    /**
     * Get all of the current attributes on the prototype.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Convert the prototype instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return mixed
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Convert the prototype instance to JSON.
     *
     * @param  int  $options
     * @return string
     *
     * @throws \Illuminate\Database\Eloquent\JsonEncodingException
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->attributes[$offset] = $value;
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->attributes[$offset]);
    }

    /**
     * Handle dynamic method calls into the prototype.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return $this->forwardCallTo($this->newQuery(), $method, $parameters);
    }

    /**
     * Handle dynamic static method calls into the prototype.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return (new static())->$method(...$parameters);
    }

    /**
     * Dynamically retrieve the value of an attribute.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Dynamically set the value of an attribute.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Dynamically check if an attribute is set.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Dynamically unset an attribute.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }
}
