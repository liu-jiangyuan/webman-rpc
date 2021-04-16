<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

return [

    'default' => 'mysql',

    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => getenv('DB_HOST'),
            'port' => getenv('DB_PORT'),
            'database' => 'webman',
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'unix_socket' => getenv(''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
//        'mongodb' => [
//            'driver' => 'mongodb',
//            'host' => getenv('MONGO_HOST'),
//            'port' =>  getenv('MONGO_PORT'),
//            'database' => getenv('MONGO_DB'),
//            'username' => getenv('MONGO_USER'),
//            'password' => getenv('MONOGO_PASSWD'),
//            'options' => [
//                'database' => env('DB_AUTHENTICATION_DATABASE', 'admin'), // required with Mongo 3+
//            ],
//        ],
    ],
];
