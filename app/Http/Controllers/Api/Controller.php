<?php
namespace App\Http\Controllers\Api;

class Controller
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Public Tender Database Test Documentation",
     *      description="Api for accessing and mannaging the public data for tenders",
     *      @OA\Contact(
     *          email="admin@admin.com"
     *      ),
     *      @OA\License(
     *          name="Apache 2.0",
     *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
     *      )
     * )
     *
     * @OA\Server(
     *      url=L5_SWAGGER_CONST_HOST,
     *      description="Demo API Server"
     * )
     *
     * @OA\Tag(
     *     name="Contracts",
     *     description="API Endpoints for Contracts"
     * )
     * 
     * @OA\Tag(
     *      name="user",
     *      description="API Endpoints for User"
     * )
    *      @OA\SecurityScheme(
     *         securityScheme="bearerAuth",
     *         type="http",
     *         scheme="bearer",
     *         bearerFormat="JWT"
     *      )
     */
}