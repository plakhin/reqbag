<?php

declare(strict_types=1);

use App\Models\Bag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Plakhin\RequestChronicle\Enums\HttpMethod;
use Plakhin\RequestChronicle\Http\Middleware\SaveRequest;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;
use function Pest\Laravel\withHeaders;

beforeEach(function (): void {
    $this->bag = Bag::factory()->create();
    $this->host = $this->bag->slug.'.'.config('app.central_domain');
    $this->baseUrl = config('app.scheme').$this->host;
    $this->table = $this->bag->requests()->getRelated()->getTable();
});

it('is correctly attached', function (): void {
    expect(
        Route::getRoutes()->match(
            Request::create(config('app.scheme').'non-existent-bag.'.config('app.central_domain'))
        )->gatherMiddleware()
    )->toContain(SaveRequest::class.':bag');
});

it('returns 404 for non-existent bag subdomains', function (): void {
    assertDatabaseMissing($this->bag->getTable(), ['slug' => '']);
    get(config('app.scheme').'non-existent-bag.'.config('app.central_domain'))->assertNotFound();
});

it('correctly saves GET request', function (): void {
    withHeaders(['X-First' => 'foo'])->get($this->baseUrl.'/test?foo=bar')->assertOk();

    assertDatabaseCount($this->table, 1);
    assertDatabaseHas($this->table, [
        'model_id' => $this->bag->getKey(),
        'model_type' => $this->bag::class,
        'method' => HttpMethod::GET,
        'url' => $this->baseUrl.'/test?foo=bar',
        'headers' => json_encode([
            'host' => [$this->host],
            'user-agent' => ['Symfony'],
            'accept' => ['text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'],
            'accept-language' => ['en-us,en;q=0.5'],
            'accept-charset' => ['ISO-8859-1,utf-8;q=0.7,*;q=0.7'],
            'x-first' => ['foo'],
        ]),
        'payload' => json_encode([]),
        'raw' => "GET /test?foo=bar HTTP/1.1\r\nAccept:          text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\nAccept-Charset:  ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\nAccept-Language: en-us,en;q=0.5\r\nHost:            {$this->host}\r\nUser-Agent:      Symfony\r\nX-First:         foo\r\n\r\n",
        'ips' => json_encode(['127.0.0.1']),
    ]);
});

it('correctly saves POST request', function (): void {
    withHeaders(['X-First' => 'foo'])->post($this->baseUrl.'/test?foo=bar', ['baz' => 'qux'])->assertOk();

    $this->assertDatabaseCount($this->table, 1)->assertDatabaseHas($this->table, [
        'model_id' => $this->bag->getKey(),
        'model_type' => $this->bag::class,
        'method' => HttpMethod::POST,
        'url' => $this->baseUrl.'/test?foo=bar',
        'headers' => json_encode([
            'host' => [$this->host],
            'user-agent' => ['Symfony'],
            'accept' => ['text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'],
            'accept-language' => ['en-us,en;q=0.5'],
            'accept-charset' => ['ISO-8859-1,utf-8;q=0.7,*;q=0.7'],
            'x-first' => ['foo'],
            'content-type' => ['application/x-www-form-urlencoded'],
        ]),
        'payload' => json_encode(['baz' => 'qux']),
        'raw' => "POST /test?foo=bar HTTP/1.1\r\nAccept:          text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\nAccept-Charset:  ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\nAccept-Language: en-us,en;q=0.5\r\nContent-Type:    application/x-www-form-urlencoded\r\nHost:            {$this->host}\r\nUser-Agent:      Symfony\r\nX-First:         foo\r\n\r\n",
        'ips' => json_encode(['127.0.0.1']),
    ]);
});
