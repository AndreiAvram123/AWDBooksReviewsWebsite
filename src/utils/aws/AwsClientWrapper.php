<?php
namespace App\utils\aws;
use Aws\S3\S3Client;


class AwsClientWrapper
{
    private S3Client $s3Client;
    public function __construct(string $region)
    {
        $this->s3Client = new S3Client(
            [
                'version' => 'latest',
                'region' => $region,
            ]
        );
    }

    /**
     * @return S3Client
     */
    public function getS3Client(): S3Client
    {
        return $this->s3Client;
    }


}