<?php

namespace App\utils\aws;

use App\Entity\Image;
use Aws\AwsClient;
use Doctrine\ORM\EntityManager;
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
        private string $publicFileURL,
        private EntityManager $entityManager
    )
    {
        $this->awsClient = $awsClientWrapper->getS3Client();
    }

    /**
     * @param UploadedFile $imageData
     * @return Image
     */
    public function uploadImageToBucketeer(UploadedFile $imageData) : Image{
        $originalFilename = pathinfo($imageData->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$imageData->guessExtension();
        $this->awsClient->putObject([
            'Bucket' => $this->bucketName,
            'Key' =>  $this->publicUploadPath . $newFilename,
            'Body' => $imageData->getContent(),
            'ACL' => 'public-read'
        ]);
        $publicUrl = $this->publicFileURL . $newFilename;
        $image = new Image();
        $image->setUrl($publicUrl);
        $this->entityManager->persist($image);
        $this->entityManager->flush();
        return $image;
   }
}