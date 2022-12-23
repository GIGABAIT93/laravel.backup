<?php

namespace Laravel\Backup;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Laravel\Backup\Skeleton\SkeletonClass
 */
class BackupFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'backup';
    }
}
