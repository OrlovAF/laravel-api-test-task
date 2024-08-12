<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\SubmitRequest;

class ApiController extends Controller
{
    public function submit(SubmitRequest $request)
    {
        return $request->validated();
    }
}
