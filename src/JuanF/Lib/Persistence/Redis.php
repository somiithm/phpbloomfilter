<?php

/**
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * Copyright 2015 Juan Ferrari
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JuanF\Lib\Persistence;

use Predis\Client;

class Redis extends Persistence
{

    protected static $host = '127.0.0.1';
    protected static $port = 6379;
    /* @var $redisInstance Client*/
    protected static $redisInstance;

    /**
     * Init Redis backend. Optionally pass a Redis instance or an array
     * with host, port and options.
     *
     * @param array $parmas
     */
    public static function init($params = null)
    {
        if(!(self::$redisInstance instanceof Client)){
            if($params === null)
                self::$redisInstance = new Client();
            else{
                if(isset($params['host'])) self::$host = $params['host'];

                if(isset($params['host'])) self::$port = $params['port'];

                self::$redisInstance = new Client([
                    'scheme'=>'tcp',
                    'host'=>self::$host,
                    'port'=>self::$port
                ]);
            }
        }

        return new self;
    }

    /**
     * {@inheritDoc}
     * @see \JuanF\Lib\Persistence\PersistenceInterface::get()
     */
    public function get($key, $bits)
    {
        $pipe = self::$redisInstance->pipeline();

        foreach ($bits as $bit) {
            $pipe->getbit($key, $bit);
        }

        return $pipe->execute();
    }

    /**
     * {@inheritDoc}
     * @see \JuanF\Lib\Persistence\PersistenceInterface::set()
     */
    public function set($key, $bit)
    {
        $pipe = self::$redisInstance->pipeline();

        $pipe->setbit($key, $bit, 1);
        return $pipe->execute();
    }
}
