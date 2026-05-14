<?php

if (!function_exists('transResource')) {
    function transResource(string $message, $resourceKey = []): string
    {
        if ($resourceKey != null) {
            $message = trans($message, ['resource' => trans($resourceKey)]);
        } else {
            $message = trans($message);
        }

        return $message;
    }
}