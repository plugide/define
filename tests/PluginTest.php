<?php

namespace Plugide\Define\Tests;

use Plugest\Demo\Test;
use Plugide\Define\Plugin;

class PluginTest extends TestCase
{
    /**
     * Test find plugin.
     *
     * @return void
     */
    public function test_find_plugin()
    {
        $this->assertTrue(
            (bool) Plugin::where('name', 'demo')->first()
        );
    }

    /**
     * Test plugin files class working
     *
     * @return void
     */
    public function test_plugin_file_class()
    {
        $this->assertTrue(
            (bool) Test::working() == 'working'
        );
    }
}
