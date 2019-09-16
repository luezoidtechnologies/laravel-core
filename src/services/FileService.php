<?php
/**
 * Created by PhpStorm.
 * User: luezoid
 * Date: 1/28/18
 * Time: 10:06 AM
 */

namespace Luezoid\Laravelcore\Services;


use Luezoid\Laravelcore\Exceptions\AppException;
use Luezoid\Laravelcore\Repositories\FileRepository;
use Ramsey\Uuid\Uuid;

class FileService
{
    private $fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function create($name, $key, $type, $localPath)
    {
        return $this->fileRepository->create($type, $name, $key, $localPath);
    }


}
