<?php

namespace App\Services;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;

class CacheService
{
    private $cache;
    
    public function __construct()
    {
        // Initialize Symfony Cache with filesystem adapter
        $this->cache = new FilesystemAdapter(
            'school_lms',           // namespace
            3600,                   // default lifetime (1 hour)
            __DIR__ . '/../../cache' // cache directory
        );
    }
    
    /**
     * Get cached item
     */
    public function get(string $key, $default = null)
    {
        $item = $this->cache->getItem($key);
        
        if ($item->isHit()) {
            return $item->get();
        }
        
        return $default;
    }
    
    /**
     * Set cache item
     */
    public function set(string $key, $value, int $ttl = 3600): bool
    {
        $item = $this->cache->getItem($key);
        $item->set($value);
        $item->expiresAfter($ttl);
        
        return $this->cache->save($item);
    }
    
    /**
     * Delete cache item
     */
    public function delete(string $key): bool
    {
        return $this->cache->deleteItem($key);
    }
    
    /**
     * Clear all cache
     */
    public function clear(): bool
    {
        return $this->cache->clear();
    }
    
    /**
     * Cache user data
     */
    public function cacheUserData(int $userId, array $userData): bool
    {
        return $this->set("user_data_{$userId}", $userData, 1800); // 30 minutes
    }
    
    /**
     * Get cached user data
     */
    public function getUserData(int $userId): ?array
    {
        return $this->get("user_data_{$userId}");
    }
    
    /**
     * Cache database query results
     */
    public function cacheQuery(string $queryKey, array $results, int $ttl = 600): bool
    {
        return $this->set("query_{$queryKey}", $results, $ttl);
    }
    
    /**
     * Get cached query results
     */
    public function getQueryCache(string $queryKey): ?array
    {
        return $this->get("query_{$queryKey}");
    }
}
