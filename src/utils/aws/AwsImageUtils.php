<?php

namespace App\utils\aws;

use Aws\AwsClient;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class AwsImageUtils
{
    private AwsClient $awsClient;

    #[Pure] public function __construct(
        private SluggerInterface $slugger,
        AwsClientWrapper $awsClientWrapper,
        private string $bucketName,
        private string $publicUploadPath,
        private string $publicFileURL
    )
    {
        $this->awsClient = $awsClientWrapper->getS3Client();

    }

    /**
     * @param UploadedFile $imageData
     * @return string  - the public available path to the image
     */
    public function uploadToBucket(UploadedFile $imageData) : string{
        $originalFilename = pathinfo($imageData->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$imageData->guessExtension();
        $this->awsClient->putObject([
            'Bucket' => $this->bucketName,
            'Key' =>  $this->publicUploadPath . $newFilename,
            'Body' => $imageData->getContent(),
            'ACL' => 'public-read'
        ]);
        return $this->publicFileURL . $newFilename;
   }
}