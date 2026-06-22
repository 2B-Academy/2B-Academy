<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\apis\ApiController;

/**
 * Shared base for every mobile API controller.
 *
 * Lives in its own file so that future cross-cutting behaviour
 * (rate-limit headers, mobile-specific exception mapping, etc.) can
 * be hung off a single base class without touching every endpoint.
 */
abstract class MobileBaseController extends ApiController
{
    //
}
