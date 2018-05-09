<?php

namespace RikSomers\OMDB\Facades;

use Illuminate\Support\Facades\Facade;

class OMDBFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return 'omdb';
    }
}
