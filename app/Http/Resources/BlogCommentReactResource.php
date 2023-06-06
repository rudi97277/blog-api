<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogCommentReactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'article_comment_id' => $this->blog_comment_id,
            'user' => $this->user,
            'reaction' => $this->reaction,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
