<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user_id=$request->header('id');
        $user = User::findOrFail($user_id);

        if (!$user){
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($user->role == "admin")
        {
            $tasks = Task::with('project:id,title')->with('users:id,name,email')->get();
            return response()->json(['tasks' => $tasks], 200);
        }else{

            $tasks = $user->userTasks()->with('users:id,name')->get();
            return response()->json(['tasks' => $tasks], 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'title' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
            'project_id' => 'required',
            'description' => 'required',
            'assign' => 'required',
            'status'=> 'sometimes|in:pending,ongoing,done'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $task = Task::create([
                'title' => $request->input('title'),
                'status' => $request->input('status', 'active'),
                'startDate' => $request->input('startDate'),
                'endDate' => $request->input('endDate'),
                'project_id' => $request->input('project_id'),
                'description' => $request->input('description'),
            ]);
            $userIds = explode(',', $request->input('assign'));
            $task->users()->attach($userIds);
            return response()->json(['project' => $task, 'message' => 'Task created successfully'], 200);

        } catch (Exception $e) {
            return response()->json([
                'errors' => "Failed to create project"
            ], 422);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $task = Task::findOrFail($id);
        if ($task == null)
        {
            return response()->json(['errors' => "Task not found"], 422);
        }
        return response()->json(['task' => $task], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make(request()->all(), [
            'title' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
            'project_id' => 'required',
            'description' => 'required',
            'assign' => 'required',
            'status'=> 'sometimes|in:pending,ongoing,done'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            $task = Task::findOrFail($id);
            if (!$task) {
                return response()->json(['error' => 'Task not found'], 404);
            }
            $task->update([
                'title' => $request->input('title'),
                'status' => $request->input('status', 'active'),
                'startDate' => $request->input('startDate'),
                'endDate' => $request->input('endDate'),
                'project_id' => $request->input('project_id'),
                'description' => $request->input('description'),
            ]);
            $userIds = explode(',', $request->input('assign'));
            $task->users()->sync($userIds);
            return response()->json(['task' => $task, 'message' => 'Task updated successfully'], 200);

        } catch (Exception $e) {
            return response()->json([
                'errors' => "Failed to update task"
            ], 422);
        }
    }

    /**
     *  Update specific task status for a specific user
     */
    public function taskStatusUpdate(Request $request, string $id)
    {
        $validator = Validator::make(request()->all(), [
            'status'=> 'required|in:pending,ongoing,done'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        $task = Task::findOrFail($id);
        $task->update([
            'status' => $request->input('status'),
        ]);
        return response()->json(['task' => $task, 'message' => 'Task updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);
        if ($task == null)
        {
            return response()->json(['errors' => "Task not found"], 404);
        }
        $task->users()->detach();
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully'], 200);
    }
}
