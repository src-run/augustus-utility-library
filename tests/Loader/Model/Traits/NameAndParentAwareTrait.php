<?php

/*
 * This file is part of the `src-run/augustus-utility-library` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace SR\Util\Test\Loader\Model\Traits;

use SR\Util\Test\Loader\Model\Fixture;
use SR\Util\Test\Loader\Model\Package;

trait NameAndParentAwareTrait
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Package|Fixture
     */
    private $parent;

    /**
     * @var mixed[]
     */
    private $data;

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }
}