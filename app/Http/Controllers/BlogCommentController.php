<?php

namespace App\Http\Controllers;

use App\Http\Resources\BlogCommentReactResource;
use App\Http\Resources\BlogCommentResource;
use App\Models\BlogComment;
use App\Models\CommentReaction;
use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class BlogCommentController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = User::first();
        $request->validate([
            'article_id' => 'required|exists:blogs,id',
            'comment' => 'required|string'
        ]);

        $blogComment = BlogComment::create([
            'user_id' => $user->id,
            'blog_id' => $request->article_id,
            'comment' => htmlentities($request->comment)
        ]);


        return $this->showOne(new BlogCommentResource($blogComment));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BlogComment  $blogComment
     * @return \Illuminate\Http\Response
     */
    public function show(BlogComment $blogComment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BlogComment  $blogComment
     * @return \Illuminate\Http\Response
     */
    public function edit(BlogComment $blogComment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BlogComment  $blogComment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::first();
        $request->validate([
            'comment' => 'required|string',
            'article_id' => 'required|exists:blogs,id'
        ]);

        $blogComment = BlogComment::where('user_id', $user->id)
            ->where('blog_id', $request->article_id)
            ->findOrFail($id);

        $blogComment->update([
            'comment' => htmlentities($request->comment)
        ]);

        return $this->showOne(new BlogCommentResource($blogComment));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BlogComment  $blogComment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $user = User::first();
        $request->validate([
            'article_id' => 'required|exists:blogs,id'
        ]);

        $blogComment = BlogComment::where('user_id', $user->id)
            ->where('blog_id', $request->article_id)
            ->findOrFail($id);

        if ($blogComment) {
            CommentReaction::where('blog_comment_id', $blogComment->id)->delete();
        }

        return $this->showOne($blogComment->delete() ? true : false);
    }

    public function react(Request $request)
    {
        $user = User::first();
        $request->validate([
            'type' => 'required|string|in:like,dislike,remove',
            'article_comment_id' => 'required|exists:blog_comments,id'
        ]);

        $reaction = [
            'like' => 1,
            'dislike' => 0,
        ];

        $matchKey = [
            'blog_comment_id' => $request->article_comment_id,
            'user_id' => $user->id
        ];

        if ($request->type == 'like' || $request->type == 'dislike')
            $reaction = CommentReaction::updateOrCreate($matchKey, [
                'reaction' => $reaction[$request->type]
            ]);

        else $reaction = CommentReaction::where($matchKey)->delete() ? true : false;

        return $this->showOne(new BlogCommentReactResource($reaction));
    }
}
