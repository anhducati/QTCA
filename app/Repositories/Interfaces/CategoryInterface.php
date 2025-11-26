<?php

namespace App\Repositories\Interfaces;

interface CategoryInterface
{
    public function getCategoryById(int $id);

    public function getAllBySlug(string $slug);
    public function getAllCategory();



    public function createCategory(array $data);

    public function updateCategory(array $data, $categoryId);

    public function forceDelete($categoryId);
}
