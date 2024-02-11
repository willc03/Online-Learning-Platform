<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseFile;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class File extends Controller
{
    /**
     * This function will display a simple non-styled form for testing file submissions
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function file_index()
    {
        return view('courses.file_upload');
    }

    /**
     * The public function upload_file is designed to process file uploads,
     * delivering  them to the  correct course folder  and returning the
     * necessary path to the file.
     *
     * @param Request $request
     * @return array
     */
    public function upload_file(Request $request)
    {
        // Validate the file upload form
        try {
            $validated_data = $request->validate([
                'name' => ['required', 'string'],
                'file' => ['required', 'file'],
                'id' => ['required', 'string', 'exists:courses']
            ]);
        } catch (ValidationException $error) {
            return [false, $error->getMessage()];
        }
        // Check if the user has permission to edit files
        $course = Course::where('id', $validated_data['id'])->firstOrFail();
        if (!Gate::allows('file-upload', $course)) {
            return [false, '403'];
        }
        // If the user has permission, upload the file
        $file_path = $this->getFileStoragePath($validated_data['id']);
        $file = $request->file('file');
        $generated_path = $file->storeAs($file_path, $file->getClientOriginalName());
        // Upload the file details to the database
        $file = new CourseFile;
        $file->name = $validated_data['name'];
        $file->path = $generated_path;
        $file->course_id = $validated_data['id'];
        $file->save();
        // Redirect the user
        return [true];
    }

    public function download(Request $request, $id, $fileId)
    {
        // Check course permission
        $course = Course::findOrFail($id);
        if (!UserCourse::where(['course_id' => $id, 'user_id' => $request->user()->id])->exists() && $request->user()->id !== $course->owner) {
            return response('Cannot download files from this course.', 403);
        }

        // Download the file
        $file = CourseFile::findOrFail($fileId);
        $path = storage_path('app/' . $file->path);

        if (!file_exists($path)) {
            return response("Requested file not found.", 404);
        }

        return response()->download($path);
    }

}
