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

interface GithubEventInterface
{
    /**
     * @return array list of fields that need to be present to define the event
     */
    public static function fields();

    /**
     * @return string name event
     */
    public static function name();

    /**
     * @param $data array data of the event
     *
     * @return bool validation rule
     */
    public static function isValid($data);

    /**
     * @param $data array data of the event
     *
     * @return GithubEventInterface
     */
    public function createFromData($data);

    /**
     * @return array data of the event
     */
    public function getPayload();
}
