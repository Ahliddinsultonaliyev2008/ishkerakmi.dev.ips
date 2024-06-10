<?php

namespace App\Http\Controllers;

use App\Services\resumelikeServies;
use Illuminate\Http\Request;

class resumelike extends Controller
{
    public function __construct(protected resumelikeServies $resumelikeServise)
    {

    }
    function store(Request $request)
    {
        return $this -> resumelikeServise ->store($request ->post());
    }
    function destroy($id)
    {
        return $this -> resumelikeServise ->destroy($id);
    }
}
