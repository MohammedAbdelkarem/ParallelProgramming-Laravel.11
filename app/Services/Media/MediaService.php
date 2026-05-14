<?php

namespace App\Services\Media;

use App\Models\Lesson;
use App\Enums\MediaTypeEnum;
use App\Enums\MediaStatusEnum;
use App\Enums\StoryStatusEnum;
use App\Constants\MediaCollection;
use App\Constants\ExceptionMessages;
use App\Services\Lesson\LessonService;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class MediaService.
 */
class MediaService
{
    public function __construct(
        
    ) {}

    public function store($data)
    {
        $model = getModel($data['context_type']);

        $mediaCollection = mediaCollectionByContxt($data['context_type']);

        $context = $model::find($data['context_id']);

        
        if (isset($data['images']))
            uploadFilesOnMedia($data['images'], $context, $mediaCollection);
        if (isset($data['videos']))
            uploadFilesOnMedia($data['videos'], $context, $mediaCollection);
        if (isset($data['files']))
            uploadFilesOnMedia($data['files'], $context, $mediaCollection);
    }

    public function update($data, $id)
    {
        $media = Media::find($id);

        $model = getModelByPath($media->model_type);

        $context = $model::find($media->model_id);

        $mediaCollection = $media->collection_name;

        $media->delete();

        if (isset($data['images']))
            uploadFilesOnMedia($data['images'], $context, $mediaCollection);
        if (isset($data['videos']))
            uploadFilesOnMedia($data['videos'], $context, $mediaCollection);
        if (isset($data['files']))
            uploadFilesOnMedia($data['files'], $context, $mediaCollection);
    }

    public function delete($data)
    {
        $media = Media::whereIn('id', $data['ids'])->get();

        foreach($media as $item) {
            $item->delete();
        }
    }
}
