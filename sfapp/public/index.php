<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    $appEnv = isset($context['APP_ENV']) && is_string($context['APP_ENV']) ? $context['APP_ENV'] : 'dev';
    $appDebug = isset($context['APP_DEBUG']) ? (bool) $context['APP_DEBUG'] : false;

    return new Kernel($appEnv, $appDebug);
};
