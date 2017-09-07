<?php

namespace App\Services;

use Aws\S3\S3Client;
use Config;


/**
* Aws Service
 * 
 * @author Jose Suarez <chua.jose@gmail.com>
 */

class AwsService
{

    var $s3Client = false;

    /**
     * Get File from s3
     *
     * @return void
     */
    function getS3()
    {
        $config = Config::get('aws.S3');
        return $this->s3Client = s3Client::factory($config);
    }
}
?>
