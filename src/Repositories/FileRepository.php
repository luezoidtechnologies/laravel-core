<?php
/**
 * Created by PhpStorm.
 * User: luezoid
 * Date: 8/22/17
 * Time: 7:31 PM
 */

namespace Luezoid\Laravelcore\Repositories;


use Luezoid\Laravelcore\Models\File;

class FileRepository
{
    public $isSnakeToCamel = true;
    public $model = File::class;


    public function create($type, $name, $s3Key, $localPath)
    {
        $file = new File();
        $file->type = $type;
        $file->name = $name;
        $file->local_path = $localPath;
        $file->s3_key = $s3Key;

        $file->save();

        return $file;
    }

}