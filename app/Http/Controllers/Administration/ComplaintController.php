<?php

namespace App\Http\Controllers\Administration;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Resources\ComplaintResoruce;
use App\Services\ComplaintService;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function __construct(
        protected ComplaintService $complaint_service
    )
    {
    }

    public function index(Request $request)
    {
        return success(
            $this->complaint_service->index($request->all()),
            ApiMessages::MSG_SUCCESS,
            ComplaintResoruce::class,
            $request->has('per_page')
        );
    }

    public function show($id)
    {
        return success(
            $this->complaint_service->show($id),
            ApiMessages::MSG_SUCCESS,
            ComplaintResoruce::class
        );
    }

    public function archive($id)
    {
        return success(
            $this->complaint_service->archive($id),
            ApiMessages::MSG_SUCCESS,
        );   
    }
}
