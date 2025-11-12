<?php

namespace App\Http\Controllers\Api;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class MetaController extends Controller
{
    public function roles(): JsonResponse
    {
        return response()->json([
            'roles' => array_values(RoleEnum::cases()),
        ]);
    }
}
