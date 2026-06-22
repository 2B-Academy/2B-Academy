<?php

namespace App\Http\Requests\Api\Admin;

/**
 * Validation rules are identical to the Store request; sharing the parent
 * keeps the two end-points behaviourally consistent and avoids drift.
 */
class AdminQuizUpdateRequest extends AdminQuizStoreRequest
{
}
