<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     openapi="3.0.0",
 *     info={
 *         @OA\Info(
 *             title="XpertBooking API",
 *             version="1.0.0",
 *             description="Full API documentation for XpertBooking, including the main API (v1) and Dropsolid/Open AI webhook endpoints."
 *         )
 *     },
 *     servers={
 *         @OA\Server(
 *             url="/",
 *             description="API Server"
 *         )
 *     },
 *     tags={
 *         @OA\Tag(name="Webhook", description="Dropsolid/Open AI webhook endpoints - requires Bearer token authentication"),
 *         @OA\Tag(name="API v1", description="Main API endpoints - requires Laravel Passport Bearer token")
 *     }
 * )
 */
class BaseDocumentation
{
}
