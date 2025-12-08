<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

// Global Configuration Override
return [
    'db' => [
        'driver'   => 'Pdo_Pgsql',
        'hostname' => '172.31.19.126',
        'database' => 'ssf_dashboard',
        'username' => 'postgres',
        'password' => 'admin',
    ],
    // 'oracle' => [
    //     'username'         => 'powerbi',
    //     'password'         => 'SSF@PBI2025',
    //     'connection_string' => "(DESCRIPTION=
    //                             (ADDRESS=(PROTOCOL=TCP)(HOST=ssf-middleware.cutkg6642h4j.us-east-1.rds.amazonaws.com)(PORT=1521))
    //                             (CONNECT_DATA=(SERVICE_NAME=SSFMID)))",
    // ],
    'oracle' => [
        'username'         => 'SAPIENS',
        'password'         => 'SAPIENS',
        'connection_string' => "(DESCRIPTION=
                                (ADDRESS=(PROTOCOL=TCP)(HOST=ssf-prod.cutkg6642h4j.us-east-1.rds.amazonaws.com)(PORT=1521))
                                (CONNECT_DATA=(SERVICE_NAME=SSFPROD)))",
    ],
];