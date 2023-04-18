<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogReaction;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $request->validate([
            'status' => 'nullable|integer',
            'author' => 'nullable|exists:users,id',
            'date' => 'nullable|date'
        ]);


        $blogs = Blog::when($request->status, function ($query) use ($request) {
            $query->where('status', $request->status);
        })
        ->when($request->author,function($query) use ($request) {
            $query->where('author',$request->author);
        })
        ->when($request->date,function($query) use ($request) {
            $query->whereDate('created_at',$request->date);
        })
        ->get();

        return $this->showOne($blogs);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        $blog = Blog::create([
            'title' => htmlentities($request->title),
            'content' => htmlentities($request->content),
            'author' => $user->id,
            'status' => Blog::NORMAL,
        ]);

        return $this->showOne($blog);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $blog = Blog::with('comment')->find($id);
        return $this->showOne($blog);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $data = $request->validate([
            'title' => 'nullable|string',
            'content' => 'nullable|string'
        ]);

        $data = collect($data)->map(function($item) {
            return htmlentities($item);
        })->toArray();

        $blog = Blog::where('author',$user->id)
            ->findOrFail($id);

        $blog->update($data);

        return $this->showOne($blog);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $blog = Blog::where('author',$user->id)
            ->findOrFail($id);

        return $this->showOne($blog->delete() ? true : false);
    }

    public function react(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'type' => 'required|string|in:like,dislike,remove',
            'blog_id' => 'required|exists:blogs,id'
        ]);

        $reaction = [
            'like' => 1,
            'dislike' => 0,
        ];

        $matchKey = [
            'blog_id' => $request->blog_id,
            'user_id' => $user->id
        ];

        if($request->type == 'like' || $request->type == 'dislike')
            $reaction = BlogReaction::updateOrCreate($matchKey,[
                'reaction' => $reaction[$request->type]
            ]);

        else $reaction = BlogReaction::where($matchKey)->delete() ? true : false;


        return $this->showOne($reaction);
    }
}
