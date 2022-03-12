<?php

namespace App\ResponseModels;

class SearchResponseModel
{
    public function __construct(
        public $bookReviews,
        public $books,
        public $users
    )
{
}
}