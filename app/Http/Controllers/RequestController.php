<?php

namespace App\Http\Controllers;

use App\Enums\HttpMethod;
use App\Models\Bag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RequestController extends Controller
{
    public function __invoke(Request $request, Bag $bag): Response
    {
        $bag->requests()->create([
            'method' => HttpMethod::{$request->method()},
            'url' => $request->fullUrl(),
            'headers' => array_diff_key($request->header(), ['cookie' => []]),
            'post' => $request->post(),
            'raw' => (string) $request,
            'ips' => $request->ips(),
        ]);

        return response()->noContent();
    }
}
