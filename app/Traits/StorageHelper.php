<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Traits\StringHelper;
use \Illuminate\Database\Eloquent\Model;

trait StorageHelper
{
    use StringHelper;

    /**
     * Deletes a file or directory from storage.
     *
     * @param string $deletePath The path to the file or directory to be deleted.
     * @param bool $isInPrivate Flag to indicate if the path is in the private disk.
     * @param bool $isDir Flag to indicate if the path is a directory.
     *
     * @return bool True if the file or directory is successfully deleted.
     */
    protected function storageDelete(string $deletePath, Bool $isInPrivate = false, Bool $isDir = false): bool
    {
        if ($deletePath)
            if ($isInPrivate)
                !$isDir ? Storage::disk("private")->delete($deletePath) : Storage::disk("private")->deleteDirectory($deletePath);
            else
                !$isDir ? Storage::disk('public')->delete($deletePath) : Storage::disk('public')->deleteDirectory($deletePath);
        return true;
    }

    /**
     * Store a file in a specified directory with a unique filename.
     *
     * @param \File $file The file to be stored.
     * @param string $path The target directory path relative to the main path.
     * @param string $mainPath The main directory path where files will be stored. Default is "public/assets/".
     * @param string $singleFilePath
     *
     * @return string The path to the stored file including the filename.
     */
    protected function storeFile($file, String $path, String $mainPath = "assets/", string $singleFilePath = ""): string
    {

        if ($singleFilePath) {
            $this->storageDelete($singleFilePath);
        }

        $destination_path = "$mainPath$path";
        $randomString = Str::random(48);
        // $file_name =  $this->arToEnNum($randomString . str_replace(' ', '_', $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension());
        $file_name =  $this->arToEnNum($randomString . '.' . $file->getClientOriginalExtension());
        $file->storeAs($destination_path, $file_name);
        return "$destination_path/$file_name";
    }

    /**
     * Stores a file in the specified directory with a unique filename, and updates the model with the new file path.
     *
     * @param \File $file The file to be stored.
     * @param \Illuminate\Database\Eloquent\Model $model The model where the file path will be stored.
     * @param string $column The name of the column where the file path will be stored.
     * @param bool $deleteImage If true, the old file will be deleted.
     * @param string $path The target directory path relative to the main path.
     * @param string $mainPath The main directory path where files will be stored. Default is "public/assets/".
     * @param string $singleFilePath The path to the old file to be deleted.
     *
     * @return \Illuminate\Database\Eloquent\Model $model
     */
    public function StoreUpdate($file, Model $model, $column, $deleteImage = false, string $path, $mainPath = "assets/", $singleFilePath = "",)
    {
        //Check to add new image
        if (isset($file)) {
            $model->$column = $this->storeFile(
                file: $file,
                path: $path,
                singleFilePath: $singleFilePath,
                mainPath: $mainPath
            );
            $deleteImage = false;
        }

        //Check to delete user image (will be default image)
        if ($deleteImage) {
            $model->$column ? $this->storageDelete($model->$column) : false;
            $model->$column = null;
        }

        return $model;
    }
}
