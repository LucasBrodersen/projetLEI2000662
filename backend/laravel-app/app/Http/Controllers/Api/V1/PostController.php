<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    // Listar posts paginados (só visíveis para não-admin)
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Post::query();

        if (!$user || $user->type !== 'admin') {
            $query->where('visible', true);
        }

        $posts = $query->orderBy('created_at', 'desc')->paginate(5);

        return response()->json($posts);
    }

    // Criar post (só admin)
    public function store(Request $request)
    {
        $this->authorize('create', Post::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'visible' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        $post = Post::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'visible' => $validated['visible'] ?? true,
        ]);

        // Se houver imagem, salve na pasta do post
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store("posts/{$post->id}", 'public');
            $post->image_path = $imagePath;
            $post->save();
        }

        return response()->json($post, 201);
    }

    // Editar post (só admin)
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'visible' => 'sometimes|boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
            $post->image_path = $imagePath;
        }

        $post->fill($validated);
        $post->save();

        return response()->json($post);
    }

    // Deletar post (só admin)
    public function destroy(Request $request, Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully.']);
    }

    // Alternar visibilidade (só admin)
    public function toggleVisibility(Request $request, Post $post)
    {
        $this->authorize('toggleVisibility', $post);
        $post->visible = !$post->visible;
        $post->save();

        return response()->json($post);
    }

    public function uploadCkeditorImage(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|max:4096',
            'post_id' => 'nullable|integer',
        ]);

        $folder = 'posts/content';
        if ($request->post_id) {
            $folder = "posts/{$request->post_id}/content";
        }

        $path = $request->file('upload')->store($folder, 'public');
        $url = asset('storage/' . $path);

        return response()->json([
            'success' => 1,
            'file' => [
                'url' => $url,
                'name' => basename($path),
            ],
        ]);
    }

}