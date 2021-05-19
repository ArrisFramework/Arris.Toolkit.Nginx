<?php

namespace Arris\Toolkit;

use Exception;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class NginxCacheHelper implements NginxCacheHelperInterface
{
    /**
     * @var mixed
     */
    private $nginx_cache_levels;

    /**
     * @var string
     */
    private $nginx_cache_root;

    /**
     * @var string
     */
    private $nginx_cache_key;

    /**
     * @var LoggerInterface
     */
    private $LOGGER = null;

    /**
     * @var mixed
     */
    private $is_using_cache;
    
    public function __construct(array $options = [], LoggerInterface $logger = null)
    {
        $this->LOGGER
            = $logger instanceof LoggerInterface
            ? $logger
            : new NullLogger();
        
        $this->is_using_cache = self::setOption($options, 'isUseCache', false);
        
        $this->nginx_cache_root = self::setOption($options, 'cache_root', null);
        if (is_null($this->nginx_cache_root)) {
            throw new Exception(__METHOD__ . ': required option `cache_root` not defined!');
        }
        
        $this->nginx_cache_root = rtrim($this->nginx_cache_root, DIRECTORY_SEPARATOR);
        $this->nginx_cache_levels = explode(':',  self::setOption($options, 'cache_levels', '1:2'));
        $this->nginx_cache_key = self::setOption($options, 'cache_key_format', 'GET|||HOST|PATH');
    }
    
    public function clearCache(string $url)
    {
        if ($this->is_using_cache == 0) {
            return false;
        }
    
        if ($url === "/"):
            return $this->clearCacheEntire();
        endif; // endif
    
        $url_parts = parse_url($url);
    
        $url_parts['host'] = $url_parts['host'] ?? '';
        $url_parts['path'] = $url_parts['path'] ?? '';
        $cache_key = $this->nginx_cache_key;
    
        $cache_key = str_replace(
            ['HOST', 'PATH'],
            [$url_parts['host'], $url_parts['path']],
            $cache_key);
        $cache_filename = md5($cache_key);
    
        $levels = $this->nginx_cache_levels;
    
        $cache_filepath = $this->nginx_cache_root;
    
        $offset = 0;
        
        foreach ($levels as $i => $level) {
            $offset -= $level;
            $cache_filepath .= "/" . substr($cache_filename, $offset, $level);
        }
        $cache_filepath .= "/{$cache_filename}";
    
        if (file_exists($cache_filepath)) {
            $unlink_status = unlink($cache_filepath);
            $msg_cleaned = $unlink_status ? ' and ' : ', but not ';
            $this->LOGGER->notice("NGINX Cache Cleaner: cached data found{$msg_cleaned}cleaned: ", [ $cache_key, $cache_filepath, $unlink_status ]);
        } else {
            $unlink_status = true;
            $this->LOGGER->notice("NGINX Cache Cleaner: cached data not found: ", [ $cache_key, $cache_filepath, null ]);
        }
        
        return $unlink_status;
    } // clear_nginx_cache()
    
    public function clearCacheEntire()
    {
        if ($this->is_using_cache == 0) {
            return false;
        }
    
        $unlink_status = true;
    
        $this->LOGGER->debug("NGINX Cache Cleaner: requested clean whole cache");
    
        if (!is_dir($this->nginx_cache_root))
            throw new Exception("NGINX Cache directory " . $this->nginx_cache_root . " not exist!");
    
        $dir_content = array_diff(scandir($this->nginx_cache_root), ['.', '..']);
    
        foreach ($dir_content as $subdir) {
            $unlink_status = $unlink_status && self::rmdir($this->nginx_cache_root . DIRECTORY_SEPARATOR . $subdir . '/', $this->LOGGER);
        }
    
        $this->LOGGER->debug("NGINX Cache Cleaner: whole cache clean status: ", [ $this->nginx_cache_root, $unlink_status ]);
    
        return $unlink_status;
    }
    
    public static function rmdir(string $directory, LoggerInterface $LOGGER): bool
    {
        if (!is_dir($directory)) {
            $LOGGER->warning(__METHOD__ . ' throws warning: no such file or directory', [ $directory ]);
            return false;
        }

        $files = array_diff(scandir($directory), ['.', '..']);

        foreach ($files as $file) {
            (is_dir("{$directory}/{$file}"))
                ? self::rmdir("{$directory}/{$file}", $LOGGER)
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
