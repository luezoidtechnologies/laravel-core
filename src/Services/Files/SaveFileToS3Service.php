<?php
/**
 * Created by PhpStorm.
 * User: luezoid
 * Date: 1/27/18
 * Time: 10:56 PM
 */

namespace Luezoid\Laravelcore\Services\Files;


use Luezoid\Laravelcore\Contracts\IFile;
use Luezoid\Laravelcore\Exceptions\AppException;
use Luezoid\Laravelcore\Services\FileService;
use Ramsey\Uuid\Uuid;

class SaveFileToS3Service implements IFile
{
    private $amazonS3Service;
    private $fileService;

    public function __construct(AmazonS3Service $amazonS3Service, FileService $fileService)
    {
        $this->amazonS3Service = $amazonS3Service;
        $this->fileService = $fileService;
    }


    public function save($name, $file, $type, $is_uuid_file_name_enabled = null, $acl = null)
    {
        $fileExtension = $file->getClientOriginalExtension();
        //Decide bucket with type, we'll keep separate buckets for documents and profile images

        $bucketName = config('file.types')[$type]['bucket_name'];
        $s3Key = $this->amazonS3Service->saveFileToAmazonServer($file->getRealPath(),
            $is_uuid_file_name_enabled ? Uuid::uuid4() . "." . $fileExtension : $name, $bucketName, $acl
                ? $acl : config('file.types')[$type]['acl'] ?? "private");
        if (!$s3Key) {
            //TODO: take message from lang
            throw  new AppException("There is some problem in saving file to s3", 500);
        }
        return $this->fileService->create($name, $s3Key, $type, null);
    }
}
