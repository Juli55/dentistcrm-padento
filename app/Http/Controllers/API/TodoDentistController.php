<?php

namespace App\Http\Controllers\API;

use App\DentistContact;
use App\TodoDentist;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TodoDentistController extends Controller
{
    public function index()
    {
        return TodoDentist::latest()->orderBy('order')->get();
    }

    public function getTodoForContact($dentistId)
    {
        $contact = DentistContact::find($dentistId);

        return $contact->tasks;
    }

    public function show($id)
    {
        return TodoDentist::findOrFail($id);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
        ]);

        $contact_id = $request->contact_id;

        $todos = TodoDentist::where('contact_id', $contact_id)->orderBy('order', 'desc')->get();

        foreach ($todos as $todo) {
          $todo->order = $todo->order+1;
          $todo->save();
        }
        $order = 1;

        return TodoDentist::create([
            'title' => $request->title,
            'is_queued' => $request->is_queued ?: 0,
            'contact_id' => $request->contact_id,
            'order' => $order,
            'creator_id' => $request->user()->id
        ]);
    }

    public function update($id, Request $request)
    {
        $todo = TodoDentist::findOrFail($id);
        $user = $request->user();

        abort_unless($user->hasRole('admin') || $user->id == $todo->creator_id, 403);

        $this->validate($request, [
            'title' => 'required',
        ]);

        $todo->update([
            'title' => $request->title,
            'is_queued' => $request->is_queued ?: 0,
        ]);

        return $todo;
    }

    public function destroy($id)
    {
        $todo = TodoDentist::findOrFail($id);
        $user = auth()->user();

        abort_unless($user->hasRole('admin') || $user->id == $todo->creator_id, 403);

        $todo->delete();

        return response()->json('success');
    }

    public function toggleComplete(Request $request)
    {
        $task = TodoDentist::find($request->task_id);

        $task->toggleComplete();

        if($task->isComplete()) {
            activity()->causedBy($request->user())->performedOn($task->dentist)->withProperties(['task' => $task->title])->log('task_completed');
        } else {
            activity()->causedBy($request->user())->performedOn($task->dentist)->withProperties(['task' => $task->title])->log('task_uncompleted');
        }

        return $task;
    }

    public function sort(Request $request)
    {
        $tasks = TodoDentist::whereIn('id', $request->ids)->where('contact_id', $request->contact_id)->get();

        return $this->sortTasks($tasks, $request->ids);
    }


    private function sortTasks($tasks, $ids)
    {
        $order = 1;

        foreach ($ids as $id) {
            $task = $tasks->where('id', $id)->first();

            $task->order = $order;
            $task->save();

            $order++;
        }

        return $tasks->sortBy('order')->values()->all();
    }
}
