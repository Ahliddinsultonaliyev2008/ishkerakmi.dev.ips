<?php

namespace App\Http\Controllers;

use App\Services\ResumeCommentServise;
use Illuminate\Http\Request;

class ResumeComment extends Controller
{
    public function __construct(protected ResumeCommentServise $resumeCommentServise)
    {

    }


    function index(Request $request)
    {
        return $this -> resumeCommentServise ->index($request ->post());
    }

    function store(Request $request)
    {
        return $this -> resumeCommentServise ->store($request ->post());
    }

    function update(Request $request, $id)
    {
        return $this -> resumeCommentServise ->update($request ->post(), $id);
    }
    function destroy($id)
    {
        return $this -> resumeCommentServise ->destroy($id);
    }
}
