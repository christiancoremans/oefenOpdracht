<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — ProjectController
|--------------------------------------------------------------------------
|
| This controller handles two pages for every practice project:
|
|   home()      → public landing page  (no login required)
|   dashboard() → protected page       (login required — enforced in routes)
|
| In an exam, you would typically have one controller per resource/model,
| for example:
|   TodoController   → index, store, update, destroy
|   PostController   → index, show, create, store, edit, update, destroy
|
| How data flows to the view:
|   1. The controller reads config("projects.{slug}") to get project info
|   2. It passes the data to the view as an array: view('...', [ data ])
|   3. The view accesses these as $variables
|
| abort_if($condition, $code) → shorthand for:  if ($condition) { abort($code); }
|
*/

class ProjectController extends Controller
{
    /**
     * Show the project's public home/landing page.
     *
     * Route:  GET /project/{project}
     * Name:   project.home
     * Auth:   not required
     */
    public function home(string $project): \Illuminate\View\View
    {
        // If the slug is not in config/projects.php → 404 Not Found
        $projectConfig = config("projects.{$project}");
        abort_if(! $projectConfig, 404);

        // Pass data to the view
        // The view file is: resources/views/projects/{project}/home.blade.php
        return view("projects.{$project}.home", [
            'currentProject'     => $project,
            'projectName'        => $projectConfig['name'],
            'projectDescription' => $projectConfig['description'],
        ]);
    }

    /**
     * Show the project's protected dashboard.
     *
     * Route:  GET /project/{project}/dashboard
     * Name:   project.dashboard
     * Auth:   required  (enforced by 'auth' middleware in web.php)
     */
    public function dashboard(string $project): \Illuminate\View\View
    {
        $projectConfig = config("projects.{$project}");
        abort_if(! $projectConfig, 404);

        return view("projects.{$project}.dashboard", [
            'currentProject'     => $project,
            'projectName'        => $projectConfig['name'],
            'projectDescription' => $projectConfig['description'],
        ]);
    }
}
