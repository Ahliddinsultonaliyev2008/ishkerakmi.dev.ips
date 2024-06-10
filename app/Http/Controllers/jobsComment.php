<?php

namespace App\Http\Controllers;

use App\Services\jobsCommentServise;
use Illuminate\Http\Request;

class jobsComment extends Controller
{
    public function __construct(protected jobsCommentServise $jobsCommentServise)
    {

    }


    function index(Request $request)
    {
        return $this -> jobsCommentServise ->index($request ->post());
    }

    function store(Request $request)
    {
        return $this -> jobsCommentServise ->store($request ->post());
    }

    function update(Request $request, $id)
    {
        return $this -> jobsCommentServise ->update($request ->post(), $id);
    }

    function destroy($id)
    {
        return $this -> jobsCommentServise ->destroy($id);
    }
}
