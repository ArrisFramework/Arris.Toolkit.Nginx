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

## Usage 

```
NginxToolkit::clear_nginx_cache($url)
NginxToolkit::clear_nginx_cache_entire()
```
