<?php

namespace App\Http\Requests\Api\Admin;

/**
 * Update validation is identical to create. We keep a separate class so we
 * can diverge in the future without touching the store request.
 */
class AdminAssignmentUpdateRequest extends AdminAssignmentStoreRequest
{
}
