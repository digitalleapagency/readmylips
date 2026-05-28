<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * Dropsolid/Open AI Webhook API Documentation
 *
 * @OA\PathItem(
 *     path="/webhook/criteria",
 *     @OA\Get(
 *         tags={"Webhook"},
 *         summary="Get filter criteria",
 *         description="Retrieve the list of available filter criteria (e.g. non-profit) that can be used to filter assets.",
 *         operationId="webhookCriteria",
 *         security={{"webhook_bearer": {}}},
 *         @OA\Response(response=200, description="List of criteria"),
 *         @OA\Response(response=401, description="Unauthorized")
 *     )
 * )
 *
 * @OA\PathItem(
 *     path="/webhook/categories",
 *     @OA\Get(
 *         tags={"Webhook"},
 *         summary="Get categories",
 *         description="Retrieve all active categories (topics/themes) for the customer.",
 *         operationId="webhookCategories",
 *         security={{"webhook_bearer": {}}},
 *         @OA\Response(response=200, description="List of categories"),
 *         @OA\Response(response=401, description="Unauthorized")
 *     )
 * )
 *
 * @OA\PathItem(
 *     path="/webhook/topics",
 *     @OA\Get(
 *         tags={"Webhook"},
 *         summary="Get topics",
 *         description="Retrieve all topics (categories) for filtering and display.",
 *         operationId="webhookTopics",
 *         security={{"webhook_bearer": {}}},
 *         @OA\Response(response=200, description="List of topics"),
 *         @OA\Response(response=401, description="Unauthorized")
 *     )
 * )
 *
 * @OA\PathItem(
 *     path="/webhook/assets",
 *     @OA\Get(
 *         tags={"Webhook"},
 *         summary="List all assets",
 *         description="Get all active assets for customer_id=1. Optional query param: updated_at (Unix timestamp, 2h offset applied) to filter by update date.",
 *         operationId="webhookAssets",
 *         security={{"webhook_bearer": {}}},
 *         @OA\Parameter(
 *             name="updated_at",
 *             in="query",
 *             required=false,
 *             description="Unix timestamp - 2 hours will be subtracted for filtering",
 *             @OA\Schema(type="integer", example=1710000000)
 *         ),
 *         @OA\Response(response=200, description="List of assets"),
 *         @OA\Response(response=401, description="Unauthorized - Invalid or missing Bearer token")
 *     )
 * )
 *
 * @OA\PathItem(
 *     path="/webhook/asset/{id}/images",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Asset ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Get(
 *         tags={"Webhook"},
 *         summary="Get asset images",
 *         description="Retrieve the images array (black-white, color, extra) for a specific asset.",
 *         operationId="webhookAssetImages",
 *         security={{"webhook_bearer": {}}},
 *         @OA\Response(response=200, description="Images array"),
 *         @OA\Response(response=401, description="Unauthorized"),
 *         @OA\Response(response=404, description="Asset not found")
 *     )
 * )
 *
 * @OA\PathItem(
 *     path="/webhook/asset/{id}",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Asset ID (use internal ID for GET/PUT, Drupal ID for POST)",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Get(
 *         tags={"Webhook"},
 *         summary="Get single asset",
 *         description="Retrieve a specific asset by ID",
 *         operationId="webhookAssetShow",
 *         security={{"webhook_bearer": {}}},
 *         @OA\Response(response=200, description="Asset details"),
 *         @OA\Response(response=401, description="Unauthorized"),
 *         @OA\Response(response=404, description="Asset not found")
 *     ),
 *     @OA\Post(
 *         tags={"Webhook"},
 *         summary="Create asset",
 *         description="Create a new asset. Pass request body with asset data, or data will be fetched from external source using the ID.",
 *         operationId="webhookAssetCreate",
 *         security={{"webhook_bearer": {}}},
 *         @OA\RequestBody(
 *             required=false,
 *             description="Asset data (optional - fetches from external source if empty)",
 *             @OA\JsonContent(
 *                 type="object",
 *                 example={"name": "Asset Name", "description": "Description"}
 *             )
 *         ),
 *         @OA\Response(response=201, description="Asset created successfully"),
 *         @OA\Response(response=401, description="Unauthorized"),
 *         @OA\Response(response=404, description="No data found or asset already exists")
 *     ),
 *     @OA\Put(
 *         tags={"Webhook"},
 *         summary="Update asset",
 *         description="Update an existing asset by ID",
 *         operationId="webhookAssetUpdate",
 *         security={{"webhook_bearer": {}}},
 *         @OA\RequestBody(
 *             required=true,
 *             description="Asset data to update",
 *             @OA\JsonContent(type="object")
 *         ),
 *         @OA\Response(response=200, description="Asset updated successfully"),
 *         @OA\Response(response=401, description="Unauthorized"),
 *         @OA\Response(response=404, description="Asset not found")
 *     )
 * )
 *
 * @OA\PathItem(
 *     path="/webhook/asset/{id}/trigger",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Asset ID (Drupal ID for create)",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Get(
 *         tags={"Webhook"},
 *         summary="Trigger asset update or create",
 *         description="Update existing asset or create new one. Uses updateOrCreate with drupal_id.",
 *         operationId="webhookAssetTrigger",
 *         security={{"webhook_bearer": {}}},
 *         @OA\Response(response=200, description="Asset processed successfully"),
 *         @OA\Response(response=401, description="Unauthorized"),
 *         @OA\Response(response=404, description="No data found")
 *     )
 * )
 */
class WebhookDocumentation
{
}
