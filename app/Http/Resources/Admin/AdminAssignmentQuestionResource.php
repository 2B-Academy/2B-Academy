<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminAssignmentQuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'position'          => (int) $this->position,
            'type'              => $this->type,
            'score'             => (int) $this->score,
            'question_en'       => $this->question_en,
            'question_ar'       => $this->question_ar,
            'options_en'        => $this->options_en ?? [],
            'options_ar'        => $this->options_ar ?? [],
            'correct_answer_en' => $this->correct_answer_en,
            'correct_answer_ar' => $this->correct_answer_ar,
            'explanation_en'    => $this->explanation_en,
            'explanation_ar'    => $this->explanation_ar,
        ];
    }
}
