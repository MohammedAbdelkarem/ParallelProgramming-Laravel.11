<?php

use App\Constants\ApiMessages;
use App\Constants\ExceptionMessages;


if (!function_exists('findByIdOrFail')) {
    function findByIdOrFail($model, $modelId, $type = 'male', $resource = null, $with = [], $withTrashed = false, $selectedColumns = null , $asQuery = false , $where = [])
    {
        $modelInstance = null;
        $query = $withTrashed ? $model::withTrashed() : $model::query();

        if (isset($selectedColumns)) {
            $query->select($selectedColumns);
        }

        if (!empty($where)) {
            $query->where($where);
        }

        if (!empty($with)) {
            $query->with($with);
        }

        if (!empty($queries)) {
            $query->$queries;
        }

        $modelInstance = $query->find($modelId);

        // dd($modelInstance);

        if (!$modelInstance) {
            $notFoundMessage = '';
            if ($type == 'female') {
                $notFoundMessage = ExceptionMessages::MSG_RESOURCE_NOT_FOUNDF;
            } else {
                $notFoundMessage = ExceptionMessages::MSG_RESOURCE_NOT_FOUND;
            }
            notFoundFailure(null, __($notFoundMessage, ['resource' => __($resource)]));
        }
        if ($asQuery)
            return $query->where('id', $modelId);
        // dd($modelInstance);
        return $modelInstance;
    }
}

if (!function_exists('generateUniqueResourceNumber')) {
    function generateUniqueResourceNumber($model, $resourceAttribute, $prefix)
    {
        $lastResource = $model::orderBy('id', 'desc')->first();
        if ($lastResource) {
            $numberPart = substr($lastResource->{$resourceAttribute}, 2); // Extracting the numeric part
            $numberPartLength = strlen($numberPart);

            $nextNumber = (int) $numberPart + 1;
            $nextNumberLength = strlen($nextNumber);

            if ($nextNumberLength > $numberPartLength) {
                $nextNumber = 1;
                $numberPartLength++;
            }

            $nextNumber = str_pad($nextNumber, $numberPartLength, '0', STR_PAD_LEFT); // Incrementing and padding

            return $prefix . '_' . $nextNumber;
        } else {
            return $prefix . '_001'; // If there are no employees yet, start from 001
        }
    }
}

if (!function_exists('generateDateBasedSequentialNumber')) {
    function generateDateBasedSequentialNumber($model, $date, $numberOfPaddedZeros)
    {
        // Get the total number of records created today for this model
        $countToday = $model::whereDate('created_at', $date)->count();

        $sequentialNumber = $countToday + 1;

        $formattedSequentialNumber = str_pad($sequentialNumber, $numberOfPaddedZeros, '0', STR_PAD_LEFT);

        return $date . $formattedSequentialNumber;
    }
}

if (!function_exists('getModelInstancesDependingOnIds')) {
    function getModelInstancesDependingOnIds($model, $modelIds)
    {
        $modelInstances = collect();
        foreach ($modelIds as $modelId) {
            $modelInstance = $model::find($modelId);
            $modelInstances->push($modelInstance);
        }

        return $modelInstances;
    }
}
