<?php
/**
 * Created by PhpStorm.
 * User: luezoid
 * Date: 1/28/18
 * Time: 10:09 AM
 */

namespace Luezoid\Laravelcore\Models;


use Illuminate\Database\Eloquent\Model;
use Luezoid\Laravelcore\Files\Services\AmazonS3Service;

class File extends Model
{
    protected $table = "files";
    protected $appends = ['url'];


    public function getUrlAttribute()
    {
        $type = config('file')['types'][$this->type];
        $acl = $type['acl'] ?? 'private';
        if ($this->local_path) {
            return url($this->local_path);
        } else {
            $amazonS3Service = app()->make(AmazonS3Service::class);
            return $acl === 'private' ?
                $amazonS3Service->getSignedUrl(
                    $this->s3_key, $type['bucket_name']
                ) : $amazonS3Service->getObjectUrl(
                    $this->s3_key, $type['bucket_name']
                );
        }
    }
}