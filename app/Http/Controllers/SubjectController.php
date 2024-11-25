<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubjectController
{
    public function list(Request $request): Response
    {
        return Inertia::render('Subjects/List', [
            'subjects' => []
        ]);
    }
}