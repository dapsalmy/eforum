<?php

namespace App\Http\Controllers\Admin;

use App\Models\BuyPoint;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BuyPointsController extends Controller
{
    public function index()
    {
        $points = BuyPoint::orderBy('order', 'asc')->get();
        return view('admin.forum.buypoints', ['points' => $points]);

    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'value' => 'required',
            'price' => 'required',
            'order' => 'required',
        ],[
            'value.required' => 'Value is Required',
            'price.required' => 'Price is Required',
            'order.required' => 'Order is Required'
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }

        $item = new BuyPoint();
        $item->value = $request->value;
        $item->price = $request->price;
        $item->order = $request->order;
        if ($item->save()) {

            return response()->json([
                'status' => 200,
                'messages' => 'Points added Successfully'
            ]);

        }
        else{

            return response()->json([
                'status' => 401,
                'messages' => 'Error, Something went wrong'
            ]);

        }
    }

    public function edit(Request $request)
    {
		$id = $request->id;
		$emp = BuyPoint::find($id);
		return response()->json($emp);
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'value' => 'required',
            'price' => 'required',
            'order' => 'required',
        ],[
            'value.required' => 'Value is Required',
            'price.required' => 'Price is Required',
            'order.required' => 'Order is Required'
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }

        $item = BuyPoint::find($request->buypoint_id);
        $item->value = $request->value;
        $item->price = $request->price;
        $item->order = $request->order;
        if ($item->update()) {

            return response()->json([
                'status' => 200,
                'messages' => 'Point updated Successfully'
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
        $item = BuyPoint::find($id);
        $item->delete();
        return response()->json(['status' => 'Point Deleted Successfully']);
	}
}
