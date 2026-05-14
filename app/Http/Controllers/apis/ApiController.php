<?php

namespace App\Http\Controllers\apis;

use App\Http\Traits\ApiResponse;
use Illuminate\Routing\Controller;

abstract class ApiController extends Controller
{
    use ApiResponse;
}
