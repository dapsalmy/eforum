<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Admin\EmailTemplate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class EmailTemplatesController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::all();
        return view('admin.email.template', ['templates' => $templates]);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'subject' => 'required',
            'body' => 'required',
        ],[
            'name.required' => 'Category Name is Required',
            'name.string' => 'Category Name should be a String',
            'name.max' => 'Category Name max characters should be 255',
            'subject.required' => 'Subject is Required',
            'body.required' => 'Body is Required',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }


        $item = new EmailTemplate();
        $item->name = $request->name;
        $item->subject = $request->subject;
        $item->body = $request->body;
        if ($item->save()) {

            return response()->json([
                'status' => 200,
                'messages' => 'Email Template added Successfully'
            ]);

        }
        else{

            return response()->json([
                'status' => 401,
                'messages' => 'Error, Something went wrong'
            ]);

        }
    }

    public function edit($id)
    {
		$template = EmailTemplate::find($id);
        return view('admin.email.template', ["template" => $template]);
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'subject' => 'required',
            'body' => 'required',
        ],[
            'name.required' => 'Category Name is Required',
            'name.string' => 'Category Name should be a String',
            'name.max' => 'Category Name max characters should be 255',
            'subject.required' => 'Subject is Required',
            'body.required' => 'Body is Required',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }

        $item = EmailTemplate::find($request->template_id);
        $item->name = $request->name;
        $item->subject = $request->subject;
        $item->body = $request->body;
        if ($item->update()) {

            return response()->json([
                'status' => 200,
                'messages' => 'Email Template updated Successfully'
            ]);
        }
        else{

            return response()->json([
                'status' => 401,
                'messages' => 'Something went wrong'
            ]);

        }
    }

	public function destroy(Request $request){

        $id = $request->id;
        $item = EmailTemplate::find($id);
        $item->delete();
        return response()->json(['status' => 'Email Template Deleted Successfully']);
	}
}
