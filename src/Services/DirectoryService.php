<?php


namespace Miladimos\FileManager\Services;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Miladimos\FileManager\Models\Directory;

// all of about directories
class DirectoryService extends Service
{

    // Directory Model
    private $model;

    public function __construct()
    {
        parent::__construct();

        $this->model = new Directory();
    }

    /**
     * Show all directories that the selected item can be moved to.
     *
     * @return array
     */
    public function allDirectories()
    {
        $directories = $this->disk->allDirectories('/');

        return collect($directories)->filter(function ($directory) {
            return !(Str::startsWith($directory, '.'));
        })->map(function ($directory) {
            return DIRECTORY_SEPARATOR . $directory;
        })->reduce(function ($allDirectories, $directory) {
            $parts = explode('/', $directory);
            $name = str_repeat('&nbsp;', (count($parts)) * 4) . basename($directory);

            $allDirectories[$directory] = $name;

            return $allDirectories;
        }, collect())->prepend($this->breadcrumbRootLabel, '/');
    }

    public function listDirectories($path)
    {
        $dirs = collect($this->disk->directories($this->base_directory . $this->ds . $path));

        return $dirs;
    }

    public function listDirectoriesRecursive($directory)
    {
        $dirs = $this->disk->allDirectories($directory);

        return $dirs;
    }


    public function createDirectory($directory)
    {
//        DB::transaction(function () {
//            $this->model->create([
//                'user_id' => user()->id,
//            ]);
//        });

        $path = $this->base_directory . $this->ds . $directory;

        if (!checkPath($path)) {
            if ($this->disk->makeDirectory($path))
                return true;
            else {
//                $this->error('Directory "' . $directory . '" already exists.');
                $this->error('Can not create directory.');
                return false;
            }
        }

        return false;
    }

    public function deleteDirectory($directory)
    {
        $path = $this->base_directory . $this->ds . $directory;

//        dd($path);
        if (!checkPath($path, $this->disk_name)) {
            return false; // directory does not exists
        }

        $filesFolders = array_merge($this->disk->directories($path), $this->disk->files($path));
        if ($filesFolders)
            return false; // directory is not empty

        if ($this->disk->deleteDirectory($path)) {
//            DB::transaction(function () use ($directory) {
//                $this->model->where('id', $directory)->delete();
//            });
            return true;
        }

        return false;
    }
}
