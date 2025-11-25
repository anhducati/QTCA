<?php

namespace App\Repositories\Interfaces;

interface BlogInterface
{
    public function getBlogById(int $id);

    public function getBlogByUserId(int $user_id);

    public function getBlogByIdWithDetails(int $id);

    public function getBlogBySlug(string $slug);

    public function getBlogTag($blog_id);

    public function getAllTagLimit(int $limit);

    public function getBlogByCategoryId(int $id);

    public function getBlogByCategoryIdPagination(int $id, int $pagination);

    public function getBlogByCategoryIdLimit(int $id ,int $limit, $orderBy = 'desc');

    public function searchBog($query, $pagination);

    public function getBlogByBlogTagName(string $name, int $paginate = 10);
    public function getAllBlog();

    public function getBlogLimit(int $limit, $orderBy = 'desc');

    public function getBlogByView(int $limit = 10);
    public function getAllRecycleBlog();


    public function createBlog(array $data);

    public function updateViewBlog($id);

    public function updateBlog(array $data, $blogId);

    public function softDelete( $blogId);

    public function restoreBlog($blogId);

    public function forceDelete($blogId);
}
