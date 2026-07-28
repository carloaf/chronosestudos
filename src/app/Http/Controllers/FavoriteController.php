<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Toggle favorite for a model (Subject or Topic).
     */
    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'favoritable_type' => 'required|string|in:App\\Models\\Subject,App\\Models\\Topic',
            'favoritable_id' => 'required|integer',
        ]);

        $type = $request->input('favoritable_type');
        $id = $request->input('favoritable_id');

        $existing = Favorite::where('user_id', Auth::id())
            ->where('favoritable_type', $type)
            ->where('favoritable_id', $id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['favorited' => false]);
        }

        Favorite::create([
            'user_id' => Auth::id(),
            'favoritable_type' => $type,
            'favoritable_id' => $id,
        ]);

        return response()->json(['favorited' => true]);
    }
}
