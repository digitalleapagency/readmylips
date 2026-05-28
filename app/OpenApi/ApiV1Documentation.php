<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * API v1 Endpoints Documentation
 *
 * Main API base path: /api/v1/
 * Authentication: Laravel Passport Bearer token (Authorization: Bearer {token})
 *
 * @OA\PathItem(
 *     path="/api/v1/login",
 *     @OA\Post(
 *         tags={"API v1"},
 *         summary="Login",
 *         operationId="apiLogin",
 *         @OA\RequestBody(
 *             required=true,
 *             @OA\JsonContent(
 *                 required={"email","password"},
 *                 @OA\Property(property="email", type="string", format="email"),
 *                 @OA\Property(property="password", type="string")
 *             )
 *         ),
 *         @OA\Response(response=200, description="Login successful"),
 *         @OA\Response(response=401, description="Invalid credentials")
 *     )
 * )
 *
 * @OA\PathItem(
 *     path="/api/v1/register",
 *     @OA\Post(
 *         tags={"API v1"},
 *         summary="Register",
 *         operationId="apiRegister",
 *         @OA\Response(response=201, description="Registration successful")
 *     )
 * )
 *
 * @OA\PathItem(
 *     path="/api/v1/assets",
 *     @OA\Get(
 *         tags={"API v1"},
 *         summary="List assets",
 *         operationId="apiAssets",
 *         security={{"bearer": {}}},
 *         @OA\Response(response=200, description="List of assets")
 *     )
 * )
 *
 * @OA\PathItem(
 *     path="/api/v1/assets/{id}",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Get(
 *         tags={"API v1"},
 *         summary="Get asset by ID",
 *         operationId="apiAssetShow",
 *         security={{"bearer": {}}},
 *         @OA\Response(response=200, description="Asset details"),
 *         @OA\Response(response=404, description="Not found")
 *     )
 * )
 */
class ApiV1Documentation
{
}
