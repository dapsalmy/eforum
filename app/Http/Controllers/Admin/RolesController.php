<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RolesController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('created_at', 'desc')->get();
        return view('admin.roles.index', ['roles' => $roles]);

    }

    public function add()
    {
        return view('admin.roles.index');
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ],[
            'name.required' => 'Name is Required',
            'name.string' => 'Name should be a String',
            'name.max' => 'Name max characters should be 255'
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }

        $item = new Role();
        $item->name = $request->name;
        $item->categories = ($request->categories == '1' ? '1' : '0');
        $item->badges = ($request->badges == '1' ? '1' : '0');
        $item->posts = ($request->posts == '1' ? '1' : '0');
        $item->comments = ($request->comments == '1' ? '1' : '0');
        $item->replies = ($request->replies == '1' ? '1' : '0');
        $item->chats = ($request->chats == '1' ? '1' : '0');
        $item->reports = ($request->reports == '1' ? '1' : '0');
        $item->ban_durations = ($request->ban_durations == '1' ? '1' : '0');
        $item->banned_users = ($request->banned_users == '1' ? '1' : '0');
        $item->plans = ($request->plans == '1' ? '1' : '0');
        $item->buy_points = ($request->buy_points == '1' ? '1' : '0');
        $item->deposits = ($request->deposits == '1' ? '1' : '0');
        $item->subscriptions = ($request->subscriptions == '1' ? '1' : '0');
        $item->tips = ($request->tips == '1' ? '1' : '0');
        $item->withdrawals = ($request->withdrawals == '1' ? '1' : '0');
        $item->transactions = ($request->transactions == '1' ? '1' : '0');
        $item->users = ($request->users == '1' ? '1' : '0');
        $item->roles = ($request->roles == '1' ? '1' : '0');
        $item->pages = ($request->pages == '1' ? '1' : '0');
        $item->faqs = ($request->faqs == '1' ? '1' : '0');
        $item->site_settings = ($request->site_settings == '1' ? '1' : '0');
        $item->gateways = ($request->gateways == '1' ? '1' : '0');
        $item->language = ($request->language == '1' ? '1' : '0');
        $item->mail = ($request->mail == '1' ? '1' : '0');
        if ($item->save()) {

            return response()->json([
                'status' => 200,
                'messages' => 'Role added Successfully'
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
		$role = Role::find($id);
        return view('admin.roles.index', ["role" => $role]);
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ],[
            'name.required' => 'Name is Required',
            'name.string' => 'Name should be a String',
            'name.max' => 'Name max characters should be 255',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }

        $name = $request->name;

        Role::where('id', $request->id)->update([
            'name' => $request->name,
            'categories' => ($request->categories == '1' ? '1' : '0'),
            'badges' => ($request->badges == '1' ? '1' : '0'),
            'posts' => ($request->posts == '1' ? '1' : '0'),
            'comments' => ($request->comments == '1' ? '1' : '0'),
            'replies' => ($request->replies == '1' ? '1' : '0'),
            'chats' => ($request->chats == '1' ? '1' : '0'),
            'reports' => ($request->reports == '1' ? '1' : '0'),
            'ban_durations' => ($request->ban_durations == '1' ? '1' : '0'),
            'banned_users' => ($request->banned_users == '1' ? '1' : '0'),
            'plans' => ($request->plans == '1' ? '1' : '0'),
            'buy_points' => ($request->buy_points == '1' ? '1' : '0'),
            'deposits' => ($request->deposits == '1' ? '1' : '0'),
            'subscriptions' => ($request->subscriptions == '1' ? '1' : '0'),
            'tips' => ($request->tips == '1' ? '1' : '0'),
            'withdrawals' => ($request->withdrawals == '1' ? '1' : '0'),
            'transactions' => ($request->transactions == '1' ? '1' : '0'),
            'users' => ($request->users == '1' ? '1' : '0'),
            'roles' => ($request->roles == '1' ? '1' : '0'),
            'pages' => ($request->pages == '1' ? '1' : '0'),
            'faqs' => ($request->faqs == '1' ? '1' : '0'),
            'site_settings' => ($request->site_settings == '1' ? '1' : '0'),
            'gateways' => ($request->gateways == '1' ? '1' : '0'),
            'language' => ($request->language == '1' ? '1' : '0'),
            'mail' => ($request->mail == '1' ? '1' : '0')
        ]);


            return response()->json([
                'status' => 200,
                'messages' => 'Role updated Successfully'
            ]);

    }

	public function destroy(Request $request){

        $id = $request->id;
        $item = Role::find($id);
        $item->delete();
        return response()->json(['status' => 'Role Deleted Successfully']);
	}
}
