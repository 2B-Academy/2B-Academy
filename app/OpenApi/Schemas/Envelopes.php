<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * Reusable response envelopes, primitives, parameters, and responses
 * shared by every endpoint in the API.
 *
 * --------------------------------------------------------------------
 * Schemas
 * --------------------------------------------------------------------
 *
 * @OA\Schema(
 *     schema="TranslatedString",
 *     type="object",
 *     required={"ar","en"},
 *     description="Bilingual string used for translatable INPUT bodies. Response bodies return a single localized string based on the request locale.",
 *     @OA\Property(property="ar", type="string", example="نص عربي"),
 *     @OA\Property(property="en", type="string", example="English text")
 * )
 *
 * @OA\Schema(
 *     schema="SuccessResponse",
 *     type="object",
 *     @OA\Property(property="status",  type="string", example="success"),
 *     @OA\Property(property="message", type="string", example="Data retrieved successfully."),
 *     @OA\Property(property="result")
 * )
 *
 * @OA\Schema(
 *     schema="EmptyResponse",
 *     type="object",
 *     @OA\Property(property="status",  type="string", example="success"),
 *     @OA\Property(property="message", type="string", example="Operation completed successfully."),
 *     @OA\Property(property="result",  type="object", nullable=true, example=null)
 * )
 *
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     @OA\Property(property="status",  type="string", example="error"),
 *     @OA\Property(property="message", type="string", example="Something went wrong.")
 * )
 *
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *     type="object",
 *     @OA\Property(property="status",  type="string", example="error"),
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         additionalProperties=@OA\AdditionalProperties(
 *             type="array",
 *             @OA\Items(type="string")
 *         ),
 *         example={"name": {"The name field is required."}}
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="PaginationMeta",
 *     type="object",
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="from",         type="integer", example=1,  nullable=true),
 *     @OA\Property(property="last_page",    type="integer", example=10),
 *     @OA\Property(property="per_page",     type="integer", example=15),
 *     @OA\Property(property="to",           type="integer", example=15, nullable=true),
 *     @OA\Property(property="total",        type="integer", example=148)
 * )
 *
 * @OA\Schema(
 *     schema="PaginationLinks",
 *     type="object",
 *     @OA\Property(property="first", type="string", nullable=true, example="https://example.com/api/v1/items?page=1"),
 *     @OA\Property(property="last",  type="string", nullable=true, example="https://example.com/api/v1/items?page=10"),
 *     @OA\Property(property="prev",  type="string", nullable=true, example=null),
 *     @OA\Property(property="next",  type="string", nullable=true, example="https://example.com/api/v1/items?page=2")
 * )
 *
 * --------------------------------------------------------------------
 * Parameters
 * --------------------------------------------------------------------
 *
 * @OA\Parameter(
 *     parameter="AcceptLanguage",
 *     name="Accept-Language",
 *     in="header",
 *     required=false,
 *     description="Locale for localized strings. Accepts `ar` or `en`. Defaults to `ar`.",
 *     @OA\Schema(type="string", enum={"ar","en"}, default="ar")
 * )
 *
 * @OA\Parameter(
 *     parameter="EmployeeCode",
 *     name="Employee-Code",
 *     in="header",
 *     required=true,
 *     description="📱 MOBILE · HR-sourced learner identifier (`machine_code` on the users table). Every mobile API call must include this header — the server uses it to load the acting employee and execute the request on their behalf. Required on every `/mobile/*` endpoint.",
 *     @OA\Schema(type="string", example="EMP-001234")
 * )
 *
 * @OA\Parameter(
 *     parameter="MobileAuthorization",
 *     name="X-Api-Token",
 *     in="header",
 *     required=true,
 *     description="📱 MOBILE · Shared static API token for the HR integration. Paste the value stored in the `settings` table under key `mobile_shared_bearer_token` (rotate via DB update + cache flush). The token is sent as a raw value — NO `Bearer ` prefix. The middleware also accepts `X-Mobile-Token` and the classic `Authorization: Bearer <token>` header for HR systems that prefer those conventions.",
 *     @OA\Schema(type="string", example="u3pBBsRbb3NGcSZsKQvuB8uZTjzJ8CKuUiMG9V6qYzo84Vxs0VIUopDTSVnj")
 * )
 *
 * @OA\Parameter(
 *     parameter="Page",
 *     name="page",
 *     in="query",
 *     required=false,
 *     description="Page number for paginated lists (1-based).",
 *     @OA\Schema(type="integer", minimum=1, default=1)
 * )
 *
 * @OA\Parameter(
 *     parameter="PerPage",
 *     name="per_page",
 *     in="query",
 *     required=false,
 *     description="Items per page for paginated lists.",
 *     @OA\Schema(type="integer", minimum=1, maximum=200, default=15)
 * )
 *
 * @OA\Parameter(
 *     parameter="Search",
 *     name="search",
 *     in="query",
 *     required=false,
 *     description="Free-text search filter.",
 *     @OA\Schema(type="string")
 * )
 *
 * @OA\Parameter(
 *     parameter="IdPath",
 *     name="id",
 *     in="path",
 *     required=true,
 *     description="Resource identifier",
 *     @OA\Schema(type="integer", minimum=1)
 * )
 *
 * --------------------------------------------------------------------
 * Responses
 * --------------------------------------------------------------------
 *
 * @OA\Response(
 *     response="Unauthorized",
 *     description="Missing or invalid bearer token",
 *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 * )
 *
 * @OA\Response(
 *     response="Forbidden",
 *     description="Caller is authenticated but not allowed to perform this action",
 *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 * )
 *
 * @OA\Response(
 *     response="NotFound",
 *     description="Resource not found",
 *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 * )
 *
 * @OA\Response(
 *     response="ValidationError",
 *     description="The request body failed validation",
 *     @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 * )
 *
 * @OA\Response(
 *     response="ServerError",
 *     description="Unexpected server error",
 *     @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 * )
 */
class Envelopes
{
}
