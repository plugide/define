<?php

namespace Plugide\Define\Tests;

use Plugide\Define\Plug;

class PlugTest extends TestCase
{
    /**
     * Test plug start.
     *
     * @return void
     */
    public function test_plug_start()
    {
        $this->assertTrue(true);
    }

    /**
     * Test data is array.
     *
     * @return void
     */
    public function test_plug_data_is_array()
    {
        $this->assertTrue(is_array(Plug::data()));
    }
}
