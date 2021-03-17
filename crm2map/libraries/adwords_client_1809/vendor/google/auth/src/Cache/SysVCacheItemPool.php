<?php

/**
 * Copyright 2018 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Google\Auth\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
/**
 * SystemV shared memory based CacheItemPool implementation.
 *
 * This CacheItemPool implementation can be used among multiple processes, but
 * it doesn't provide any locking mechanism. If multiple processes write to
 * this ItemPool, you have to avoid race condition manually in your code.
 */
class SysVCacheItemPool implements CacheItemPoolInterface
{
    const VAR_KEY = 1;
    const DEFAULT_PROJ = 'A';
    const DEFAULT_MEMSIZE = 10000;
    const DEFAULT_PERM = 384;
    /** @var int */
    private $sysvKey;
    /**
     * @var CacheItemInterface[]
     */
    private $items;
    /**
     * @var CacheItemInterface[]
     */
    private $deferredItems;
    /**
     * @var array
     */
    private $options;
    /**
     * Save the current items.
     *
     * @return bool true when success, false upon failure
     */
    private function saveCurrentItems()
    {
        $shmid = shm_attach($this->sysvKey, $this->options['memsize'], $this->options['perm']);
        if ($shmid !== false) {
            $ret = shm_put_var($shmid, $this->options['variableKey'], $this->items);
            shm_detach($shmid);
            return $ret;
        }
        return false;
    }
    /**
     * Load the items from the shared memory.
     *
     * @return bool true when success, false upon failure
     */
    private function loadItems()
    {
        $shmid = shm_attach($this->sysvKey, $this->options['memsize'], $this->options['perm']);
        if ($shmid !== false) {
            $data = @shm_get_var($shmid, $this->options['variableKey']);
            if (!empty($data)) {
                $this->items = $data;
            } else {
                $this->items = array();
            }
            shm_detach($shmid);
            return true;
        }
        return false;
    }
    /**
     * Create a SystemV shared memory based CacheItemPool.
     *
     * @param array $options [optional] {
     *     Configuration options.
     *
     *     @type int $variableKey The variable key for getting the data from
     *           the shared memory. **Defaults to** 1.
     *     @type string $proj The project identifier for ftok. This needs to
     *           be a one character string. **Defaults to** 'A'.
     *     @type int $memsize The memory size in bytes for shm_attach.
     *           **Defaults to** 10000.
     *     @type int $perm The permission for shm_attach. **Defaults to** 0600.
     */
    public function __construct($options = array())
    {
        if (!extension_loaded('sysvshm')) {
            throw \RuntimeException('sysvshm extension is required to use this ItemPool');
        }
        $this->options = $options + array('variableKey' => self::VAR_KEY, 'proj' => self::DEFAULT_PROJ, 'memsize' => self::DEFAULT_MEMSIZE, 'perm' => self::DEFAULT_PERM);
        $this->items = array();
        $this->deferredItems = array();
        $this->sysvKey = ftok(__FILE__, $this->options['proj']);
        $this->loadItems();
    }
    /**
     * {@inheritdoc}
     */
    public function getItem($key)
    {
        $this->loadItems();
        return current($this->getItems(array($key)));
    }
    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = array())
    {
        $this->loadItems();
        $items = array();
        foreach ($keys as $key) {
            $items[$key] = $this->hasItem($key) ? clone $this->items[$key] : new Item($key);
        }
        return $items;
    }
    /**
     * {@inheritdoc}
     */
    public function hasItem($key)
    {
        $this->loadItems();
        return isset($this->items[$key]) && $this->items[$key]->isHit();
    }
    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->items = array();
        $this->deferredItems = array();
        return $this->saveCurrentItems();
    }
    /**
     * {@inheritdoc}
     */
    public function deleteItem($key)
    {
        return $this->deleteItems(array($key));
    }
    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys)
    {
        foreach ($keys as $key) {
            unset($this->items[$key]);
        }
        return $this->saveCurrentItems();
    }
    /**
     * {@inheritdoc}
     */
    public function save(CacheItemInterface $item)
    {
        $this->items[$item->getKey()] = $item;
        return $this->saveCurrentItems();
    }
    /**
     * {@inheritdoc}
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        $this->deferredItems[$item->getKey()] = $item;
        return true;
    }
    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        foreach ($this->deferredItems as $item) {
            if ($this->save($item) === false) {
                return false;
            }
        }
        $this->deferredItems = array();
        return true;
    }
}