<?php

/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\FileChecksum\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'routes' => [
        ['name' => 'home#index', 'url' => '/', 'verb' => 'GET'],
        [
            'name' => 'file_checksum_api#start_scanning',
            'url' => '/api/statistic/startscanning{folder}',
            'verb' => 'GET',
            'defaults' => array('folder' => 'root')
        ],
        [
            'name' => 'file_checksum_api#get_checksum_statistic_status',
            'url' => '/api/statistic/status',
            'verb' => 'GET'
        ],
        [
            'name' => 'file_checksum_api#get_checksum_statistic',
            'url' => '/api/statistic',
            'verb' => 'GET'
        ],
    ]
];