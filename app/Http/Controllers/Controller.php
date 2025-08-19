<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Mini PMS API",
 *     version="1.0.0",
 *     description="A mini Project Management System API with user authentication using Laravel Sanctum",
 *     @OA\Contact(
 *         email="admin@minipms.com"
 *     )
 * )
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Local Development Server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
abstract class Controller
{
    //
}
