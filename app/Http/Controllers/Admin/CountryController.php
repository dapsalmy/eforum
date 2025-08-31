<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Admin\Country;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CountryController extends Controller
{
    public function index()
    {
        $country = Country::all();
        return view('admin.country.index', compact('country'));

    }


    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required',
        ],[
            'name.required' => 'Country Name is Required',
            'name.string' => 'Country Name should be a String',
            'name.max' => 'Country Name max characters should be 255',
            'code.required' => 'Code is Required',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }

        $item = new Country();
        $item->name = $request->name;
        $item->code = $request->code;
        $item->status = $request->status;
        if ($item->save()) {

            return response()->json([
                'status' => 200,
                'messages' => 'Country added Successfully'
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
		$emp = Country::find($id);
		return response()->json($emp);
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required',
        ],[
            'name.required' => 'Country Name is Required',
            'name.string' => 'Country Name should be a String',
            'name.max' => 'Country Name max characters should be 255',
            'code.required' => 'Code is Required',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }

        $item = Country::find($request->country_id);
        $item->name = $request->name;
        $item->code = $request->code;
        $item->status = $request->status;
        if ($item->update()) {

            return response()->json([
                'status' => 200,
                'messages' => 'Country updated Successfully'
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
        $item = Country::find($id);
        $item->delete();
        return response()->json(['status' => 'Country Deleted Successfully']);
	}
}
