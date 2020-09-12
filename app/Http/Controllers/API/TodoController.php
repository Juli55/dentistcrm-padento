<?php

namespace App\Http\Controllers\API;

use App\Patient;
use App\Todo;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TodoController extends Controller
{
    public function index()
    {
        return [
            'withWaiting' => Todo::where('is_queued', 1)->where('contact_id', null)->orderBy('order')->get(),
            'withoutWaiting' => Todo::where('is_queued', 0)->where('contact_id', null)->orderBy('order')->get()
        ];
    }

    public function getTodoForContact($patientId)
    {
        $contact = Patient::find($patientId);

        return $contact->tasks;
    }

    public function show($id)
    {
        return Todo::findOrFail($id);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
        ]);

        $contact_id = $request->contact_id;

        $todos = Todo::where('contact_id', $contact_id)->orderBy('order', 'desc')->get();
        foreach ($todos as $todo) {
          $todo->order = $todo->order+1;
          $todo->save();
        }
        $order = 1;

        $todo = Todo::create([
            'title' => $request->title,
            'is_queued' => $request->is_queued ?: 0,
            'contact_id' => $request->contact_id,
            'order' => $order,
            'creator_id' => $request->user()->id
        ]);

        if($todo->patient) {
          activity()->causedBy($request->user())->performedOn($todo->patient)->withProperties(['task' => $todo->title, 'creator' => $request->user()->name])->log('task_created');
        } else {
          activity()->causedBy($request->user())->withProperties(['task' => $todo->title])->log('task_created');
        }

        return $todo;
    }

    public function update($id, Request $request)
    {
        $todo = Todo::findOrFail($id);
        $user = $request->user();

        abort_unless($user->hasRole('admin') || $user->hasRole('lab'), 403);

        $this->validate($request, [
            'title' => 'required',
        ]);

        $todo->update([
            'title' => $request->title,
            'is_queued' => $request->is_queued ?: 0,
        ]);

        if($todo->patient) {
          activity()->causedBy($request->user())->performedOn($todo->patient)->withProperties(['task' => $todo->title])->log('task_updated');
        } else {
          activity()->causedBy($request->user())->withProperties(['task' => $todo->title])->log('task_updated');
        }

        return $todo;
    }

    public function destroy($id)
    {
        $todo = Todo::findOrFail($id);
        $user = auth()->user();

        abort_unless($user->hasRole('admin') || $user->hasRole('lab'), 403);
        if ($todo->patient) {
          activity()->causedBy(auth()->user())->performedOn($todo->patient)->withProperties(['task' => $todo->title])->log('task_deleted');
        } else {
          activity()->causedBy(auth()->user())->withProperties(['task' => $todo->title])->log('task_deleted');
        }

        $todo->delete();

        return response()->json('success');
    }

    public function toggleComplete(Request $request)
    {
        $task = Todo::find($request->task_id);

        $task->toggleComplete();

        if($task->isComplete()) {
            activity()->causedBy($request->user())->performedOn($task->patient)->withProperties(['task' => $task->title])->log('task_completed');
        } else {
            activity()->causedBy($request->user())->performedOn($task->patient)->withProperties(['task' => $task->title])->log('task_uncompleted');
        }

        return $task;
    }

    public function sort(Request $request)
    {
        $tasks = Todo::whereIn('id', $request->ids)->where('contact_id', $request->contact_id)->get();

        return $this->sortTasks($tasks, $request->ids);
    }

    public function sortWithWaiting(Request $request)
    {
        $tasks = Todo::whereIn('id', $request->ids)->where('contact_id', null)->where('is_queued', 1)->get();

        return $this->sortTasks($tasks, $request->ids);
    }

    public function sortWithoutWaiting(Request $request)
    {
        $tasks = Todo::whereIn('id', $request->ids)->where('contact_id', null)->where('is_queued', 0)->get();

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
