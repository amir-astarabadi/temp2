<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response as HttpResponse;
use App\Services\User\UserService;
use App\Responses\Response;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function destroy()
    {
        $this->userService->delete(auth()->user());

        return Response::success(
            message: 'Bye :(',
            code: HttpResponse::HTTP_OK,
        );
    }
}
