<?php

namespace App\BookApi;

use App\BookApi\GoogleBook;
use JMS\Serializer\Annotation\AccessType;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Type;

#[ExclusionPolicy(ExclusionPolicy::NONE)]
class GoogleBooksSearchResponse
{
       #[Expose]
        private int $totalItems = 100;



   #[Type("array<App\BookApi\GoogleBookDTO>")]
   private array $items;

    /**
     * @return int
     */
    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    /**
     * @param int $totalItems
     */
    public function setTotalItems(int $totalItems): void
    {
        $this->totalItems = $totalItems;
    }

    /**
     * @return GoogleBookDTO[] array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param array $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }



}