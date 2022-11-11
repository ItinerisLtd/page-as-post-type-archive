<?php

namespace Itineris\PageAsPostTypeArchive;

use Illuminate\Support\Facades\Facade;

class ItinerisPageAsPostTypeArchiveFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'itinerispageasposttypearchive';
    }
}
