<?php

use App\Http\Controllers\apis\QualificationSkillController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Qualification Skill Routes — /api/v1/qualification-skills/*
|--------------------------------------------------------------------------
*/

// Public list (for frontend / Angular dropdowns)
Route::get('qualification-skills/active', [QualificationSkillController::class, 'activeList']);

// Authenticated readers (any logged-in user can browse skills)
Route::middleware('auth.user')->group(function () {
    Route::get('qualification-skills',                         [QualificationSkillController::class, 'index']);
    Route::get('qualification-skills/{qualification_skill}',   [QualificationSkillController::class, 'show']);
});

// Admin only
Route::middleware(['auth.user', 'role:Admin'])->group(function () {
    Route::post('qualification-skills',                          [QualificationSkillController::class, 'store']);
    Route::put('qualification-skills/{qualification_skill}',     [QualificationSkillController::class, 'update']);
    Route::delete('qualification-skills/{qualification_skill}',  [QualificationSkillController::class, 'destroy']);
});
