<?php
/**
 * Created by PhpStorm.
 * User: keshav
 * Date: 11/10/18
 * Time: 6:04 PM
 */


return [
    'is_local' => true, // set this to false to use upload on S3 bucket
    'temp_path' => 'uploads',
    'aws_temp_link_time' => 10,
    'types' => [
        'EXAMPLE' => [
            'type' => 'EXAMPLE',
            'local_path' => 'storage/examples',
            'bucket_name' => 'examples',    // if files are gonna be uploaded on s3 bucket
            'validation' => 'required',
            'valid_file_types' => [     // define the extensions allowed
                'csv',
                'xls',
                'xlsx',
                'jpg',
                'png'
            ],
            'acl' => 'private'          // for s3 bucket
        ]
    ]
];