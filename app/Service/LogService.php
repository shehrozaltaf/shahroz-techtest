<?php

declare(strict_types=1);

namespace App\Service;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

final class LogService
{
    public function log(string $message): void
    {
        $env = $_SERVER['APP_ENV'] ?? 'dev';
        $logFolder = $_SERVER['LOGS_FOLDER'] ?? 'var/log';

        $rootDirectory = dirname(__DIR__, 2);

        $path =sprintf('%s/%s/%s.log', $rootDirectory, $logFolder, $env);

        $log = new Logger('companies');
        $log->pushHandler(new StreamHandler($path));

        $log->debug($message);
    }
}
