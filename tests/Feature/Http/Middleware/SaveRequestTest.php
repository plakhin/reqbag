<?php

use App\Enums\HttpMethod;
use App\Http\Middleware\SaveRequest;
use App\Models\Bag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;
use function Pest\Laravel\withHeaders;

beforeEach(function () {
    $this->bag = Bag::factory()->create();
    $this->host = $this->bag->slug.'.'.config('app.central_domain');
    $this->baseUrl = config('app.scheme').$this->host;
    $this->table = $this->bag->requests()->getRelated()->getTable();
});

it('is correctly attached', function () {
    expect(
        Route::getRoutes()->match(
            Request::create(config('app.scheme').'non-existent-bag.'.config('app.central_domain'))
        )->gatherMiddleware()
    )->toContain(
        SaveRequest::class
    );
});

it('returns 404 for non-existent bag subdomains', function () {
    assertDatabaseMissing($this->bag->getTable(), ['slug' => '']);
    get(config('app.scheme').'non-existent-bag.'.config('app.central_domain'))->assertNotFound();
});

it('correctly saves GET request', function () {
    withHeaders(['X-First' => 'foo'])->get($this->baseUrl.'/test?foo=bar')->assertOk();

    $this->assertDatabaseCount($this->table, 1)->assertDatabaseHas($this->table, [
        'bag_id' => $this->bag->id,
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

it('correctly saves POST request', function () {
    withHeaders(['X-First' => 'foo'])->post($this->baseUrl.'/test?foo=bar', ['baz' => 'qux'])->assertOk();

    $this->assertDatabaseCount($this->table, 1)->assertDatabaseHas($this->table, [
        'bag_id' => $this->bag->id,
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
