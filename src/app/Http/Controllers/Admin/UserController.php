<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status', 'all');

        $query = User::query()->orderBy('name');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        match ($status) {
            'active' => $query->where('is_active', true),
            'blocked' => $query->where('is_active', false),
            'admin' => $query->where(function ($sub) {
                $sub->where('is_admin', true)
                    ->orWhereIn(DB::raw('LOWER(email)'), config('chronos.admin_emails', []));
            }),
            default => null,
        };

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'q' => $q,
            'status' => $status,
        ]);
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);

        $subjectsCount = $user->subjects()->count();
        $topicsCount = \App\Models\Topic::whereIn(
            'subject_id',
            $user->subjects()->select('id')
        )->count();
        $resourcesCount = \App\Models\Resource::whereIn(
            'topic_id',
            \App\Models\Topic::whereIn('subject_id', $user->subjects()->select('id'))->select('id')
        )->count();
        $pendingReviews = \App\Models\StudySchedule::whereIn(
            'topic_id',
            \App\Models\Topic::whereIn('subject_id', $user->subjects()->select('id'))->select('id')
        )->where('status', 'pending')
         ->whereDate('next_review_at', '<=', now())
         ->count();

        return view('admin.users.show', [
            'user' => $user,
            'summary' => [
                'subjects' => $subjectsCount,
                'topics' => $topicsCount,
                'resources' => $resourcesCount,
                'pendingReviews' => $pendingReviews,
            ],
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $user->name = $request->validated('name');
        $user->email = $request->validated('email');
        $user->save();

        return back()->with('status', 'Usuário atualizado com sucesso.');
    }

    public function toggleActive(User $user): RedirectResponse
    {
        $this->authorize('toggleActive', $user);

        $user->is_active = ! $user->is_active;
        $user->save();

        return back()->with(
            'status',
            $user->is_active ? 'Usuário ativado.' : 'Usuário bloqueado.'
        );
    }

    public function toggleAdmin(User $user): RedirectResponse
    {
        $this->authorize('toggleAdmin', $user);

        $user->is_admin = ! $user->is_admin;
        $user->save();

        return back()->with(
            'status',
            $user->is_admin ? 'Usuário promovido a administrador.' : 'Administrador rebaixado.'
        );
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Usuário excluído com sucesso.');
    }
}
