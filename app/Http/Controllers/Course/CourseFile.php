<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\UserCourse;
use App\Models\CourseFile as CourseFileModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CourseFile extends Controller
{
    public function serve(Request $request, $id, $fileId)
    {
        // Check course permission
        $course = Course::findOrFail($id);
        if (!UserCourse::where(['course_id' => $id, 'user_id' => $request->user()->id])->exists() && $request->user()->id !== $course->owner) {
            return response('You do not have access to files for ' . $id, 403);
        }

        // Get the file
        $file = CourseFileModel::findOrFail($fileId);
        $disk = Storage::disk('private');

        // Check the file exists
        if (!$disk->exists($file->path)) {
            return response("The specified file does not exist.", 404);
        }

        // Return the file
        return response()->file(storage_path('app/courses/' . $file->path), ['Content-Type' => mime_content_type(storage_path('app/courses/' . $file->path))]);
    }

    public function download(Request $request, $id, $fileId)
    {
        // Check course permission
        $course = Course::findOrFail($id);
        if (!UserCourse::where(['course_id' => $id, 'user_id' => $request->user()->id])->exists() && $request->user()->id !== $course->owner) {
            return response('You do not have access to files for ' . $id, 403);
        }

        // Get the file
        $file = CourseFileModel::findOrFail($fileId);
        $disk = Storage::disk('private');

        // Check the file exists
        if (!$disk->exists($file->path)) {
            return response("The specified file does not exist.", 404);
        }

        // Return the file
        return response()->download(storage_path('app/courses/' . $file->path));
    }

    /**
     * The public function upload_file is designed to process file uploads,
     * delivering  them to the  correct course folder  and returning the
     * necessary path to the file.
     *
     * @param Request $request
     * @param $id
     * @return array
     */
    public function upload(Request $request, $id)
    {
        // Validate the file upload form
        try {
            $validated_data = $request->validate([
                'name' => ['required', 'string'],
                'file' => ['required', 'file'],
                'id' => ['required', 'string', 'exists:courses', 'in:'.$id]
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
        $file = $request->file('file');
        $generated_path = $file->storeAs($validated_data['id'], $file->getClientOriginalName(), 'private');
        // Upload the file details to the database
        $file = new CourseFileModel;
        $file->name = $validated_data['name'];
        $file->path = $generated_path;
        $file->course_id = $validated_data['id'];
        $file->save();
        // Redirect the user
        return [true];
    }
}