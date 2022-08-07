<?php

namespace Plugide\Define\Support\Concerns;

use Illuminate\Support\Arr;

trait HasCommon
{
    /**
     * The common data associated with the prototype.
     *
     * @var array
     */
    protected array $common = [];

    /**
     * Get or Set the common data.
     *
     * @param array|string|null $key
     * @return mixed|string
     */
    public function common($key = null)
    {
        if (is_array($key)) {
            $this->common = $key;
            return $this;
        }

        if ($key) {
            return Arr::get($this->common, $key);
        }

        return $this->common;
    }
}
