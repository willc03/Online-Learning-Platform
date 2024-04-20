<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseFile as CourseFileModel;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CourseFile extends Controller
{

    /**
     * The 'serve' route is intended to deliver a viewable version of a file. This way of
     * implementing the file system allows for files to remain private and for error messages
     * to be delivered if the user does not have permission or the file does not exist.
     *
     * @param Request $request The HTTP request provided by Laravel
     * @param string  $id      The course's id (UUID)
     * @param string  $fileId  The file's id (UUID)
     *
     * @return Response|BinaryFileResponse Either an error response or a file response.
     */
    public function serve ( Request $request, string $id, string $fileId )
    {
        // Check course permission
        $course = Course::findOrFail($id);
        if ( !UserCourse::where([ 'course_id' => $id, 'user_id' => $request->user()->id ])->exists() && $request->user()->id !== $course->owner ) {
            return response('You do not have access to files for ' . $id, 403);
        }

        // Get the file
        $file = CourseFileModel::findOrFail($fileId);
        $disk = Storage::disk('private');

        // Check the file exists
        if ( !$disk->exists($file->path) ) {
            return response("The specified file does not exist.", 404);
        }

        // Return the file
        return response()->file(storage_path('app/courses/' . $file->path), [ 'Content-Type' => mime_content_type(storage_path('app/courses/' . $file->path)) ]);
    }

    /**
     * The download route is used to allow the user to request files to be downloaded. The response
     * provided by Laravel's download() response forces the user's browser to process a download
     * payload (if the user allows the download, that is).
     *
     * @param string $id     The course's id
     * @param string $fileId The file's id (UUID)
     *
     * @return Response|BinaryFileResponse An error response or a file download force response
     */
    public function download ( string $id, string $fileId )
    {
        // We can assume the user has file access permission due to the course middleware being used on the route.
        // Get the file
        $file = CourseFileModel::where('id', $fileId)->firstOrFail();
        $disk = Storage::disk('private');

        // Check the file exists
        if ( !$disk->exists($file->path) ) {
            return response("The specified file does not exist.", 403);
        }

        // Return the file
        return response()->download(storage_path('app/courses/' . $file->path));
    }

    /**
     * The upload public route is designed to process file uploads, delivering them to the correct course
     * folder and returning the necessary path to the file.
     *
     * @param Request $request The HTTP request provided by Laravel
     * @param string  $id      The course's id (UUID)
     *
     * @return array An array containing whether the upload was successful, an/or an error code/message.
     */
    public function upload ( Request $request, string $id )
    {
        // Validate the file upload form
        try {
            $validated_data = $request->validate([
                'name' => [ 'required', 'string' ],
                'file' => [ 'required', 'file' ],
                'id'   => [ 'required', 'string', 'exists:courses', 'in:' . $id ],
            ]);
        } catch ( ValidationException $error ) {
            return [ false, $error->getMessage() ];
        }
        // Course permission checks
        $course = Course::find($id);
        if ( !Gate::allows('course-edit', $course) ) { // Protectively ensure the user can make edits if a new route is defined.
            return [ false, "You cannot complete this action as you do not own this course." ];
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
        // Return a 200
        return [ true, 200 ];
    }

    /**
     * The remove route is designed to allow course owners to delete files from
     * their course.
     *
     * @param Request $request The HTTP request provided by Laravel
     * @param string  $id      The course's id (UUID)
     *
     * @return Response The response sent to the user's browser
     */
    public function remove ( Request $request, string $id )
    {
        // Validate the uploaded data
        $request->validate([
            'fileId' => [ 'required', 'string', 'exists:course_files,id' ],
        ]);
        // Course permission checks
        $course = Course::find($id);
        if ( !Gate::allows('course-edit', $course) ) { // Protectively ensure the user can make edits if a new route is defined.
            return response("You cannot complete this action as you do not own this course.", 403);
        }
        // Delete the file (or rather, attempt to)
        $file = CourseFileModel::where('id', $request->fileId)->firstOrFail();
        $disk = Storage::disk('private');
        if ( !$disk->exists($file->path) ) {
            return response("The requested file could not be found.", 404);
        }
        // If the file exists, begin a database transaction.
        DB::beginTransaction();
        try {
            if ( !$disk->delete($file->path) ) { // Check if the file can be deleted
                throw new \Exception("The requested file could not be deleted.");
            } else {
                $file->delete(); // Try to delete the record.
            }
            DB::commit();                                               // Commit if there are no errors
            return response("The file was successfully deleted.", 200); // Success message
        } catch ( \Exception $exception ) {
            DB::rollBack();                                                            // Roll back on any errors
            return response("Encountered an error: " . $exception->getMessage(), 500); // Respond with the error message in a 500 error.
        }
    }

}
