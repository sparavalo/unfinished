<?php

namespace Category\View\Helper;

use Category\Service\CategoryService;
use Zend\View\Helper\AbstractHelper;

class CategoryHelper extends AbstractHelper
{
    private $categoryService;
    private $categoriesWithPosts;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function __invoke()
    {
        return $this;
    }

    public function forSelect()
    {
        return $this->categoryService->getAll();
    }

    /**
     * We need to return X categories with Y posts in every category sorted by magic for homepage.
     * magic = most viewed, most comments, most appropriate for the user etc. etc.
     *
     * for now return just latest.
     */
    public function forWeb()
    {
        if(!$this->categoriesWithPosts) {
            $this->categoriesWithPosts = $this->categoryService->getWebCategories();
        }

        return $this->categoriesWithPosts;
    }
}