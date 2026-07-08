<?php

namespace App\Events;

use App\Models\UserExam;
use Illuminate\Foundation\Events\Dispatchable;

class QuizSubmitted
{
    use Dispatchable;

    public function __construct(
        public readonly UserExam $userExam,
    ) {}
}
