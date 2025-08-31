<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Pages;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;

class PagesController extends Controller
{
    public function index()
    {
        $pages = Pages::all();
        return view('admin.pages.index', compact('pages'));

    }

    public function store(Request $request)
    {
        // Form validation
        $request->validate([
            'title' => 'required|unique:pages',
            'description' => 'required',
            'status' => 'integer|in:0,1',
            'meta_title' => 'required',
            'meta_keywords' => 'required',
            'meta_description' => 'required',
        ],[
            'title.required' => 'Page Title is Required',
            'title.unique' => 'Page Title is already used',
            'description.required' => 'Description is Required',
            'status.integer' => 'Status Field should be an Integer',
            'meta_title.required' => 'Meta Title is Required',
            'meta_keywords.required' => 'Meta Keywords is Required',
            'meta_description.required' => 'Meta Description is Required',
        ]);

        $page = new Pages();
        $page->title = $request->title;
        $page->slug = Str::slug($request->title, '-');
        $page->description = $request->description;
        $page->status = $request->status;
        $page->meta_title = $request->meta_title;
        $page->meta_keywords = $request->meta_keywords;
        $page->meta_description = $request->meta_description;
        if ($page->save()) {

            return redirect()->back()->with('success', 'Page added successfully');

        }
        else{

            return redirect()->back()->with('error', 'Something went wrong!');

        }
    }

    public function edit($id)
    {
		$page = Pages::find($id);
        return view('admin.pages.index', ["page" => $page]);
    }

    public function update(Request $request)
    {
        // Form validation
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'status' => 'integer|in:0,1',
            'meta_title' => 'required',
            'meta_keywords' => 'required',
            'meta_description' => 'required',
        ],[
            'title.required' => 'Page Title is Required',
            'title.unique' => 'Page Title is already used',
            'description.required' => 'Description is Required',
            'status.integer' => 'Status Field should be an Integer',
            'meta_title.required' => 'Meta Title is Required',
            'meta_keywords.required' => 'Meta Keywords is Required',
            'meta_description.required' => 'Meta Description is Required',
        ]);

        $page = Pages::find($request->id);
        $page->title = $request->title;
        $page->slug = Str::slug($request->title, '-');
        $page->description = $request->description;
        $page->status = $request->status;
        $page->meta_title = $request->meta_title;
        $page->meta_keywords = $request->meta_keywords;
        $page->meta_description = $request->meta_description;
        if ($page->update()) {

            return redirect()->back()->with('success', 'Page successfully updated');

        }
        else{

            return redirect()->back()->with('error', 'Something went wrong!');

        }
    }

    public function view(Request $request)
    {
		$id = $request->id;
		$emp = Pages::find($id);
		return response()->json($emp);
    }

	public function destroy(Request $request){

        $id = $request->id;
        $page = Pages::find($id);
        $page->delete();
        return response()->json(['status' => 'Page Deleted Successfully']);
	}
}
