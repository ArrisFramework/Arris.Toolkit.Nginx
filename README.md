# Arris.nginx-toolkit

```
composer require karelwintersky/arris.nginx-toolkit 
```

# How To

## Init class first

```
use Arris\Toolkit\NginxToolkit;

NginxToolkit::init($options, $logger)
```

Options are hash-array of:  
- `isUseCache` - использовать ли кэш? - default = false
- `cache_root` - путь до кэша nginx - required
- `cache_levels` - уровни кэша - default = '1:2'
- `cache_key_format` - определение формата ключа - default = 'GET|||HOST|PATH'

Logger can be: 
- null, 
- monolog logger instance 
- AppLogger::scope() call

## Methods

```
/**
 * Init NGINX Toolkit static class
 * Инициализирует NGINX Toolkit static class
 *
 * @param array $options (default [])
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
init($options = [], $logger = null);
```

```
/**
 * Clear NGINX Cache record for given URL
 * Очищает nginx-кэш для переданного URL
 *
 * @param string $url
 * @return bool
 * @throws Exception
 */
clear_nginx_cache(string $url);
```

```
/**
 * Clear entire NGINX Cache
 * Полная очистка КЭША NGINX
 *
 * @return bool
 * @throws Exception
 */
clear_nginx_cache_entire();
```