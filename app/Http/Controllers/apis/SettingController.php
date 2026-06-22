<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\UpdateSettingsRequest;
use App\Http\Traits\HasFile;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class SettingController extends ApiController
{
    use HasFile;

    public function __construct(private readonly SettingService $settingService) {}

    /**
     * @OA\Get(
     *     path="/settings",
     *     tags={"Settings"},
     *     summary="Get public settings as a key=>value map. Public.",
     *     @OA\Response(
     *         response=200,
     *         description="Settings map",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="object",
     *                     additionalProperties=@OA\AdditionalProperties(type="string"),
     *                     example={"site_name":"2B Academy","support_email":"info@example.com"}
     *                 ))
     *             }
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        return $this->success(__('messages.retrieved'), $this->settingService->getMap());
    }

    /**
     * @OA\Get(
     *     path="/admin/settings",
     *     tags={"Settings"},
     *     summary="Get the full settings list with type metadata (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Full settings list",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id",    type="integer"),
     *                         @OA\Property(property="key",   type="string"),
     *                         @OA\Property(property="value", type="string", nullable=true),
     *                         @OA\Property(property="type",  type="string"),
     *                         @OA\Property(property="label", type="string")
     *                     )
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden")
     * )
     */
    public function adminIndex(): JsonResponse
    {
        $settings = $this->settingService->getAll()->map(fn ($s) => [
            'id'    => $s->id,
            'key'   => $s->key,
            'value' => $s->value,
            'type'  => $s->type,
            'label' => $s->label ?? $s->key,
        ]);

        return $this->success(__('messages.retrieved'), $settings);
    }

    /**
     * @OA\Put(
     *     path="/admin/settings",
     *     tags={"Settings"},
     *     summary="Update one or many settings (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"settings"},
     *             @OA\Property(
     *                 property="settings",
     *                 type="object",
     *                 description="Map of setting key to string value.",
     *                 additionalProperties=@OA\AdditionalProperties(type="string", nullable=true),
     *                 example={"site_name":"2B Academy","support_email":"info@example.com"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated settings map",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="object",
     *                     additionalProperties=@OA\AdditionalProperties(type="string")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function update(UpdateSettingsRequest $request): JsonResponse
    {
        $this->settingService->updateMany($request->validated('settings'));

        // Return the full admin shape so the SPA can refresh local state in
        // one round-trip, including file-type rows the public map filters out.
        $settings = $this->settingService->getAll()->map(fn ($s) => [
            'id'    => $s->id,
            'key'   => $s->key,
            'value' => $s->value,
            'type'  => $s->type,
            'label' => $s->label ?? $s->key,
        ]);

        return $this->success(__('messages.updated'), $settings);
    }

    /**
     * @OA\Post(
     *     path="/admin/settings/upload",
     *     tags={"Settings"},
     *     summary="Upload a single image/file and write its storage path back to the matching setting (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"key","file"},
     *                 @OA\Property(property="key",  type="string", description="Setting key — e.g. header_logo, banner_background, footer_logo, about_image."),
     *                 @OA\Property(property="file", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Uploaded — returns the new public URL and the relative storage path.",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="object",
     *                     @OA\Property(property="key",  type="string"),
     *                     @OA\Property(property="path", type="string"),
     *                     @OA\Property(property="url",  type="string", format="uri")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'key'  => 'required|string|max:255',
            'file' => 'required|file|image|max:3072', // 3 MB cap covers banner/logo per Figma
        ]);

        $key  = (string) $request->input('key');
        $path = $this->uploadRequestFile('Setting', $request, 'file');

        $this->settingService->updateMany([$key => $path]);

        return $this->success(__('messages.updated'), [
            'key'  => $key,
            'path' => $path,
            'url'  => $this->getFileUrl($path),
        ]);
    }
}
