<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Storage;

class ArticlesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = Article::all();

        foreach ($articles as $i => $article) {
            $author = User::select()->where('id', $article->user_id)->first();
            $approved_by = User::select()->where('id', $article->approved_by)->first();
            $category = Category::select()->where('id', $article->category_id)->first();

            $articles[$i]->author = $author;
            $articles[$i]->approved_by = $approved_by;
            $articles[$i]->category = $category;
            $articles[$i]->brief = substr( strip_tags($article->content), 0, 45);
            
        }
        //error_log(print_r($articles, true));
        return response()->json($articles);
    }

    public function image(Article $article)
    {
        return response()->download(
            public_path(Storage::url($article->image)),
            $article->title
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Article::class);

        $validatedData = $request->validate([
            'title' => 'required|string|unique:articles|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|image|dimensions:min_width=200,min_height=200',
        ]);

        $article = new Article($request->all());

        $path = $request->image->store('public/articles');

        $article->image = $path;
        $article->save();

        return response()->json($article, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        $author = User::select()->where('id', $article->user_id)->first();
        $approved_by = User::select()->where('id', $article->approved_by)->first();
        $category = Category::select()->where('id', $article->category_id)->first();

        $article->author = $author;
        $article->approved_by = $approved_by;
        $article->category = $category;

        return response()->json($article);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
        $this->authorize('update', $article);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'image' => 'image|dimensions:min_width=200,min_height=200',
        ]);

        if (isset($request->image)) {
            $path = $request->image->store('public/articles');

            $article->image = $path;
        }

        $article->approved = 0;

        $data = $request->all();

        unset($data['image']);

        $article->update($data);

        return response()->json($article, 201);
    }

    public function approve(Request $request, Article $article)
    {
        $this->authorize('approve', $article);

        $user = JWTAuth::parseToken()->authenticate();

        $article->update([
            'approved' => 1,
            'approved_by' => $user->id
        ]);

        return response()->json($article, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        $this->authorize('delete', $article);

        $article->delete();

        return response()->json([
            'msg' => 'Article deleted'
        ], 200);
    }
}
