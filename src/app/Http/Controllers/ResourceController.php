<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResourceRequest;
use App\Models\Resource;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function store(StoreResourceRequest $request, Topic $topic): RedirectResponse
    {
        $topic->resources()->create($request->validated());

        return redirect()->route('topics.show', $topic)
            ->with('success', 'Recurso adicionado!');
    }

    public function destroy(Resource $resource): RedirectResponse
    {
        $this->authorize('update', $resource->topic);

        $topic = $resource->topic;
        $resource->delete();

        return redirect()->route('topics.show', $topic)
            ->with('success', 'Recurso removido.');
    }
}
