<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\LessonItem as LessonItemModel;
use Illuminate\Validation\Rule;

class LessonItem extends Controller
{
    private array $allowedItemTypes = ['question', 'text'];

    public function create(Request $request, string $id, string $lessonId)
    {
        // Exterior validation must first occur to ensure the content is in the correct format
        $validatedData = $request->validate([
            'item-type' => [ 'required', 'string', 'in:' . implode(',', $this->allowedItemTypes) ],
            'item-title' => [ 'required', 'string' ],
            'item-description' => [ 'nullable', 'string' ]
        ]);
        // Create component
        $lessonItem = new LessonItemModel;
        // Set the title, description, and value
        if ($validatedData['item-type'] == 'question') {
            $lessonItem->item_type = 'QUESTION';
        } elseif ($validatedData['item-type'] == 'text') {
            $lessonItem->item_type = 'TEXT';
            $lessonItem->item_title = $validatedData['item-title'];
            if (array_key_exists('item-description', $validatedData)) {
                $lessonItem->description = $validatedData['item-description'];
            }
            $lessonItem->item_value = [];
        }
        $lessonItem->position = LessonItemModel::whereLessonId($lessonId)->count() + 1;
        $lessonItem->lesson_id = $lessonId;
        // Save component
        if ($lessonItem->save()) {
            return redirect()->to(route('course.lesson.configure.get', [ 'id' => $id, 'lessonId' => $lessonId ]));
        } else {
            return back()->withErrors([ 'LESSON_ITM' => 'Could not create a lesson item.' ]);
        }
    }

    public function modify(string $id, string $lessonId)
    {

    }

    public function delete(string $id, string $lessonId)
    {

    }
}
