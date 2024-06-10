<?php

namespace App\Http\Controllers;

use App\Services\jobslikeServise;
use Illuminate\Http\Request;

class jobslike extends Controller
{
    public function __construct(protected jobslikeServise $jobslikeServise)
    {

    }
    function store(Request $request)
    {
        return $this -> jobslikeServise ->store($request ->post());
    }
    function destroy($id)
    {
        return $this -> jobslikeServise ->destroy($id);
    }
}
