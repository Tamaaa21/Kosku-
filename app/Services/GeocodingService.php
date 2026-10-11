<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GeocodingService
{
    /**
     * Create a new geocoding service instance.
     */
    public function __construct(
        protected ?string $baseUrl = null,
        protected ?string $userAgent = null,
        protected int $timeout = 5
    ) {
        $this->baseUrl = $baseUrl ?? config('services.nominatim.base_url', 'https://nominatim.openstreetmap.org/search');
        $this->userAgent = $userAgent ?? config('services.nominatim.user_agent', 'backend-kos-application/1.0 (contact@backend-kos.ddev.site)');
        $this->timeout = $timeout ?: (int) config('services.nominatim.timeout', 5);
    }

    /**
     * Convert an address to geographic coordinates (latitude and longitude).
     *
     * @return array{latitude: float, longitude: float}|null
     */
    public function geocodeAddress(string $address): ?array
    {
        $trimmedAddress = trim($address);

        if ($trimmedAddress === '') {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => $this->userAgent,
            ])
                ->timeout($this->timeout)
                ->get($this->baseUrl, [
                    'q' => $trimmedAddress,
                    'format' => 'json',
                    'limit' => 1,
                ]);

            if (! $response->successful()) {
                Log::warning('Geocoding service returned non-successful response.', [
                    'status' => $response->status(),
                    'address' => $trimmedAddress,
                ]);

                return null;
            }

            $data = $response->json();

            if (! empty($data) && isset($data[0]['lat'], $data[0]['lon'])) {
                return [
                    'latitude' => (float) $data[0]['lat'],
                    'longitude' => (float) $data[0]['lon'],
                ];
            }

            return null;
        } catch (Throwable $exception) {
            Log::warning('Geocoding service encountered an error or timed out.', [
                'message' => $exception->getMessage(),
                'address' => $trimmedAddress,
            ]);

            return null;
        }
    }
}
