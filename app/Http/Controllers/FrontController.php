<?php

namespace App\Http\Controllers;

use App\Filament\Resources\CategoryResource;
use App\Models\ArticleNews;
use App\Models\Author;
use App\Models\BannerAdvertisement;
use App\Models\Category;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    //
    public function index() {
        $categories = Category::all();

        $articles = ArticleNews::with(['category'])
        ->where('is_featured', 'not_featured')
        ->latest()
        ->take(3)
        ->get();

        $featured_articles = ArticleNews::with(['category'])
        ->where('is_featured', 'featured')
        ->inRandomOrder()
        ->take(3)
        ->get();

        $authors = Author::all();

        $bannerads = BannerAdvertisement::where('is_active', 'active')
        ->where('type', 'banner')
        ->inRandomOrder()
        // ->take(1)
        // ->get();
        ->first();

        $entertaiment_articles = ArticleNews::whereHas('category', function ($query){
            $query->where('name', 'Entertaiment');
        })
        ->where('is_featured', 'not_featured')
        ->latest()
        ->take(6)
        ->get();
        
        $entertaiment_featured_articles = ArticleNews::whereHas('category', function ($query){
            $query->where('name', 'Entertaiment');
        })
        ->where('is_featured', 'featured')
        ->inRandomOrder()
        ->first();

        $business_articles = ArticleNews::whereHas('category', function ($query){
            $query->where('name', 'Business');
        })
        ->where('is_featured', 'not_featured')
        ->latest()
        ->take(6)
        ->get();

        $business_featured_articles = ArticleNews::whereHas('category', function ($query){
            $query->where('name', 'Business');
        })
        ->where('is_featured', 'featured')
        ->inRandomOrder()
        ->first();

        $automotive_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Automotive');  
        })
        ->where('is_featured', 'not_featured')
        ->latest()
        ->take(6)
        ->get();

        $automotive_featured_articles = ArticleNews::whereHas('category', function ($query) {
            $query->where('name', 'Automotive');
        })
        ->where('is_featured', 'featured')
        ->inRandomOrder()
        ->first();

        return view('front.index', compact('categories', 'articles', 'authors', 'featured_articles', 
        'bannerads', 'entertaiment_articles', 'entertaiment_featured_articles', 'business_articles', 'business_featured_articles',
        'automotive_articles', 'automotive_featured_articles',
    ));
    }

    public function category(Category $category) {
        $categories = Category::all();

        $bannerads = BannerAdvertisement::where('is_active', 'active')
        ->where('type', 'banner')
        ->inRandomOrder()
        // ->take(1)
        // ->get();
        ->first();

        return view('front.category', compact('category', 'categories', 'bannerads'));
    }

    public function author(Author $author){
        $categories = Category::all();

        $bannerads = BannerAdvertisement::where('is_active', 'active')
        ->where('type', 'banner')
        ->inRandomOrder()
        // ->take(1)
        // ->get();
        ->first();

        return view('front.author', compact('categories', 'author', 'bannerads'));
    }

    public function search(Request $request) {
        $request->validate([
            'keyword'=> ['required', 'string', 'max:255'],
        ]);
        $categories = Category::all();
        $keyword = $request->keyword;

        $articles = ArticleNews::with(['category', 'author'])
        ->where('name', 'like', '%'. $keyword .'%') -> paginate(6);

        return view('front.search', compact('articles', 'keyword', 'categories', 'articles'));
    }

    public function details(ArticleNews $articleNews) {
        $categories = Category::all();

        $articles = ArticleNews::with(['category'])
        ->where('is_featured', 'not_featured')
        ->where('id', '!=', $articleNews->id)
        ->latest()
        ->take(3)
        ->get();

        $bannerads = BannerAdvertisement::where('is_active', 'active')
        ->where('type', 'banner')
        ->inRandomOrder()
        // ->take(1)
        // ->get();
        ->first();
        
        $squareads = BannerAdvertisement::where('type', 'square')
        ->where('is_active', 'active')
        ->inRandomOrder()
        ->take(2)
        ->get();

        if ($squareads->count()<2) {
            $squareads_1 = $squareads->first();
            $squareads_2 = null;
        } else {
            $squareads_1 = $squareads->get(0);
            $squareads_2 = $squareads->get(1);
        }

        $author_news = ArticleNews::where('author_id', $articleNews->author_id) 
        ->where('id', '!=', $articleNews->id)
        ->inRandomOrder()
        ->get();

        return view('front.details', compact('articleNews', 'categories', 'articles', 'bannerads', 'squareads_1', 'squareads_2', 'author_news'));
    }
}
