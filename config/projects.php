<?php

return [
    /*
    |--------------------------------------------------------------------------
    | EXAM STUDY NOTE — How this file works
    |--------------------------------------------------------------------------
    |
    | This file lists all your practice projects.
    | The project-switcher header is built from this array automatically.
    |
    | Each key is the URL "slug":  /project/todo  /project/blog
    |
    | Each project MUST have matching views:
    |   resources/views/projects/{slug}/home.blade.php
    |   resources/views/projects/{slug}/dashboard.blade.php
    |
    | TO ADD A NEW EXAM PROJECT:
    |   1. Add an entry here (slug + name + description + color)
    |   2. Create the views folder with home.blade.php + dashboard.blade.php
    |   That's it — the routes and switcher nav update automatically.
    |
    | Available Tailwind colors for 'color': indigo, emerald, rose, amber,
    |   sky, violet, orange, teal, pink, lime, cyan, fuchsia
    |
    */

    'todo' => [
        'name'        => 'TodoApp',
        'description' => 'Practice: build a todo list with CRUD',
        'color'       => 'indigo',
    ],

    'blog' => [
        'name'        => 'BlogApp',
        'description' => 'Practice: build a blog with posts and comments',
        'color'       => 'emerald',
    ],
];
