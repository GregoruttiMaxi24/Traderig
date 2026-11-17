<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * con esto para hacer los tests no se necesitan los archivos compilados de Vite.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }
}