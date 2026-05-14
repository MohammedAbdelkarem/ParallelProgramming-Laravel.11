<?php

use Illuminate\Support\Str;


if (!function_exists('uploadFileOnMedia')) {
    function uploadFileOnMedia($file, $model, $collectionName)
    {
        $image = null;
        $image = is_array($file) ? ($file['image'] ?? $file['video']) : $file;
        $customProperties = is_array($file) && isset($file['quality']) ? ['quality' => $file['quality']] : [];
        $fileName = generateFileName($image);

        $model->addMedia($image)
            ->usingFileName($fileName)
            ->withCustomProperties($customProperties)
            ->toMediaCollection($collectionName);
    }
}

if (!function_exists('uploadFilesOnMedia')) {
    function uploadFilesOnMedia($files, $model, $collectionName)
    {
        foreach ($files as $file) {
            uploadFileOnMedia($file, $model, $collectionName);
        }
    }
}

if (!function_exists('updateFilesOnMedia')) {
    function updateFilesOnMedia($files, $model, $collectionName)
    {
        $model->clearMediaCollection($collectionName);
        uploadFilesOnMedia($files, $model, $collectionName);
    }
}

if (!function_exists('updateFileOnMedia')) {
    function updateFileOnMedia($file, $model, $collectionName)
    {
        $model->clearMediaCollection($collectionName);
        uploadFileOnMedia($file, $model, $collectionName);
    }
}

if (!function_exists('updateFileOnMediaWithoutClear')) {
    function updateFileOnMediaWithoutClear($file, $model, $collectionName)
    {
        // $model->clearMediaCollection($collectionName);
        uploadFileOnMedia($file, $model, $collectionName);
    }
}

if (!function_exists('deleteFilesFromMedia')) {
    function deleteFilesFromMedia($model, $collectionName)
    {
        $model->clearMediaCollection($collectionName);
    }
}



if (!function_exists('generateSanitizedFileName')) {
    function generateSanitizedFileName($file) {
        $originalFileName = $file->getClientOriginalName();
        $sanitizedFileName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $originalFileName);
        return $sanitizedFileName;
    }
}

if (!function_exists('generateFileName')) {
    function generateFileName($file)
    {
        $sanitizedFileName = basename(generateSanitizedFileName($file));

        $uniqueId = Str::uuid();

        $fileName = $uniqueId . '_' . $sanitizedFileName;

        return $fileName;
    }
}
