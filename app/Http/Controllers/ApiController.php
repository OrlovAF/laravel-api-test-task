<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\SubmitRequest;
use App\Jobs\ProcessSubmission;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;

class ApiController extends Controller
{
    /**
     * @param SubmitRequest $request
     * @return ResponseFactory|Application|Response
     */
    public function submit(SubmitRequest $request)
    {
        dispatch(new ProcessSubmission($request->name, $request->email, $request->message));

        return response($request->validated(), 201);
    }
}
