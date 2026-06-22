<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * Per-enum Swagger documentation for `GET /api/v1/enums/{name}`.
 *
 * Why a dedicated file:
 *   - The real HTTP route is generic (`/enums/{name}`) so we keep one
 *     handler in `EnumController::show()`.
 *   - But the team wants each enum to show up in Swagger UI as its own
 *     named endpoint (e.g. `GET /enums/course_type`, `GET /enums/cohort_status`),
 *     not buried behind a `{name}` placeholder.
 *
 * To add a new enum:
 *   1. Append it to {@see \App\Enums\EnumRegistry::MAP}.
 *   2. Add localized labels in `resources/lang/{en,ar}/enums.php`.
 *   3. Add an `@OA\Get` block below.
 *   4. Run `php artisan l5-swagger:generate`.
 *
 * @OA\Get(
 *     path="/enums/course_type",
 *     tags={"Enums"},
 *     operationId="enums.course_type",
 *     summary="Course delivery type options.",
 *     description="Allowed values: online, offline, hybrid, external_link.",
 *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
 *     @OA\Response(response=200, description="Course type options.", @OA\JsonContent(allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/EnumOptionList"))
 *     }))
 * )
 *
 * @OA\Get(
 *     path="/enums/course_status",
 *     tags={"Enums"},
 *     operationId="enums.course_status",
 *     summary="Course list filter tabs.",
 *     description="Allowed values: all, pending, active, upcoming, inactive.",
 *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
 *     @OA\Response(response=200, description="Course status options.", @OA\JsonContent(allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/EnumOptionList"))
 *     }))
 * )
 *
 * @OA\Get(
 *     path="/enums/cohort_status",
 *     tags={"Enums"},
 *     operationId="enums.cohort_status",
 *     summary="Cohort / section status options.",
 *     description="Allowed values: scheduled, active, completed, inactive.",
 *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
 *     @OA\Response(response=200, description="Cohort status options.", @OA\JsonContent(allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/EnumOptionList"))
 *     }))
 * )
 *
 * @OA\Get(
 *     path="/enums/module_content_type",
 *     tags={"Enums"},
 *     operationId="enums.module_content_type",
 *     summary="Course module content type options.",
 *     description="Allowed values: video, document, article, link.",
 *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
 *     @OA\Response(response=200, description="Module content type options.", @OA\JsonContent(allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/EnumOptionList"))
 *     }))
 * )
 *
 * @OA\Get(
 *     path="/enums/module_learner_scope",
 *     tags={"Enums"},
 *     operationId="enums.module_learner_scope",
 *     summary="Course module learner scope options.",
 *     description="Allowed values: all, cohort.",
 *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
 *     @OA\Response(response=200, description="Module learner scope options.", @OA\JsonContent(allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/EnumOptionList"))
 *     }))
 * )
 *
 * @OA\Get(
 *     path="/enums/resource_type",
 *     tags={"Enums"},
 *     operationId="enums.resource_type",
 *     summary="LMS resource (knowledge base) type options.",
 *     description="Allowed values: article, link, file.",
 *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
 *     @OA\Response(response=200, description="Resource type options.", @OA\JsonContent(allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/EnumOptionList"))
 *     }))
 * )
 *
 * @OA\Get(
 *     path="/enums/certificate_basis",
 *     tags={"Enums"},
 *     operationId="enums.certificate_basis",
 *     summary="Certificate award basis options (ships descriptions).",
 *     description="Allowed values: attendance, score, both. Each option ships a `description` field used by the radio cards on the Platform Settings page.",
 *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
 *     @OA\Response(response=200, description="Certificate basis options.", @OA\JsonContent(allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/EnumOptionList"))
 *     }))
 * )
 *
 * @OA\Get(
 *     path="/enums/locale",
 *     tags={"Enums"},
 *     operationId="enums.locale",
 *     summary="Supported UI locale options.",
 *     description="Allowed values: en, ar.",
 *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
 *     @OA\Response(response=200, description="Locale options.", @OA\JsonContent(allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/EnumOptionList"))
 *     }))
 * )
 *
 * @OA\Get(
 *     path="/enums/cohort_scope",
 *     tags={"Enums"},
 *     operationId="enums.cohort_scope",
 *     summary="Quiz / assignment cohort scope options.",
 *     description="Allowed values: all, specific.",
 *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
 *     @OA\Response(response=200, description="Cohort scope options.", @OA\JsonContent(allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/EnumOptionList"))
 *     }))
 * )
 *
 * @OA\Get(
 *     path="/enums/question_type",
 *     tags={"Enums"},
 *     operationId="enums.question_type",
 *     summary="Quiz / assignment question type options.",
 *     description="Allowed values: mcq, yes_no, open, reorder.",
 *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
 *     @OA\Response(response=200, description="Question type options.", @OA\JsonContent(allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/EnumOptionList"))
 *     }))
 * )
 *
 * @OA\Get(
 *     path="/enums/dashboard_range",
 *     tags={"Enums"},
 *     operationId="enums.dashboard_range",
 *     summary="Admin dashboard time-range tab options.",
 *     description="Allowed values: week, month, quarter, year.",
 *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
 *     @OA\Response(response=200, description="Dashboard range options.", @OA\JsonContent(allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/EnumOptionList"))
 *     }))
 * )
 *
 * @OA\Get(
 *     path="/enums/role_color",
 *     tags={"Enums"},
 *     operationId="enums.role_color",
 *     summary="Role badge color swatch options.",
 *     description="Allowed values: teal, green, orange, red, blue.",
 *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
 *     @OA\Response(response=200, description="Role color options.", @OA\JsonContent(allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/EnumOptionList"))
 *     }))
 * )
 *
 * @OA\Get(
 *     path="/enums/role_guard",
 *     tags={"Enums"},
 *     operationId="enums.role_guard",
 *     summary="Spatie role guard options.",
 *     description="Allowed values: admin, web.",
 *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
 *     @OA\Response(response=200, description="Role guard options.", @OA\JsonContent(allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/EnumOptionList"))
 *     }))
 * )
 *
 * @OA\Get(
 *     path="/enums/inbox_tab",
 *     tags={"Enums"},
 *     operationId="enums.inbox_tab",
 *     summary="Inbox / messages tab filter options.",
 *     description="Allowed values: all, unread, sent, resolved.",
 *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
 *     @OA\Response(response=200, description="Inbox tab options.", @OA\JsonContent(allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/EnumOptionList"))
 *     }))
 * )
 *
 * @OA\Get(
 *     path="/enums/user_status",
 *     tags={"Enums"},
 *     operationId="enums.user_status",
 *     summary="Admin user account status options.",
 *     description="Allowed values: active, inactive, deactivated.",
 *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
 *     @OA\Response(response=200, description="User status options.", @OA\JsonContent(allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/EnumOptionList"))
 *     }))
 * )
 *
 * @OA\Get(
 *     path="/enums/learner_type",
 *     tags={"Enums"},
 *     operationId="enums.learner_type",
 *     summary="Learner enrollment type options.",
 *     description="Allowed values: online, offline, hybrid.",
 *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
 *     @OA\Response(response=200, description="Learner type options.", @OA\JsonContent(allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/EnumOptionList"))
 *     }))
 * )
 *
 * @OA\Get(
 *     path="/enums/lifecycle_status",
 *     tags={"Enums"},
 *     operationId="enums.lifecycle_status",
 *     summary="Quiz / assignment lifecycle status options.",
 *     description="Allowed values: draft, active.",
 *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
 *     @OA\Response(response=200, description="Lifecycle status options.", @OA\JsonContent(allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/EnumOptionList"))
 *     }))
 * )
 *
 * @OA\Get(
 *     path="/enums/enrollment_status",
 *     tags={"Enums"},
 *     operationId="enums.enrollment_status",
 *     summary="Learner enrollment progress status options.",
 *     description="Allowed values: not_started, in_progress, completed. Used by the Learners tab on the course detail page.",
 *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
 *     @OA\Response(response=200, description="Enrollment status options.", @OA\JsonContent(allOf={
 *         @OA\Schema(ref="#/components/schemas/SuccessResponse"),
 *         @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/EnumOptionList"))
 *     }))
 * )
 */
class EnumEndpoints
{
}
