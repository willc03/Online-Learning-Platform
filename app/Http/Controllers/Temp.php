<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Temp extends Controller
{
    /**
     * This function will display a simple non-styled form for testing file submissions
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function file_index()
    {
        return view('temp_file');
    }

    /**
     * The public function upload_file is designed to process file uploads,
     * delivering  them to the  correct course folder  and returning the
     * necessary path to the file.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function upload_file(Request $request)
    {
        // Check if there is a course in the
        if (!$request->session()->has('course')) {
            return back();
        }
        // Check if the user has permission to edit files
        $course = Course::where('id', session()->get('course'))->firstOrFail();
        if (! Gate::allows('file-upload', $course)) {
            return abort(403);
        }
        // If the user has permission, upload the file
        $file_path = $this->getFileStoragePath($request);
        $file = $request->file('file');

        $generated_path = $file->store($file_path);

        return back();
    }
}
