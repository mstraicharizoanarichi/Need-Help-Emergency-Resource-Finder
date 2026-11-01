<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggle(Resource $resource, Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to save favorite emergency resources.');
        }

        $user->favorites()->toggle($resource->id);

        $isFavorited = $user->favorites()->where('resource_id', $resource->id)->exists();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'is_favorited' => $isFavorited,
                'message' => $isFavorited ? 'Saved to your emergency bookmarks.' : 'Removed from emergency bookmarks.',
            ]);
        }

        return back()->with('success', $isFavorited ? 'Resource saved to your favorites.' : 'Resource removed from favorites.');
    }
}
