<?php

namespace App\Http\Controllers;

use App\Models\Project;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user_id=$request->header('id');
        $user = User::findOrFail($user_id);
        if ($user->role == "admin")
        {
            $projects = Project::all();
            return response()->json(['projects' => $projects], 200);
        }else{
            $projects = Project::where('participants', 'LIKE', '%' . $user->email . '%')
                ->where('status', 'active')
                ->get();
            return response()->json(['projects' => $projects], 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        $validator = Validator::make(request()->all(), [
            'title' => 'required',
            'description' => 'required',
            'participants' => 'required',
            'color' => 'required',
            'status'=> 'sometimes|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }


        try {
            $project = Project::create([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'participants' => $request->input('participants'),
                'color' => $request->input('color'),
                'status' => $request->input('status', "active"),
                'startDate' => Carbon::parse($request->input('startDate', now())),
            ]);
            return response()->json(['project' => $project, 'message' => 'Project crated successfully'], 200);

        } catch (Exception $e) {
            return response()->json([
                'errors' => "Failed to create project"
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $project = Project::findOrFail($id);
        if ($project == null)
        {
            return response()->json(['errors' => "Project not found"], 422);
        }
        return response()->json(['project' => $project], 200);
    }

    public function projectDetails(string $id)
    {
        $project = Project::findOrFail($id);
        if ($project == null)
        {
            return response()->json(['errors' => "Project not found"], 422);
        }
        $projectParticipantsString = $project->participants;
        $projectParticipantsStringArray = explode(',', $projectParticipantsString);
        $resultsArray = [];

        foreach ($projectParticipantsStringArray as $email) {
            $user = User::where('email', $email)->select('id', 'name')->first();
            if ($user !== null){
                $resultsArray[] = $user;
            }
        }

        return response()->json(['users' => $resultsArray], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make(request()->all(), [
            'title' => 'required',
            'description' => 'required',
            'participants' => 'required',
            'color' => 'required',
            'status'=> 'sometimes|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $project = Project::findOrFail($id)->update([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'participants' => $request->input('participants'),
                'color' => $request->input('color'),
                'status' => $request->input('status', "active"),
                'startDate' => $request->input('startDate', now()),
            ]);

            return response()->json(['project' => $project, 'message' => 'Project update successfully'], 200);

        } catch (Exception $e) {
            return response()->json([
                'errors' => "Failed to update project"
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);
        if ($project == null)
        {
            return response()->json(['errors' => "Project not found"], 404);
        }
        $project->delete();
        return response()->json(['message' => 'Project delete successfully'], 200);
    }
}
