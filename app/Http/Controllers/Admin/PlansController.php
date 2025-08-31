<?php

namespace App\Http\Controllers\Admin;

use App\Models\Plans;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PlansController extends Controller
{
    public function index()
    {
        $plans = Plans::orderBy('order', 'asc')->get();
        return view('admin.plans.index', ['plans' => $plans]);

    }

    public function add()
    {
        return view('admin.plans.index');
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'points' => 'required',
            'order' => 'required',
        ],[
            'name.required' => 'Name is Required',
            'name.string' => 'Name should be a String',
            'name.max' => 'Name max characters should be 255',
            'points.required' => 'Points is Required',
            'order.required' => 'Order is Required',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }

        $item = new Plans();
        $item->name = $request->name;
        $item->price = $request->price;
        $item->duration = $request->duration;
        $item->points = $request->points;
        $item->verified = ($request->verified == '1' ? '1' : '0');
        $item->categories = ($request->categories == '1' ? '1' : '0');
        $item->tips = ($request->tips == '1' ? '1' : '0');
        $item->comments = ($request->comments == '1' ? '1' : '0');
        $item->reactions = ($request->reactions == '1' ? '1' : '0');
        $item->followers = ($request->followers == '1' ? '1' : '0');
        $item->messages = ($request->messages == '1' ? '1' : '0');
        $item->users = ($request->users == '1' ? '1' : '0');
        $item->viewers = ($request->viewers == '1' ? '1' : '0');
        $item->ads = ($request->ads == '1' ? '1' : '0');
        $item->order = $request->order;
        $item->status = $request->status;
        if ($item->save()) {

            return response()->json([
                'status' => 200,
                'messages' => 'Plan added Successfully'
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
		$plan = Plans::find($id);
        return view('admin.plans.index', ["plan" => $plan]);
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'points' => 'required',
            'order' => 'required',
        ],[
            'name.required' => 'Name is Required',
            'name.string' => 'Name should be a String',
            'name.max' => 'Name max characters should be 255',
            'points.required' => 'Points is Required',
            'order.required' => 'Order is Required',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }

        $item = Plans::find($request->id);
        $item->name = $request->name;
        $item->price = $request->price;
        $item->duration = $request->duration;
        $item->points = $request->points;
        $item->verified = ($request->verified == '1' ? '1' : '0');
        $item->categories = ($request->categories == '1' ? '1' : '0');
        $item->tips = ($request->tips == '1' ? '1' : '0');
        $item->comments = ($request->comments == '1' ? '1' : '0');
        $item->reactions = ($request->reactions == '1' ? '1' : '0');
        $item->followers = ($request->followers == '1' ? '1' : '0');
        $item->messages = ($request->messages == '1' ? '1' : '0');
        $item->users = ($request->users == '1' ? '1' : '0');
        $item->viewers = ($request->viewers == '1' ? '1' : '0');
        $item->ads = ($request->ads == '1' ? '1' : '0');
        $item->order = $request->order;
        $item->status = $request->status;
        if ($item->update()) {

            return response()->json([
                'status' => 200,
                'messages' => 'Plan updated Successfully'
            ]);

        }
        else{

            return response()->json([
                'status' => 401,
                'messages' => 'Error, Something went wrong'
            ]);

        }
    }

	public function destroy(Request $request){

        $id = $request->id;
        $item = Plans::find($id);
        $item->delete();
        return response()->json(['status' => 'Plan Deleted Successfully']);
	}
}
