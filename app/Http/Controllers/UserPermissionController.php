<?php

namespace App\Http\Controllers;

use App\Models\user_permission;
use App\Http\Requests\Storeuser_permissionRequest;
use App\Http\Requests\Updateuser_permissionRequest;

class UserPermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Storeuser_permissionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(user_permission $user_permission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(user_permission $user_permission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Updateuser_permissionRequest $request, user_permission $user_permission)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(user_permission $user_permission)
    {
        //
    }
}
