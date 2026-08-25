<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class LandingController extends Controller
{
    /**
     * Display the public marketing landing page.
     *
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('Landing/Index');
    }
}
