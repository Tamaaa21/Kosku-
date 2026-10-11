<?php

namespace Tests\Feature;

use App\Services\GeocodingService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeocodingIntegrationTest extends TestCase
{
    public function test_geocodes_address_successfully(): void
    {
        Http::fake([
            'https://nominatim.openstreetmap.org/search*' => Http::response([
                [
                    'place_id' => 12345,
                    'lat' => '-7.782893',
                    'lon' => '110.367067',
                    'display_name' => 'Jl. Malioboro, Yogyakarta',
                ],
            ], 200),
        ]);

        $service = app(GeocodingService::class);
        $result = $service->geocodeAddress('Jl. Malioboro, Yogyakarta');

        $this->assertNotNull($result);
        $this->assertEquals(-7.782893, $result['latitude']);
        $this->assertEquals(110.367067, $result['longitude']);

        Http::assertSent(function (Request $request) {
            return str_contains($request->url(), 'nominatim.openstreetmap.org')
                && $request->hasHeader('User-Agent')
                && $request['q'] === 'Jl. Malioboro, Yogyakarta'
                && $request['format'] === 'json'
                && (int) $request['limit'] === 1;
        });
    }

    public function test_returns_null_when_address_not_found(): void
    {
        Http::fake([
            'https://nominatim.openstreetmap.org/search*' => Http::response([], 200),
        ]);

        $service = app(GeocodingService::class);
        $result = $service->geocodeAddress('Alamat Tidak Dikenal 99999');

        $this->assertNull($result);
    }

    public function test_returns_null_for_empty_address(): void
    {
        Http::fake();

        $service = app(GeocodingService::class);
        $result = $service->geocodeAddress('   ');

        $this->assertNull($result);
        Http::assertNothingSent();
    }

    public function test_gracefully_degrades_on_server_error(): void
    {
        Http::fake([
            'https://nominatim.openstreetmap.org/search*' => Http::response('Server Error', 500),
        ]);

        $service = app(GeocodingService::class);
        $result = $service->geocodeAddress('Jl. Kaliurang KM 5');

        $this->assertNull($result);
    }

    public function test_gracefully_degrades_on_connection_timeout(): void
    {
        Http::fake([
            'https://nominatim.openstreetmap.org/search*' => fn () => throw new ConnectionException('Connection timed out after 5000 milliseconds'),
        ]);

        $service = app(GeocodingService::class);
        $result = $service->geocodeAddress('Jl. Gejayan, Sleman');

        $this->assertNull($result);
    }
}
