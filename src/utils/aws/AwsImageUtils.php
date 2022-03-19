<?php

namespace App\utils\aws;

use App\Entity\Image;
use Aws\AwsClient;
use Doctrine\ORM\EntityManager;
use JetBrains\PhpStorm\Pure;
use PHPUnit\Framework\Error\Warning;
use Symfony\Component\Filesystem\Filesystem;
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
        private EntityManager $entityManager,
        private Filesystem $filesystem
    )
    {
        $this->awsClient = $awsClientWrapper->getS3Client();
        $this->filesystem = new Filesystem();
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

   public function uploadBase64ImageToBucketeer(string $image):?Image{
        $decodedImage = $this->decodeImage($image);
         if($decodedImage === null){
             return null;
         }

        $tempFile = $this->filesystem-> tempnam('/tmp','image_','.jpeg');
        $uuidImage = uniqid() . ".jpeg";
        file_put_contents(
            filename: $tempFile,
            data: $decodedImage
        );
        $this->awsClient->putObject([
              'Bucket' => $this->bucketName,
                'Key' => $this->publicUploadPath . $uuidImage,
                'Body' => file_get_contents($tempFile),
                'ACL' => 'public-read'

          ]
        );
       $image = new Image();
       $image->setUrl($this->publicFileURL  . $uuidImage);
       $this->entityManager->persist($image);
       $this->entityManager->flush();
       return $image;
   }

   private function decodeImage(string $encodedData):?string{
       $imageDecoded = base64_decode($encodedData ,strict: true);
       if($imageDecoded === false || !$this->isImageValid($imageDecoded)){
           return null;
       }
       return $imageDecoded;
   }

   private function isImageValid($imageData):bool{
        try {
            $img = imagecreatefromstring(base64_decode($imageData));
        }catch(\Exception $error){
            return false;
       }

       imagepng($img, 'myimage.png');
       $data = getimagesize('myimage.png');
       return $data !== false;
   }

}