<?php

/*
 * Copyright 2010 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Google\Auth;

use Psr\Cache\CacheItemPoolInterface;
/**
 * A class to implement caching for any object implementing
 * FetchAuthTokenInterface
 */
class FetchAuthTokenCache implements FetchAuthTokenInterface
{
    /**
     * @var FetchAuthTokenInterface
     */
    private $fetcher;
    /**
     * @var array
     */
    private $cacheConfig;
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;
    public function __construct(FetchAuthTokenInterface $fetcher, array $cacheConfig = null, CacheItemPoolInterface $cache)
    {
        $this->fetcher = $fetcher;
        $this->cache = $cache;
        $this->cacheConfig = array_merge(array('lifetime' => 1500, 'prefix' => ''), (array) $cacheConfig);
    }
    /**
     * Implements FetchAuthTokenInterface#fetchAuthToken.
     *
     * Checks the cache for a valid auth token and fetches the auth tokens
     * from the supplied fetcher.
     *
     * @param callable $httpHandler callback which delivers psr7 request
     *
     * @return array the response
     *
     * @throws \Exception
     */
    public function fetchAuthToken(callable $httpHandler = null)
    {
        // Use the cached value if its available.
        //
        // TODO: correct caching; update the call to setCachedValue to set the expiry
        // to the value returned with the auth token.
        //
        // TODO: correct caching; enable the cache to be cleared.
        $cacheKey = $this->fetcher->getCacheKey();
        $cached = $this->getCachedValue($cacheKey);
        if (!empty($cached)) {
            return array('access_token' => $cached);
        }
        $auth_token = $this->fetcher->fetchAuthToken($httpHandler);
        if (isset($auth_token['access_token'])) {
            $this->setCachedValue($cacheKey, $auth_token['access_token']);
        }
        return $auth_token;
    }
    /**
     * @return string
     */
    public function getCacheKey()
    {
        return $this->getFullCacheKey($this->fetcher->getCacheKey());
    }
    /**
     * @return array|null
     */
    public function getLastReceivedToken()
    {
        return $this->fetcher->getLastReceivedToken();
    }
    private $maxKeyLength = 64;
    /**
     * Gets the cached value if it is present in the cache when that is
     * available.
     */
    private function getCachedValue($k)
    {
        if (is_null($this->cache)) {
            return;
        }
        $key = $this->getFullCacheKey($k);
        if (is_null($key)) {
            return;
        }
        $cacheItem = $this->cache->getItem($key);
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }
    }
    /**
     * Saves the value in the cache when that is available.
     */
    private function setCachedValue($k, $v)
    {
        if (is_null($this->cache)) {
            return;
        }
        $key = $this->getFullCacheKey($k);
        if (is_null($key)) {
            return;
        }
        $cacheItem = $this->cache->getItem($key);
        $cacheItem->set($v);
        $cacheItem->expiresAfter($this->cacheConfig['lifetime']);
        return $this->cache->save($cacheItem);
    }
    private function getFullCacheKey($key)
    {
        if (is_null($key)) {
            return;
        }
        $key = $this->cacheConfig['prefix'] . $key;
        // ensure we do not have illegal characters
        $key = preg_replace('|[^a-zA-Z0-9_\\.!]|', '', $key);
        // Hash keys if they exceed $maxKeyLength (defaults to 64)
        if ($this->maxKeyLength && strlen($key) > $this->maxKeyLength) {
            $key = substr(hash('sha256', $key), 0, $this->maxKeyLength);
        }
        return $key;
    }
}