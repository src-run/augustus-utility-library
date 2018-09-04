<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Tests\Utilities\Resources\Fixtures;

class ClassFixture
{
    /**
     * @var string
     */
    protected $protectedProperty = 'protectedProperty';

    /**
     * @var string
     */
    private $privateProperty = 'privateProperty';

    /**
     * @return string
     */
    protected function getProtectedProperty()
    {
        return $this->protectedProperty;
    }

    /**
     * @return string
     */
    private function getPrivateProperty()
    {
        return $this->privateProperty;
    }
}
