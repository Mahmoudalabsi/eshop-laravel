<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ApiService
{
    protected $baseUrl;
    protected $timeout;

    public function __construct()
    {
        $this->baseUrl = config('api.base_url');
        $this->timeout = config('api.timeout', 10);
    }

    protected function http()
    {
        $http = Http::timeout($this->timeout);

        // Add authorization header if token exists
        if (Session::has('api_token')) {
            $http = $http->withToken(Session::get('api_token'));
        }

        return $http;
    }

    public function get($endpoint, $params = [])
    {
        try {
            $response = $this->http()->get($this->baseUrl . $endpoint, $params);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            return collect(['data' => [], 'error' => true]);
        }
    }

    public function post($endpoint, $data = [], $multipart = false)
    {
        try {
            $http = $this->http();

            // Handle multipart form data for file uploads
            if ($multipart) {
                $formData = [];
                foreach ($data as $key => $value) {
                    if ($value instanceof \Illuminate\Http\UploadedFile) {
                        $formData[] = [
                            'name' => $key,
                            'contents' => fopen($value->getRealPath(), 'r'),
                            'filename' => $value->getClientOriginalName(),
                        ];
                    } else {
                        $formData[] = [
                            'name' => $key,
                            'contents' => $value,
                        ];
                    }
                }
                $response = $http->asMultipart()->post($this->baseUrl . $endpoint, $formData);
            } else {
                $response = $http->post($this->baseUrl . $endpoint, $data);
            }

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            return collect(['data' => [], 'error' => true]);
        }
    }

    public function put($endpoint, $data = [])
    {
        try {
            $response = $this->http()->put($this->baseUrl . $endpoint, $data);
            return $this->handleResponse($response);
        } catch (\Exception $e) {
            return collect(['data' => [], 'error' => true]);
        }
    }

    public function delete($endpoint)
    {
        try {
            $response = $this->http()->delete($this->baseUrl . $endpoint);
            return $this->handleResponse($response);
        } catch (\Exception $e) {
            return collect(['data' => [], 'error' => true]);
        }
    }

    protected function handleResponse($response)
    {
        if ($response->successful()) {
            return collect($response->json());
        }

        // Try to extract error message from API response
        $apiData = [];
        try {
            $apiData = (array) $response->json();
        } catch (\Exception $e) {
            // Response isn't JSON
        }

        return collect(array_merge([
            'data' => [],
            'error' => true,
            'status' => $response->status()
        ], $apiData)); // Merge API response so message is preserved
    }
}
