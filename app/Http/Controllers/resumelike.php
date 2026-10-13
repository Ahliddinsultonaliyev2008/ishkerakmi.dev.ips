<?php

namespace App\Http\Controllers;

use App\Services\ResumelikeServies;
use Illuminate\Http\Request;

class resumelike extends Controller
{
    public function __construct(protected ResumelikeServies $resumelikeServise)
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
