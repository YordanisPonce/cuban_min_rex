<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = \App\Models\Review::orderBy('created_at', 'desc')->take(5)->get();

        $index = 999;

        $review = \App\Models\Review::where('user_id', auth()->id())->first();

        return view('review', compact('reviews', 'index', 'review'));
    }

    function submit(Request $request) {
        
        $comment = $request->input('comment');
        $rating = $request->input('rating');
        $user_id = auth()->id();

        $review = \App\Models\Review::where('user_id', $user_id)->whereNull('dj_id')->first();
    
        if ($review) {
            $review->update([
                'rating' => $rating,
                'comment' => $comment
            ]);
        } else {
            \App\Models\Review::create([
                'user_id' => $user_id,
                'rating' => $rating,
                'comment' => $comment
            ]);
        }

        return redirect()->back()->with('success', 'Reseña enviada satisfactoriamente.');
    }

    function rating_dj(Request $request, $dj_id) {
        $rating = $request->input('rating');
        $comment = $request->input('comment');
        $user_id = auth()->id();

        $review = \App\Models\Review::where('user_id', $user_id)->where('dj_id', $dj_id)->first();
    
        if ($review) {
            $review->update([
                'rating' => $rating,
                'comment' => $comment
            ]);
        } else {
            \App\Models\Review::create([
                'user_id' => $user_id,
                'dj_id' => $dj_id,
                'rating' => $rating,
                'comment' => $comment
            ]);
        }

        return redirect()->back()->with('success', 'Valoración enviada satisfactoriamente.');
    }
}

