<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
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
            'title' => $this->title,
            'content' => $this->content,
            'author' => $this->author_data,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at ' => $this->updated_at,
            'like_count ' => $this->like_count,
            'dislike_count ' => $this->dislike_count,
            'comment' => BlogCommentResource::collection($this->whenLoaded('comment'))
        ];
    }
}
