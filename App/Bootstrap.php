<?php

namespace App;

use App\Services\CacheService;
use App\Services\HttpService;

class Bootstrap
{
    private static $cacheService;
    private static $httpService;
    
    /**
     * Initialize the application
     */
    public static function init()
    {
        // Load Composer autoloader
        require_once __DIR__ . '/../vendor/autoload.php';
        
        // Initialize services
        self::$cacheService = new CacheService();
        self::$httpService = new HttpService();
        
        // Set error reporting
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        // Set timezone
        date_default_timezone_set('Asia/Kolkata');
    }
    
    /**
     * Get cache service instance
     */
    public static function cache(): CacheService
    {
        return self::$cacheService;
    }
    
    /**
     * Get HTTP service instance
     */
    public static function http(): HttpService
    {
        return self::$httpService;
    }
}
