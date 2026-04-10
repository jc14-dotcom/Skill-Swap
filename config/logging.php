<?php

declare(strict_types=1);

return [
    'path' => BASE_PATH . '/storage/logs/app.log',
    'level' => getenv('LOG_LEVEL') ?: 'debug',
];