<?php
/*
 * This file is part of the TranoUtilsBundle package.
 *
 * (c) atety <https://www.atety.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Trano\UtilsBundle\Util;

/**
 * @author     ranaivo.razakanirina@atety.com
 */
class Env
{
    public function getEnv($envkey = '')
    {
        if (array_key_exists($envkey, $_ENV)) {
            return $_ENV[$envkey];
        } // if
        return '';
    } // getEnv
}
