<?php

namespace App\Http\Controllers;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Response as HttpResponse;
use App\Responses\Response;
use App\Models\Project;

class ProfileController extends Controller
{
    public function show()
    {
        return Response::success(
            message: 'Profile retrieved successfully.',
            code: HttpResponse::HTTP_OK,
            data: UserResource::make(auth()->user()),
        );
    }
}
