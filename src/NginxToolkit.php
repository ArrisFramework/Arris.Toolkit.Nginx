<?php

namespace Arris\Toolkit;

use Exception;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class NginxToolkit implements NginxToolkitInterface
{
    /**
     * @var mixed
     */
    private static $nginx_cache_levels;

    /**
     * @var string
     */
    private static $nginx_cache_root;

    /**
     * @var string
     */
    private static $nginx_cache_key;

    /**
     * @var LoggerInterface
     */
    private static $LOGGER = null;

    /**
     * @var mixed
     */
    private static $is_using_cache;
    
    public static function init($options = [], LoggerInterface $logger = null)
    {
        self::$LOGGER
            = $logger instanceof LoggerInterface
            ? $logger
            : new NullLogger();
        
        self::$is_using_cache = self::setOption($options, 'isUseCache', false);

        self::$nginx_cache_root = self::setOption($options, 'cache_root', null);
        if (is_null(self::$nginx_cache_root)) {
            throw new Exception(__METHOD__ . ': required option `cache_root` not defined!');
        }
        self::$nginx_cache_root = rtrim(self::$nginx_cache_root, DIRECTORY_SEPARATOR);

        self::$nginx_cache_levels = self::setOption($options, 'cache_levels', '1:2');
        self::$nginx_cache_levels = explode(':', self::$nginx_cache_levels);

        self::$nginx_cache_key = self::setOption($options, 'cache_key_format', 'GET|||HOST|PATH');
    }

    public static function clear_nginx_cache(string $url)
    {
        if (self::$is_using_cache == 0) {
            return false;
        }

        if ($url === "/"):
            return self::clear_nginx_cache_entire();
        endif; // endif

        $url_parts = parse_url($url);

        $url_parts['host'] = $url_parts['host'] ?? '';
        $url_parts['path'] = $url_parts['path'] ?? '';

        $cache_key = self::$nginx_cache_key;

        $cache_key = str_replace(
            ['HOST', 'PATH'],
            [$url_parts['host'], $url_parts['path']],
            $cache_key);

        $cache_filename = md5($cache_key);

        $levels = self::$nginx_cache_levels;

        $cache_filepath = self::$nginx_cache_root;

        $offset = 0;

        foreach ($levels as $i => $level) {
            $offset -= $level;
            $cache_filepath .= "/" . substr($cache_filename, $offset, $level);
        }
        
        $cache_filepath .= "/{$cache_filename}";

        if (file_exists($cache_filepath)) {
            $unlink_status = unlink($cache_filepath);
            $msg_cleaned = $unlink_status ? ' and ' : ', but not ';
            self::$LOGGER->notice("NGINX Cache Cleaner: cached data found{$msg_cleaned}cleaned: ", [ $cache_key, $cache_filepath, $unlink_status ]);
        } else {
            $unlink_status = true;
            self::$LOGGER->notice("NGINX Cache Cleaner: cached data not found: ", [ $cache_key, $cache_filepath, null ]);
        }

        return $unlink_status;
    } // -clear_nginx_cache()


    public static function clear_nginx_cache_entire()
    {
        if (self::$is_using_cache == 0) {
            return false;
        }

        $unlink_status = true;

        self::$LOGGER->debug("NGINX Cache Cleaner: requested clean whole cache");

        if (!is_dir(self::$nginx_cache_root))
            throw new Exception("NGINX Cache directory " . self::$nginx_cache_root . " not exist!");

        $dir_content = array_diff(scandir(self::$nginx_cache_root), ['.', '..']);

        foreach ($dir_content as $subdir) {
            $unlink_status = $unlink_status && self::rmdir(self::$nginx_cache_root . DIRECTORY_SEPARATOR . $subdir . '/');
        }

        self::$LOGGER->debug("NGINX Cache Cleaner: whole cache clean status: ", [ self::$nginx_cache_root, $unlink_status ]);

        return $unlink_status;
    }

    public static function rmdir(string $directory): bool
    {
        if (!is_dir($directory)) {
            self::$LOGGER->warning(__METHOD__ . ' throws warning: no such file or directory', [ $directory ]);
            return false;
        }

        $files = array_diff(scandir($directory), ['.', '..']);

        foreach ($files as $file) {
            (is_dir("{$directory}/{$file}"))
                ? self::rmdir("{$directory}/{$file}")
                : unlink("{$directory}/{$file}");
        }
        return rmdir($directory);
    }

    /**
     *
     * @param $options
     * @param $key
     * @param null $default_value
     * @return mixed|null
     */
    private static function setOption($options = [], $key = null, $default_value = null)
    {
        if (!is_array($options)) return $default_value;

        if (is_null($key)) return $default_value;

        return array_key_exists($key, $options) ? $options[ $key ] : $default_value;
    }
}

# -eof-
