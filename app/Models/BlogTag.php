<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogTag extends Model
{
    use HasFactory;
    protected $table = 'blog_tags';

    public static function InsertDeleteTags($blogId, $tags)
    {
        // Xóa các tag liên quan đến bài viết có blog_id (khóa ngoại blog_tags)
        BlogTag::where('blog_id', '=', $blogId)->delete();

        if(!empty($tags)) {
            $tagArray = explode(',', $tags);
            foreach ($tagArray as $tag) {
                $save = new BlogTag();
                $save->blog_id = $blogId;
                $save->name = $tag;
                $save->save();
            }
        }
    }

    public static function getTagByBlogId($blogId)
    {
        return  BlogTag::select('name')->where('blog_id', '=', $blogId)->get();
//        return $this->hasMany(BlogTag::class, 'blog_id');
    }
}
