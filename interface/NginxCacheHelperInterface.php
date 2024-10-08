<?php

namespace Arris\Toolkit;

use Exception;
use Psr\Log\LoggerInterface;

/**
 * Interface NginxToolkitInterface
 *
 * @package Arris\Toolkit
 */
interface NginxCacheHelperInterface {
    
    /**
     * Init NGINX Toolkit static class
     * Инициализирует NGINX Toolkit static class
     *
     * @param array $options
     * Options:
     * - isUseCache     -- default(false)               -- использовать ли кэш? -- env(NGINX.CACHE_USE)
     * - cache_root     -- required                     -- путь до кэша nginx   -- env(NGINX.CACHE_PATH)
     * - cache_levels   -- default('1:2')               -- уровни кэша          -- env(NGINX.CACHE_LEVELS)
     * - cache_key_format -- default('GET|||HOST|PATH') -- определение формата ключа -- env(NGINX.CACHE_KEY_FORMAT)
     *
     * @param LoggerInterface $logger - передаем null или AppLogger::scope()
     *
     * @throws Exception
     */
    public function __construct(array $options = [], LoggerInterface $logger = null);

    /**
     * Clear NGINX Cache record for given URL
     * Очищает nginx-кэш для переданного URL
     *
     * @param string $url
     *
     * @return bool
     *
     * @throws Exception
     */
    public function clearCache(string $url): bool;

    /**
     * Clear entire NGINX Cache
     * Полная очистка КЭША NGINX
     *
     * @return bool
     *
     * @throws Exception
     */
    public function clearCacheEntire(): bool;

    /**
     * Рекурсивно удаляет каталоги по указанному пути
     *
     * @param string $directory
     * @param LoggerInterface $LOGGER
     * @return bool
     */
    public static function rmdir(string $directory, LoggerInterface $LOGGER): bool;
}

# -eof-
