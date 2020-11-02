<?php

/*
 * Copyright (c) 2015 Lp digital system
 *
 * This file is part of Github Parser library.
 *
 * Github Parser is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Github Parser is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Github Parser. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Lpdigital\Github\EventType;

use Lpdigital\Github\Entity\Integration;

abstract class AbstractEventType implements GithubEventInterface
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var Integration|null
     */
    public $integration;

    public function getPayload()
    {
        return $this->data;
    }

    public static function fields()
    {
        return [];
    }

    public static function name()
    {
        return get_called_class();
    }

    public static function isValid($data)
    {
        foreach (static::fields() as $field) {
            if ((isset($data[$field]) || array_key_exists($field, $data)) === false) {
                return false;
            }
        }

        return true;
    }

    public function createFromData($data)
    {
        $this->data = $data;

        $this->integration = isset($data['installation']) ? Integration::createFromData($data['installation']) : null;
    }
}
