<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\Category;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::with('category')->where('user_id', auth()->id());

        //Action: if there is a 'status' in the URL, add it to the query

        if($request->has('status')){
            $query->where('status', $request->status);
        }

        
    // ACTION: If there is a 'category_id', add it too

    if($request->has('category_id')) {
        $query->where('category_id', $request->category_id);
    }
    
    return inertia('Tasks/Index', [
            'tasks' => $query->latest()->get(),'categories' => Category::where('user_id', auth()->id())->get(),
            'filters' => $request->only(['status', 'category_id'])
        ]);
    }

    public function create()
    {

        $category = Category::where('user_id', auth()->id())->get()->map(function($cat){
            return [
                '_id' =>(string)$cat->_id,
                'name' => $cat->name
            ];
        });
        return inertia('Tasks/Create', [
            'categories' => $category
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'priority' => 'required|string|in:low,medium,high',
            'category_id' => 'required|string'
        ]);

        $request->user()->tasks()->create($validated);

        return redirect()->route('tasks.index');
    }


    public function edit(Task $task)
    {
        return inertia('Tasks/Edit', ['task' => $task]);
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'priority' => 'required|string|in:low,medium,high',
            'category_id' => 'required|string'
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index');
    }
}
