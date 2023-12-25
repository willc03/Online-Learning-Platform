<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Write a protected function to get the resource storage path for
     * the required course in child controllers.
     *
     * @param Request $request
     * @return string|null
     */
    protected function getFileStoragePath(string $courseId)
    {
        if ($courseId && $course = Course::find($courseId)) {
            return "courses/{$course->id}";
        }

        return null;
    }

}
