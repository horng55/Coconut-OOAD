<?php

namespace App\Http\Controllers\Web\Admin\User;

use AllowDynamicProperties;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserPostRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Repositories\Admin\User\UserRepository;
use Illuminate\Http\Request;

#[AllowDynamicProperties] class UserController extends Controller
{
    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $list = $this->user->list($search);

        return inertia('User/Index', [
            'paginate' => $list,
            'search' => $search,
        ]);
    }

    public function create()
    {
        $roles = \App\Models\Role::where('status', 'active')->get(['id', 'name', 'slug']);
        return inertia('User/Create', [
            'roles' => $roles,
        ]);
    }

    public function store(UserPostRequest $request)
    {
        $this->user->store($request->validated());
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = $this->user->find($id);
        $user->load('roles');
        $roles = \App\Models\Role::where('status', 'active')->get(['id', 'name', 'slug']);
        return inertia('User/Edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function update(UserUpdateRequest $request, $id)
    {
        $this->user->update($id, $request->validated());
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function delete($id)
    {
        $this->user->delete($id);
        return redirect()->route('users.index')->with('success', 'UserSidebar deleted successfully.');
    }
}
