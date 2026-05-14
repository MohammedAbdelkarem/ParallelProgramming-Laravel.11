<?php


namespace App\Services;

use App\Models\ErrorsLog;

class ErrorsLogService
{
    public function newError($e)
    {

        $model = new ErrorsLog();
        $model->message = $e->getMessage();
        $model->file = $e->getFile();
        $model->line = $e->getLine();
        $model->trace = $e->getTraceAsString();
        $model->err_code = $e->getCode();
        $model->save();

        // return $model->id;
    }

    public function getLastError()
    {
        $last_error = ErrorsLog::latest()->select('id', 'message', 'line', 'err_code')
            ->first();
        return $last_error;
    }

    public function getById($id)
    {
        $last_error = ErrorsLog::where('id', $id)->select('message', 'file', 'line')->first();
        return $last_error;
    }
}
