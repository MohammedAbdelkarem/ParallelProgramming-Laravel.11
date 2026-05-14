<?php

namespace App\Http\Controllers\Media;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Services\Media\MediaService;
use App\Http\Requests\Media\DeleteMediaRequest;
use App\Http\Requests\Media\UpdateMediaRequest;
use App\Http\Requests\Media\UploadMediaRequest;

class MediaController extends Controller
{
    public function __construct(
        protected MediaService $mediaService,
    ) {}

    public function store(UploadMediaRequest $request)
    {
        return success(
            $this->mediaService->store($request->validated()),
            ApiMessages::MSG_SUCCESS
        );
    }

    public function update(UpdateMediaRequest $request , $id)
    {
        return success(
            $this->mediaService->update($request->validated() , $id),
            ApiMessages::MSG_SUCCESS
        );
    }

    
    public function delete(DeleteMediaRequest $request)
    {
        return success(
            $this->mediaService->delete($request->validated()),
            ApiMessages::MSG_SUCCESS
        );
    }
    
}
