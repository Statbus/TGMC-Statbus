<?php

use App\Enum\ApplicationEnvironment;

ini_set('xdebug.var_display_max_depth', 4);
ini_set('xdebug.var_display_max_data', -1);

$environment = ApplicationEnvironment::LOCAL;

$settings['debug'] = $environment->enableDebug($environment);

$settings['error'] = [
    'display_error_details' => $environment->enableDebug($environment),
    'log_errors' => $environment->enableDebug($environment),
    'log_error_details' => $environment->enableDebug($environment),
];

$settings['twig']['options']['debug'] = $environment->enableDebug($environment);

$settings['auth'] = [
    'forum' => [
      'clientId' => '3',
      'clientSecret' => 'vnEMecFZZsMcPiTiRwsNT1cosuvYb7FzICu33w',
      'scope' => ['user','user.linked_accounts'],
    ]
  ];

return $settings;
