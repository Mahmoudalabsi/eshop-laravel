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
    // Use configured timeout (fallback to 10s) instead of hardcoded 2s
    $this->timeout = (int) config('api.timeout', 10);
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

// داخل نفس الملف ApiService.php

public function get($endpoint, $params = [])
{
    $cacheKey = 'api_cache_' . md5($endpoint . serialize($params));

    return cache()->remember($cacheKey, 3600, function () use ($endpoint, $params) {
        try {
            $response = $this->http()->get($this->baseUrl . $endpoint, $params);
            return $this->handleResponse($response);
        } catch (\Exception $e) {
            // في حال فشل الإنترنت، نعيد مصفوفة فارغة فوراً ولا نعلق الصفحة
            return collect(['data' => [], 'error' => true]);
        }
    });
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

    protected function handleResponse($response)
    {
        if ($response->successful()) {
            return collect($response->json());
        }
        return collect(['data' => [], 'error' => true, 'status' => $response->status()]);
    }
}
