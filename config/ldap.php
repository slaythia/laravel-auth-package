<?php

return [
    'domain_controller' => '0.0.0.0',//'10.0.2.2', // Server domain (ip or domain)
    'port' => 10389, // Port number
    'base_dn' => ['ou=people,o=sevenSeas', 'ou=people'], // BASE_DN, can add multiple
    'ssl' => false, // Secure access
    'bind_dn' => 'uid=admin,ou=system', // Bind DN
    'bind_dn_password' => 'secret', // Bind DN Password
    'user_name_attribute' => 'uid', // The attribute containing user detail to store ie uid, mail, sAMAccountName
    //'returned_fields' => ['mail', 'cn', 'sn', 'uid', 'sAMAccountName']
];

//return [
//    'domain_controller' => env('DOMAIN_CONTROLLER'), // Server domain (ip or domain)
//    'port' => env('PORT'), // Port number
//    'base_dn' => env('BASE_DN'), // BASE_DN, can add multiple
//    'ssl' => env('SSL'), // Secure access
//    'bind_dn' => env('BIND_DN'), // Bind DN
//    'bind_dn_password' => env('BIND_DN_PASSWORD'), // Bind DN Password
//    'user_name_attribute' => env('USER_NAME_ATTRIBUTE'), // The attribute containing user detail to store ie uid, mail, sAMAccountName
//    //'returned_fields' => ['mail', 'cn', 'sn', 'uid', 'sAMAccountName']
//];
