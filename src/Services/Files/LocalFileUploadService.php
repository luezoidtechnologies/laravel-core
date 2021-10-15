<?php

namespace Luezoid\Laravelcore\Services\Files;


use Luezoid\Laravelcore\Contracts\IFile;
use Luezoid\Laravelcore\Exceptions\AppException;
use Luezoid\Laravelcore\Services\FileService;
use Ramsey\Uuid\Uuid;

class LocalFileUploadService implements IFile
{

    private $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function save($name, $file, $type, $is_uuid_file_name_enabled = null, $acl = null)
    {
        $extensionArray = explode('.', $name);
        $extension = $extensionArray[count($extensionArray) - 1];
        $fileName = $is_uuid_file_name_enabled ? Uuid::uuid4() . "." . $extension : $name;
        $destination = config('file.types')[$type]['local_path'];
        $isFileMoved = $file->move($destination, $fileName);
        if (!$isFileMoved)
            throw new AppException("File not moved");

        return $this->fileService->create($name, null, $type, $destination . '/' . $fileName);

    }
}
