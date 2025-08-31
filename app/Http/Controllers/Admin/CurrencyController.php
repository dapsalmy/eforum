<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Admin\Currency;
use App\Utility\SettingsUtility;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::all();
        return view('admin.currencies.index', compact('currencies'));

    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:255',
            'code' => 'required|string|max:255',
        ],[
            'name.required' => 'Currency Name is Required',
            'name.string' => 'Currency Name should be a String',
            'name.max' => 'Currency Name max characters should be 255',
            'symbol.required' => 'Currency Symbol is Required',
            'symbol.string' => 'Currency Symbol should be a String',
            'symbol.max' => 'Currency Name max characters should be 255',
            'code.required' => 'Currency Code is Required',
            'code.string' => 'Currency Code should be a String',
            'code.max' => 'Currency Code max characters should be 255',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }

        $currency = new Currency;
        $currency->name = $request->name;
        $currency->symbol = $request->symbol;
        $currency->code = $request->code;
        $currency->exchange_rate = $request->exchange_rate;
        if ($currency->save()) {

            return response()->json([
                'status' => 200,
                'messages' => 'Currency added Successfully'
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
		$emp = Currency::find($id);
		return response()->json($emp);
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:255',
            'code' => 'required|string|max:255',
        ],[
            'name.required' => 'Currency Name is Required',
            'name.string' => 'Currency Name should be a String',
            'name.max' => 'Currency Name max characters should be 255',
            'symbol.required' => 'Currency Symbol is Required',
            'symbol.string' => 'Currency Symbol should be a String',
            'symbol.max' => 'Currency Name max characters should be 255',
            'code.required' => 'Currency Code is Required',
            'code.string' => 'Currency Code should be a String',
            'code.max' => 'Currency Code max characters should be 255',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }

        $currency = Currency::find($request->currency_id);
        $currency->name = $request->name;
        $currency->symbol = $request->symbol;
        $currency->code = $request->code;
        $currency->exchange_rate = $request->exchange_rate;
        if ($currency->update()) {

            return response()->json([
                'status' => 200,
                'messages' => 'Currency updated Successfully'
            ]);
        }
        else{

            return response()->json([
                'status' => 401,
                'messages' => 'Something went wrong'
            ]);

        }
    }

    public function settings(Request $request)
    {
        $inputs = $request->except(['_token']);

        if(!empty($inputs)){
            foreach ($inputs as $type => $value) {

                SettingsUtility::save_settings($type,trim($value));

            }
        }

        return redirect()->back()->with('success', 'Currency Settings Updated Successfully');
    }

	public function destroy(Request $request){

        $id = $request->id;
        $currency = Currency::find($id);
        $currency->delete();
        return response()->json(['status' => 'Currency Deleted Successfully']);
	}
}
