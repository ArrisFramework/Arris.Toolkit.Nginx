# Arris.nginx-toolkit

```
composer require karelwintersky/arris.nginx-toolkit 
```

# How To

Init class first:
```
use Arris\Toolkit\NginxToolkit;

NginxToolkit::init($options, $logger)
``` 

- `isLogging` - default(false)
- `isUseCache` - default(false)
- `cache_root` - required
- `cache_levels` - default(`'1:2'`)
- `cache_key_format` - default(`'GET|||HOST|PATH'`)

Later:

```
NginxToolkit::clear_nginx_cache($url)

NginxTookit::clear_nginx_cache_entire()


```
