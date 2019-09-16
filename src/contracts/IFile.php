<?php
/**
 * Created by PhpStorm.
 * User: luezoid
 * Date: 1/27/18
 * Time: 10:55 PM
 */

namespace Luezoid\Laravelcore\Contracts;

interface IFile
{


    public function save($name, $file, $type, $is_uuid_file_name_enabled = null);
}