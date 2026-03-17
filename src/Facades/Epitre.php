<?php

namespace BlackpigCreatif\Epitre\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \BlackpigCreatif\Epitre\Epitre
 */
class Epitre extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \BlackpigCreatif\Epitre\Epitre::class;
    }
}
