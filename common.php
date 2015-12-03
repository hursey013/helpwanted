<?php
// Initialize SimpleCache
$cache = new Gilbitron\Util\SimpleCache();
$cache->cache_path = HW_CACHE_DIR . '/';
$cache->cache_time = HW_CACHE_TIME;

// Initialize GitHub-API
$github_cache  = new Github\HttpClient\CachedHttpClient(array(
    'cache_dir' => HW_CACHE_DIR
));
$client = new Github\Client($github_cache);
$client->authenticate(HW_GITHUB_API, null, Github\Client::AUTH_HTTP_TOKEN);
$paginator = new Github\ResultPager($client);
