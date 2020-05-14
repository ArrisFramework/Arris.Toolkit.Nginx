<?php

namespace Arris\Toolkit;

use Exception;

/**
 * Interface NginxToolkitInterface
 *
 * @package Arris\Toolkit
 */
interface NginxToolkitInterface {

    /**
     * Init NGINX Toolkit class
     *
     * @param array $options
     * Options:
     * - isUseCache     -- default(false)               -- использовать ли кэш? -- env(NGINX.CACHE_USE)
     * - cache_root     -- required                     -- путь до кэша nginx   -- env(NGINX.CACHE_PATH)
     * - cache_levels   -- default('1:2')               -- уровни кэша          -- env(NGINX.CACHE_LEVELS)
     * - cache_key_format -- default('GET|||HOST|PATH') -- определение формата ключа -- env(NGINX.CACHE_KEY_FORMAT)
     *
     * Если не нужно логгирование - передаем аргументом logger-а null или AppLogger::scope() с параметром logging = false
     *
     * @param null $logger
     * @throws Exception
     */
    public static function init($options = [], $logger = null);

    /**
     * Очищает nginx-кэш для переданного URL
     * Логгирует всегда
     *
     * @param string $url
     * @return bool
     */
    public static function clear_nginx_cache(string $url);

    /**
     * Полная очистка КЭША NGINX
     *
     * @return bool
     */
    public static function clear_nginx_cache_entire();

    /**
     * Рекурсивно удаляет каталоги по указанному пути
     *
     * @param $directory
     * @return bool
     */
    public static function rmdir(string $directory): bool;
}

# -eof-
