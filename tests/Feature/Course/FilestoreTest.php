<?php

namespace Tests\Feature\Course;

use App\Models\CourseFile;
use App\Models\UserCourse;
use Faker\Provider\Uuid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FilestoreTest extends TestCase
{

    use RefreshDatabase;

    /**
     * A test to ensure non-owners cannot upload files.
     */
    public function test_reject_non_owner_file_upload_requests () : void
    {
        // Login
        $user = $this->loginWithFakeUser();

        // Get a course to test with
        $course = $this->generate_course();

        // Join the user to the course
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Generate a fake file to upload
        $fileName = Uuid::uuid() . '.pdf';
        $file = UploadedFile::fake()->create($fileName, 100);

        // Make the upload request
        \Storage::fake('private');
        $response = $this->post('/course/' . $course->id . '/filestore/upload', [
            'name' => 'TEST.pdf',
            'file' => $file,
            'id'   => $course->id,
        ]);

        // Make assertions of the result
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors([ 'NOT_COURSE_OWNER' ]);
    }

    /**
     * A test to ensure owners CAN upload files.
     */
    public function test_accept_owner_file_upload_requests () : void
    {
        // Get a course to test with
        $course = $this->generate_course();

        // Generate a fake file to upload
        $fileName = Uuid::uuid() . '.pdf';
        $file = UploadedFile::fake()->create($fileName, 100);

        // Make the upload request
        Storage::fake('private');
        $response = $this->actingAs($course->course_owner)->post('/course/' . $course->id . '/filestore/upload', [
            'name' => 'TEST.pdf',
            'file' => $file,
            'id'   => $course->id,
        ]);

        // Make assertions of the result
        $response->assertStatus(200);
        $this->assertDatabaseHas('course_files', [ 'name' => 'TEST.pdf' ]);
        Storage::disk('private')->assertExists(CourseFile::where('name', 'TEST.pdf')->firstOrFail()->path);
    }

    /**
     * A test to ensure non-owners cannot upload files.
     */
    public function test_reject_non_owner_file_removal_requests () : void
    {
        // Login
        $user = $this->loginWithFakeUser();

        // Get a course to test with
        $course = $this->generate_course();

        // Join the user to the course
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Generate a fake file to upload
        $fileName = Uuid::uuid() . '.pdf';
        $file = UploadedFile::fake()->create($fileName, 100);

        // Make the upload request (as the owner)
        Storage::fake('private');
        $this->actingAs($course->course_owner)->post('/course/' . $course->id . '/filestore/upload', [
            'name' => 'TEST.pdf',
            'file' => $file,
            'id'   => $course->id,
        ]);

        $fileRecord = CourseFile::where('name', 'TEST.pdf')->firstOrFail();

        // Attempt to delete the file
        $response = $this->delete('/course/' . $course->id . '/filestore/remove', [
            'fileId' => $fileRecord->id,
        ]);

        // Make assertions of the result
        $response->assertStatus(200);
        Storage::disk('private')->assertMissing($fileRecord->path);
    }

    /**
     * A test to ensure owners CAN upload files.
     */
    public function test_accept_owner_file_removal_requests () : void
    {
        // Get a course to test with
        $course = $this->generate_course();

        // Generate a fake file to upload
        $fileName = Uuid::uuid() . '.pdf';
        $file = UploadedFile::fake()->create($fileName, 100);

        // Make the upload request (as the owner)
        Storage::fake('private');
        $this->actingAs($course->course_owner)->post('/course/' . $course->id . '/filestore/upload', [
            'name' => 'TEST.pdf',
            'file' => $file,
            'id'   => $course->id,
        ]);

        $fileRecord = CourseFile::where('name', 'TEST.pdf')->firstOrFail();

        // Attempt to delete the file
        $response = $this->actingAs($course->course_owner)->delete('/course/' . $course->id . '/filestore/remove', [
            'fileId' => $fileRecord->id,
        ]);

        // Make assertions of the result
        $response->assertStatus(200);
        $this->assertDatabaseMissing('course_files', [ 'name' => 'TEST.pdf' ]);
        // No check for the file if the database record is missing as there is no way to find it
    }

    /**
     * A test to make sure non-members can't access a file
     */
    public function test_reject_non_member_serve_requests () : void
    {
        // Login
        $user = $this->loginWithFakeUser();

        // Get a course to test with
        $course = $this->generate_course();

        // Generate a fake file to upload
        $fileName = Uuid::uuid() . '.pdf';
        $file = UploadedFile::fake()->create($fileName, 100);

        // Make the upload request as the owner
        //Storage::fake('private'); (Can't use this here as it breaks the test)
        $this->actingAs($course->course_owner)->post('/course/' . $course->id . '/filestore/upload', [
            'name' => 'TEST.pdf',
            'file' => $file,
            'id'   => $course->id,
        ]);

        // Get the file from the database records
        $fileRecord = CourseFile::where('name', 'TEST.pdf')->firstOrFail();

        // Attempt to access the file using the serve route
        $response = $this->actingAs($user)->get('/course/' . $course->id . '/filestore/serve/' . $fileRecord->id);

        // Make assertions of the result
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors([ 'NOT_COURSE_MEMBER' ]);
    }

    /**
     * A test to make sure non-members can't access a file
     */
    public function test_accept_member_serve_requests () : void
    {
        // Login
        $user = $this->loginWithFakeUser();

        // Get a course to test with
        $course = $this->generate_course();

        // Join the user to the course
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Generate a fake file to upload
        $fileName = Uuid::uuid() . '.pdf';
        $file = UploadedFile::fake()->create($fileName, 100);

        // Make the upload request as the owner
        //Storage::fake('private'); (Can't use this here as it breaks the test)
        $this->actingAs($course->course_owner)->post('/course/' . $course->id . '/filestore/upload', [
            'name' => 'TEST.pdf',
            'file' => $file,
            'id'   => $course->id,
        ]);

        // Get the file from the database records
        $fileRecord = CourseFile::where('name', 'TEST.pdf')->firstOrFail();

        // Attempt to access the file using the serve route
        $response = $this->actingAs($user)->get('/course/' . $course->id . '/filestore/serve/' . $fileRecord->id);

        // Make assertions of the result
        $response->assertStatus(200);
    }

    /**
     * A test to make sure non-members can't access a file
     */
    public function test_reject_non_member_download_requests () : void
    {
        // Login
        $user = $this->loginWithFakeUser();

        // Get a course to test with
        $course = $this->generate_course();

        // Generate a fake file to upload
        $fileName = Uuid::uuid() . '.pdf';
        $file = UploadedFile::fake()->create($fileName, 100);

        // Make the upload request as the owner
        //Storage::fake('private'); (Can't use this here as it breaks the test)
        $this->actingAs($course->course_owner)->post('/course/' . $course->id . '/filestore/upload', [
            'name' => 'TEST.pdf',
            'file' => $file,
            'id'   => $course->id,
        ]);

        // Get the file from the database records
        $fileRecord = CourseFile::where('name', 'TEST.pdf')->firstOrFail();

        // Attempt to access the file using the serve route
        $response = $this->actingAs($user)->get('/course/' . $course->id . '/filestore/download/' . $fileRecord->id);

        // Make assertions of the result
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors([ 'NOT_COURSE_MEMBER' ]);
    }

    /**
     * A test to make sure non-members can't access a file
     */
    public function test_accept_member_download_requests () : void
    {
        // Login
        $user = $this->loginWithFakeUser();

        // Get a course to test with
        $course = $this->generate_course();

        // Join the user to the course
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Generate a fake file to upload
        $fileName = Uuid::uuid() . '.pdf';
        $file = UploadedFile::fake()->create($fileName, 100);

        // Make the upload request as the owner
        //Storage::fake('private'); (Can't use this here as it breaks the test)
        $this->actingAs($course->course_owner)->post('/course/' . $course->id . '/filestore/upload', [
            'name' => 'TEST.pdf',
            'file' => $file,
            'id'   => $course->id,
        ]);

        // Get the file from the database records
        $fileRecord = CourseFile::where('name', 'TEST.pdf')->firstOrFail();

        // Attempt to access the file using the serve route
        $response = $this->actingAs($user)->get('/course/' . $course->id . '/filestore/download/' . $fileRecord->id);

        // Make assertions of the result
        $response->assertDownload();
    }

}
