<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\UserService;
use App\Services\AuthService;
use App\Http\Requests\UserRegistrationRequest;
use App\Http\Requests\UserLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseController
{
    protected UserService $userService;
    protected AuthService $authService;

    public function __construct(UserService $userService, AuthService $authService)
    {
        $this->userService = $userService;
        $this->authService = $authService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/register",
     *     tags={"Authentication"},
     *     summary="Register a new user",
     *     description="Create a new user account and return user data with access token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="role", type="string", enum={"Developer","Admin","Manager","User"}, example="Developer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john@example.com"),
     *                     @OA\Property(property="role", type="string", example="Developer"),
     *                     @OA\Property(property="created_at", type="string", example="2025-08-19T10:30:00.000000Z")
     *                 ),
     *                 @OA\Property(property="access_token", type="string", example="1|abcdef123456"),
     *                 @OA\Property(property="token_type", type="string", example="Bearer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function register(UserRegistrationRequest $request): JsonResponse
    {
        try {
            $data = $this->userService->register($request->validated());
            return $this->sendResponse($data, 'User registered successfully', 201);
        } catch (ValidationException $e) {
            return $this->sendError('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->sendError('Registration failed', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     tags={"Authentication"},
     *     summary="Login user",
     *     description="Authenticate user and return access token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john@example.com"),
     *                     @OA\Property(property="role", type="string", example="Developer"),
     *                     @OA\Property(property="last_login", type="string", example="2025-08-19T10:30:00.000000Z")
     *                 ),
     *                 @OA\Property(property="access_token", type="string", example="1|abcdef123456"),
     *                 @OA\Property(property="token_type", type="string", example="Bearer"),
     *                 @OA\Property(property="expires_in", type="string", nullable=true, example=null)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Invalid credentials"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function login(UserLoginRequest $request): JsonResponse
    {
        try {
            $data = $this->authService->login($request->validated());
            return $this->sendResponse($data, 'Login successful');
        } catch (ValidationException $e) {
            return $this->sendError('Invalid credentials', ['error' => $e->getMessage()], 401);
        } catch (\Exception $e) {
            return $this->sendError('Login failed', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     tags={"Authentication"},
     *     summary="Logout user",
     *     description="Revoke current access token",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Logout successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string", example="Token revoked successfully"),
     *                 @OA\Property(property="logged_out_at", type="string", example="2025-08-19T10:30:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $data = $this->authService->logout($request->user());
            return $this->sendResponse($data, 'Logout successful');
        } catch (\Exception $e) {
            return $this->sendError('Logout failed', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/me",
     *     tags={"Authentication"},
     *     summary="Get current user profile",
     *     description="Get authenticated user information",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="User profile retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john@example.com"),
     *                     @OA\Property(property="role", type="string", example="Developer"),
     *                     @OA\Property(property="created_at", type="string", example="2025-08-19T10:30:00.000000Z"),
     *                     @OA\Property(property="tokens_count", type="integer", example=1)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $data = $this->authService->getUserProfile($request->user());
            return $this->sendResponse($data, 'User profile retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve user profile', ['error' => $e->getMessage()], 500);
        }
    }
}
