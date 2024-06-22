<?php

namespace App\Enums;

enum HttpMethod: int
{
    case GET = 1;
    case HEAD = 2;
    case POST = 3;
    case PUT = 4;
    case DELETE = 5;
    case CONNECT = 6;
    case OPTIONS = 7;
    case PATCH = 8;
    case PURGE = 9;
    case TRACE = 10;
}
