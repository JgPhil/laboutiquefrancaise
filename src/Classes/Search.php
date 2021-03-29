<?php

namespace App\Classes;

use App\Entity\Category;

class Search
{
    /**
     * @var string
     */
    public $content = '';

    /**
     * Undocumented variable
     *
     * @var array
     */
    public $price_range = [];

    /**
     * @var Category[]
     */
    public $categories = [];
}
