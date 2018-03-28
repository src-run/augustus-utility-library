<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Utilities\Test\Fixture;

/**
 * Class FixtureTrait.
 */
trait FixtureTrait
{
    public $propPublic = 'propPublic';
    protected $propProtecte = 'propProtecte';
    private $propPrivate = 'propPrivate';

    public function methodPublic()
    {
        return __METHOD__;
    }

    protected function methodProtected()
    {
        return __METHOD__;
    }

    private function methodPrivate()
    {
        return __METHOD__;
    }
}

/* EOF */