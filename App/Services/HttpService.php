<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class HttpService
{
    private $client;
    
    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'verify' => false, // For development only
            'headers' => [
                'User-Agent' => 'School-LMS/1.0',
                'Accept' => 'application/json',
            ]
        ]);
    }
    
    /**
     * Send GET request
     */
    public function get(string $url, array $options = []): ?array
    {
        try {
            $response = $this->client->get($url, $options);
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            error_log("HTTP GET Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Send POST request
     */
    public function post(string $url, array $data = [], array $options = []): ?array
    {
        try {
            $options['json'] = $data;
            $response = $this->client->post($url, $options);
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            error_log("HTTP POST Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Send notification to external service
     */
    public function sendNotification(string $message, string $recipient): bool
    {
        // Example: Send SMS or email notification via external API
        $data = [
            'message' => $message,
            'recipient' => $recipient,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // Replace with actual notification service URL
        $result = $this->post('https://api.notification-service.com/send', $data);
        
        return $result !== null;
    }
    
    /**
     * Fetch weather data for school events
     */
    public function getWeatherData(string $location): ?array
    {
        // Example: Fetch weather data for outdoor school events
        $url = "https://api.openweathermap.org/data/2.5/weather";
        $params = [
            'q' => $location,
            'appid' => 'YOUR_API_KEY', // Replace with actual API key
            'units' => 'metric'
        ];
        
        return $this->get($url, ['query' => $params]);
    }
    
    /**
     * Validate email address via external service
     */
    public function validateEmail(string $email): bool
    {
        // Example: Use external email validation service
        $result = $this->get("https://api.email-validator.com/validate/{$email}");
        
        return $result['valid'] ?? false;
    }
}
