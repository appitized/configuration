<?php

namespace Appitized\Configuration;

use Illuminate\Support\Facades\Facade;

class ConfigurationFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'configuration';
    }

}
