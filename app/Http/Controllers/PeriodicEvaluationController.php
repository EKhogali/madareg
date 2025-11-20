<?php

namespace App\Http\Controllers;

use App\Models\PeriodicEvaluation;
use App\Http\Requests\StorePeriodicEvaluationRequest;
use App\Http\Requests\UpdatePeriodicEvaluationRequest;

class PeriodicEvaluationController extends Controller
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
    public function store(StorePeriodicEvaluationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PeriodicEvaluation $periodicEvaluation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PeriodicEvaluation $periodicEvaluation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePeriodicEvaluationRequest $request, PeriodicEvaluation $periodicEvaluation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PeriodicEvaluation $periodicEvaluation)
    {
        //
    }
}
