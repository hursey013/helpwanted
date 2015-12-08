<?php
// Initialize SimpleCache
$cache = new Gilbitron\Util\SimpleCache();
$cache->cache_path = HW_CACHE_DIR . '/';

// Initialize GitHub-API
$client = new \Github\Client(
    new \Github\HttpClient\CachedHttpClient(array('cache_dir' => HW_CACHE_DIR))
);
if(HW_GITHUB_API != ''){
  $client->authenticate(HW_GITHUB_API, null, Github\Client::AUTH_HTTP_TOKEN);
}
$paginator = new Github\ResultPager($client);
