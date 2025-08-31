<?php

namespace App\Http\Controllers\Frontend;

use App\Models\User;
use App\Models\Block;
use App\Models\Plans;
use App\Models\Posts;
use App\Models\Points;
use App\Models\Replies;
use App\Models\BuyPoint;
use App\Models\Comments;
use App\Models\Admin\Faq;
use App\Models\UserViews;
use App\Models\ReportUser;
use App\Models\Admin\Badge;
use App\Models\Admin\Pages;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Notifications;
use Conner\Tagging\Model\Tag;
use Illuminate\Support\Carbon;
use App\Models\Admin\Categories;
use App\Models\Admin\EmailTemplate;
use App\Http\Controllers\Controller;
use App\Mail\GeneralMail;
use App\Models\Admin\Country;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Overtrue\LaravelFollow\Followable;
use Illuminate\Support\Facades\Validator;

class WebsiteController extends Controller
{

    public function index()
    {
        $users = User::where('role', 'user')->orderByDesc('created_at')->limit(6)->get();
        $total_users = User::count();
        $top_users = User::where('role', 'user')->withCount('posts')->orderBy('posts_count', 'desc')->limit(8)->get();
        $categories = Categories::where('status', 1)->orderBy('created_at', 'asc')->limit(6)->get();
        $tags = Tag::orderBy('count', 'desc')->limit(12)->get();
        return view('frontend.home', [
            'users' => $users,
            'total_users' => $total_users,
            'top_users' => $top_users,
            'categories' => $categories,
            'tags' => $tags
        ]);
    }

    public function about()
    {
        $page = Pages::where('status', 1)->where('slug', 'about')->first();

        return view('frontend.pages.index', ['page' => $page]);
    }

    public function rules()
    {
        $page = Pages::where('status', 1)->where('slug', 'community-rules')->first();

        return view('frontend.pages.index', ['page' => $page]);
    }

    public function privacy()
    {
        $page = Pages::where('status', 1)->where('slug', 'privacy-policy')->first();

        return view('frontend.pages.index', ['page' => $page]);
    }

    public function terms()
    {
        $page = Pages::where('status', 1)->where('slug', 'terms-and-conditions')->first();

        return view('frontend.pages.index', ['page' => $page]);
    }

    public function cookie()
    {
        $page = Pages::where('status', 1)->where('slug', 'cookie-policy')->first();

        return view('frontend.pages.index', ['page' => $page]);
    }

    public function faqs()
    {
        $faqs = Faq::orderBy('order', 'asc')->get();

        return view('frontend.pages.faq', ['faqs' => $faqs]);
    }

    public function badges()
    {
        $badges = Badge::orderBy('score', 'desc')->get();

        return view('frontend.pages.badges', ['badges' => $badges]);
    }

    public function leaderboard()
    {
        $users = User::join("points", "points.user_id", "=", "users.id")
        ->select("users.id as user_id", "users.name as user_name","users.image as user_image","users.username as user_username","users.verified as user_verified",
        "users.role as user_role")
        ->selectRaw("SUM(points.score) as sum_score") // select() doesn't work for aggregate values
        ->groupBy("users.id", "users.name", "users.image", "users.username", "users.verified", "users.role")
        ->orderBy('sum_score', 'desc')
        ->get();

        return view('frontend.pages.leaderboard', ['users' => $users]);
    }

    public function sortLeaderboard(Request $request)
    {

        $number = $request->get('number');
        $search_term = $request->get('search_term');

        $week = User::join("points", "points.user_id", "=", "users.id")
        ->select("users.id as user_id")
        ->selectRaw("SUM(points.score) as sum_score_week")
        ->whereBetween('points.created_at', [Carbon::now()->startOfWeek()->format('Y-m-d H:i:s'), Carbon::now()->endOfWeek()->format('Y-m-d H:i:s')])
        ->groupBy("users.id")
        ->orderBy('sum_score_week', 'desc')
        ->get();

        $total_users = User::join("points", "points.user_id", "=", "users.id")
        ->select("users.id as user_id", "users.name as user_name","users.image as user_image","users.username as user_username","users.verified as user_verified")
        ->selectRaw("SUM(points.score) as sum_score") // select() doesn't work for aggregate values
        ->groupBy("users.id", "users.name", "users.image", "users.username", "users.verified")
        ->orderBy('sum_score', 'desc')
        ->get();

        $users = User::query();

        $users = $users->join("points", "points.user_id", "=", "users.id")
        ->select("users.id as user_id", "users.name as user_name","users.image as user_image","users.username as user_username","users.verified as user_verified",
        "users.role as user_role")
        ->selectRaw("SUM(points.score) as sum_score") // select() doesn't work for aggregate values
        ->groupBy("users.id", "users.name", "users.image", "users.username", "users.verified", "users.role")
        ->orderBy('sum_score', 'desc');

        if ($request->ajax()) {
            $users = $this->filterLeaderboard($users, $search_term);
        }

        $users = $users->paginate($number);
        return view('frontend.partials.leaderboard', ['users' => $users, 'total_users' => $total_users, 'week' => $week]);
    }

    protected function filterLeaderboard($users, $search_term)
    {

        if ($search_term != '') {
            $users = $users->where('users.name', 'like', '%'.$search_term.'%');
        }
        return $users;
    }

    public function users(Request $request)
    {
        $users = User::orderByDesc('created_at')->paginate(12);
        $countries = Country::where('status','1')->get();

        return view('frontend.users', ['users' => $users, 'countries' => $countries]);
    }

    public function sortUsers(Request $request)
    {
        $location = $request->get('location');
        $sort = $request->get('sort');
        $number = $request->get('number');
        $search_term = $request->get('search_term');

        $users = User::query();

        if ($request->ajax()) {
            $users = $this->filterUsers($users, $location, $sort, $search_term);
        }
        $users = $users->orderByDesc('created_at')->paginate($number);
        return view('frontend.partials.users', ['users' => $users]);
    }

    protected function filterUsers($users, $location, $sort, $search_term)
    {

        if ($location == 'all') {
            $users = $users;
        }else{
            $users = $users->where('country', $location);
        }

        if ($sort == 'all') {
            $users = $users;
        }elseif ($sort == 'recent') {
            $users = $users->whereBetween('created_at', [Carbon::now()->startOfWeek()->format('Y-m-d H:i:s'), Carbon::now()->endOfWeek()->format('Y-m-d H:i:s')]);
        }elseif ($sort == 'most_posts') {
            $users = $users->withCount('posts')->orderBy('posts_count', 'desc');
        }elseif ($sort == 'most_comments') {
            $users = $users->withCount('comments')->orderBy('comments_count', 'desc');
        }

        if ($search_term != '') {
            $users = $users->where('name', 'like', '%'.$search_term.'%');
        }
        return $users;
    }

    public function user(Request $request)
    {

        $username = $request->username;
        $user = User::where('username', $username)->first();

        if(Route::is('user')){

            $item= new UserViews();
            $item->user_id = $user->id;
            $item->viewer_id = (Auth::check())?Auth::user()->id:null;
            $item->ip = request()->ip();
            $item->agent = request()->header('User-Agent');
            $item->save();

        }

        $badges = Badge::orderBy('score', 'asc')->get();
        $posts = Posts::where('user_id', $user->id)
                        ->where('status', 1)
                        ->where('public', 1)
                        ->orderByDesc('pinned')
                        ->orderBy('created_at', 'desc')
                        ->paginate(get_setting('results_per_page'));
        $comments = Comments::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(get_setting('results_per_page'));
        $replies = Replies::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(get_setting('results_per_page'));

        return view('frontend.user', ['user' => $user, 'badges' => $badges, 'posts' => $posts, 'comments' => $comments, 'replies' => $replies]);
    }

    public function paginateUserPosts(Request $request)
    {
        $username = $request->username;
        $user = User::where('username', $username)->first();
        $posts = Posts::where('user_id', $user->id)
                        ->where('status', 1)
                        ->where('public', 1)
                        ->orderByDesc('pinned')
                        ->orderBy('created_at', 'desc')
                        ->paginate(get_setting('results_per_page'));
        return view('frontend.pagination.user.posts', ['posts' => $posts]);
    }

    public function paginateUserComments(Request $request)
    {
        $username = $request->username;
        $user = User::where('username', $username)->first();
        $comments = Comments::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(get_setting('results_per_page'));
        return view('frontend.pagination.user.comments', ['comments' => $comments]);
    }

    public function paginateUserReplies(Request $request)
    {
        $username = $request->username;
        $user = User::where('username', $username)->first();
        $replies = Replies::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(get_setting('results_per_page'));
        return view('frontend.pagination.user.replies', ['replies' => $replies]);
    }

    function follow(Request $request){

        $user = User::findOrFail($request->item);

        if(Auth::user()->isFollowing($user)) {

            $id = Auth::user()->followings()->with('followable')->where('followable_type', 'App\Models\User')->first()->id;
            Notifications::where('following_id', $id)->delete();

            Auth::user()->unfollow($user);

            return response()->json([
                'bool'=>false,
                'status' => 200,
                'messages' => 'Unfollowed User successfully'
            ]);

        } else {

            Auth::user()->follow($user);

            //Notification
            Notifications::create([
                'sender_id' => Auth::user()->id,
                'recipient_id' => $user->id,
                'notification_type' => "Following User",
                'following_id' => Auth::user()->followings()->with('followable')->where('followable_type', 'App\Models\User')->first()->id,
                'seen' => 2,
            ]);

            if (get_setting('enable_emails') == 'Yes') {
                $re = User::where('id',$user->id)->first();
                if ($re->email_follower == '1') {

                    //Replace paremeter
                    $replace = array(
                        '{url}'              => env('APP_URL'),
                        '{logo}'              => get_setting('logo_dark'),
                        '{sitename}'          => get_setting('site_name'),
                        '{year}'              => date('Y'),
                        '{name}'              => Auth::user()->name,
                        '{image}'              => Auth::user()->image,
                        '{username}'              => Auth::user()->username,
                    );

                    //Send email
                    $template = EmailTemplate::where('name','follower')->first();
                    $template->body = process_string($replace, $template->body);

                    Mail::to($re->email)->send(new GeneralMail($template));
                }
            }


            return response()->json([
                'bool' => true,
                'status' => 200,
                'messages' => 'Followed User successfully'
            ]);

        }
    }

    public function feed()
    {
        $categories = Categories::all();
        //get list of follower ids
        $follows = Auth::user()->followings()->pluck('followable_id');

        //get posts of those that user follows
        $posts = Posts::whereIn('user_id',$follows)
                    ->where('status', 1)
                    ->orderByDesc('pinned')
                    ->orderBy('created_at', 'desc')
                    ->paginate(get_setting('results_per_page'));


        return view('frontend.feed', ['posts' => $posts, 'categories' => $categories]);

    }

    public function sortPostsFeed(Request $request)
    {
        $categories = Categories::all();

        $user_id = $request->get('user_id');
        $categoryId = $request->get('category_id');
        $date = $request->get('date');
        $sort = $request->get('sort');
        $search_term = $request->get('search_term');
        $tag = $request->get('tag');

        $posts = Posts::query();

        if ($request->ajax()) {
            $posts = $this->filterPostsFeed($posts, $user_id, $categoryId, $date, $sort, $search_term);
        }
        $posts = $posts->where('status', 1)
                    ->where('public', 1)
                    ->orderByDesc('pinned')
                    ->orderByDesc('created_at')
                    ->paginate(get_setting('results_per_page'));

        return view('frontend.partials.feed', ['posts' => $posts, 'categories' => $categories]);
    }

    protected function filterPostsFeed($posts, $user_id, $categoryId, $date, $sort, $search_term)
    {

        if ($user_id == 'all') {
            //get list of follower ids
            $follows = Auth::user()->followings()->pluck('followable_id');
            $posts = $posts->whereIn('user_id',$follows);
        }else{
            $posts = $posts->where('user_id', $user_id);
        }

        if ($categoryId == 'all') {
            $posts = $posts;
        }else{
            $posts = $posts->where('category_id', $categoryId);
        }

        if ($date == 'all') {
            $posts = $posts;
        }elseif ($date == 'today') {
                $posts = $posts->whereDate('created_at', Carbon::today()->format('Y-m-d H:i:s'));
        }elseif ($date == 'week') {
                $posts = $posts->whereBetween('created_at', [Carbon::now()->startOfWeek()->format('Y-m-d H:i:s'), Carbon::now()->endOfWeek()->format('Y-m-d H:i:s')]);
        }elseif ($date == 'month') {
                $posts = $posts->whereDate('created_at', '>=', Carbon::now()->subDay(30)->format('Y-m-d H:i:s'));
        }elseif ($date == 'year') {
                $posts = $posts->whereYear('created_at', date('Y'));
        }

        if ($sort == 'all') {
            $posts = $posts;
        }elseif ($sort == 'solved') {
            $posts = $posts->where('solved', 1);
        }elseif ($sort == 'pinned') {
            $posts = $posts->where('pinned', 1);
        }elseif ($sort == 'closed') {
            $posts = $posts->where('closed', 1);
        }elseif ($sort == 'most_reactions') {
            $posts = $posts->withCount('reactions')->orderBy('reactions_count', 'desc');
        }elseif ($sort == 'most_likes') {
            $posts = $posts->withCount('search_likes')->orderBy('search_likes_count', 'desc');
        }elseif ($sort == 'most_comments') {
            $posts = $posts->withCount('search_comments')->orderBy('search_comments_count', 'desc');
        }

        if ($search_term != '') {
            $posts = $posts->where('title', 'like', '%'.$search_term.'%');
        }
        return $posts;
    }

    public function categories()
    {
        $categories = Categories::where('status', 1)->orderBy('created_at','asc')->paginate(get_setting('results_per_page'));
        return view('frontend.categories', ['categories' => $categories]);
    }

    public function paginateCategories()
    {
        $categories = Categories::where('status', 1)->orderBy('created_at','asc')->paginate(get_setting('results_per_page'));
        return view('frontend.pagination.categories', ['categories' => $categories]);
    }

    public function category(Request $request)
    {
        $category = Categories::where('slug', $request->slug)->first();
        $categories = Categories::all();
        $posts = Posts::where('category_id', $category->id)->where('status', 1)->where('public', 1)->orderByDesc('pinned')->orderByDesc('created_at')->paginate(get_setting('results_per_page'));

        return view('frontend.category', ['posts' => $posts, 'categories' => $categories, 'category' => $category]);
    }

    public function sortPostsCategory(Request $request)
    {
        $categories = Categories::all();

        $categoryId = $request->get('category_id');
        $date = $request->get('date');
        $sort = $request->get('sort');
        $search_term = $request->get('search_term');

        $posts = Posts::query();

        if ($request->ajax()) {
            $posts = $this->filterPostsCategory($posts, $categoryId, $date, $sort, $search_term);
        }
        $posts = $posts->where('status', 1)->where('public', 1)->orderByDesc('pinned')->orderByDesc('created_at')->paginate(get_setting('results_per_page'));
        return view('frontend.partials.posts', ['posts' => $posts, 'categories' => $categories]);
    }

    protected function filterPostsCategory($posts, $categoryId, $date, $sort, $search_term)
    {


        $posts = $posts->where('category_id', $categoryId);

        if ($date == 'all') {
            $posts = $posts;
        }elseif ($date == 'today') {
                $posts = $posts->whereDate('created_at', Carbon::today()->format('Y-m-d H:i:s'));
        }elseif ($date == 'week') {
                $posts = $posts->whereBetween('created_at', [Carbon::now()->startOfWeek()->format('Y-m-d H:i:s'), Carbon::now()->endOfWeek()->format('Y-m-d H:i:s')]);
        }elseif ($date == 'month') {
                $posts = $posts->whereDate('created_at', '>=', Carbon::now()->subDay(30)->format('Y-m-d H:i:s'));
        }elseif ($date == 'year') {
                $posts = $posts->whereYear('created_at', date('Y'));
        }

        if ($sort == 'all') {
            $posts = $posts;
        }elseif ($sort == 'solved') {
            $posts = $posts->where('solved', 1);
        }elseif ($sort == 'pinned') {
            $posts = $posts->where('pinned', 1);
        }elseif ($sort == 'closed') {
            $posts = $posts->where('closed', 1);
        }elseif ($sort == 'most_reactions') {
            $posts = $posts->withCount('reactions')->orderBy('reactions_count', 'desc');
        }elseif ($sort == 'most_likes') {
            $posts = $posts->withCount('search_likes')->orderBy('search_likes_count', 'desc');
        }elseif ($sort == 'most_comments') {
            $posts = $posts->withCount('search_comments')->orderBy('search_comments_count', 'desc');
        }

        if ($search_term != '') {
            $posts = $posts->where('title', 'like', '%'.$search_term.'%');
        }
        return $posts;
    }

    public function tags()
    {
        $tags = Tag::paginate(get_setting('results_per_page'));
        return view('frontend.tags', ['tags' => $tags]);
    }

    public function sortTags(Request $request)
    {

        $number = $request->get('number');
        $search_term = $request->get('search_term');

        $tags = Tag::query();

        $tags = $tags->orderBy('id', 'desc');

        if ($request->ajax()) {
            $tags = $this->filterTags($tags, $search_term);
        }

        $tags = $tags->paginate($number);
        return view('frontend.partials.tags', ['tags' => $tags]);
    }

    protected function filterTags($tags, $search_term)
    {

        if ($search_term != '') {
            $tags = $tags->where('name', 'like', '%'.$search_term.'%');
        }
        return $tags;
    }

    public function tag(Request $request)
    {
        $tag = Tag::where('slug', $request->slug)->first();
        $categories = Categories::all();
        $posts = Posts::withAnyTag([$request->slug])
                        ->where('status', 1)
                        ->where('public', 1)
                        ->orderByDesc('pinned')
                        ->orderByDesc('created_at')
                        ->paginate(get_setting('results_per_page'));

        return view('frontend.tag', ['posts' => $posts, 'categories' => $categories, 'tag' => $tag]);
    }

    public function sortPostsTag(Request $request)
    {
        $categories = Categories::all();

        $categoryId = $request->get('category_id');
        $date = $request->get('date');
        $sort = $request->get('sort');
        $search_term = $request->get('search_term');
        $tag = $request->get('tag');

        $posts = Posts::query();

        if ($request->ajax()) {
            $posts = $this->filterPostsTag($posts, $categoryId, $date, $sort, $search_term);
        }
        $posts = $posts->withAnyTag([$tag])
                    ->where('status', 1)
                    ->where('public', 1)
                    ->orderByDesc('pinned')
                    ->orderByDesc('created_at')
                    ->paginate(get_setting('results_per_page'));

        return view('frontend.partials.posts', ['posts' => $posts, 'categories' => $categories]);
    }

    protected function filterPostsTag($posts, $categoryId, $date, $sort, $search_term)
    {

        if ($categoryId == 'all') {
            $posts = $posts;
        }else{
            $posts = $posts->where('category_id', $categoryId);
        }

        if ($date == 'all') {
            $posts = $posts;
        }elseif ($date == 'today') {
                $posts = $posts->whereDate('created_at', Carbon::today()->format('Y-m-d H:i:s'));
        }elseif ($date == 'week') {
                $posts = $posts->whereBetween('created_at', [Carbon::now()->startOfWeek()->format('Y-m-d H:i:s'), Carbon::now()->endOfWeek()->format('Y-m-d H:i:s')]);
        }elseif ($date == 'month') {
                $posts = $posts->whereDate('created_at', '>=', Carbon::now()->subDay(30)->format('Y-m-d H:i:s'));
        }elseif ($date == 'year') {
                $posts = $posts->whereYear('created_at', date('Y'));
        }

        if ($sort == 'all') {
            $posts = $posts;
        }elseif ($sort == 'solved') {
            $posts = $posts->where('solved', 1);
        }elseif ($sort == 'pinned') {
            $posts = $posts->where('pinned', 1);
        }elseif ($sort == 'closed') {
            $posts = $posts->where('closed', 1);
        }elseif ($sort == 'most_reactions') {
            $posts = $posts->withCount('reactions')->orderBy('reactions_count', 'desc');
        }elseif ($sort == 'most_likes') {
            $posts = $posts->withCount('search_likes')->orderBy('search_likes_count', 'desc');
        }elseif ($sort == 'most_comments') {
            $posts = $posts->withCount('search_comments')->orderBy('search_comments_count', 'desc');
        }

        if ($search_term != '') {
            $posts = $posts->where('title', 'like', '%'.$search_term.'%');
        }
        return $posts;
    }

    public function search(Request $request)
    {

        $search_term = $request->get('search_term');
        if ($search_term != '') {
            $users = User::where('name', 'like', '%'.$search_term.'%')->withCount('search_views')->orderBy('search_views_count', 'desc')->limit(5)->get();
            $tags = Tag::where('name', 'like', '%'.$search_term.'%')->orderByDesc('count')->limit(10)->get();
            $posts = Posts::where('title', 'like', '%'.$search_term.'%')->where('status', 1)->withCount('search_views')->orderBy('search_views_count', 'desc')->limit(6)->get();
        }

        return view('layouts.frontend-partials.search', ['users' => $users, 'tags' => $tags, 'posts' => $posts]);
    }

    public function stats()
    {
        $users = User::all()->count();
        $posts = Posts::all()->count();
        $comments = Comments::all()->count();
        $replies = Replies::all()->count();
        $categories = Categories::all()->count();
        $tags = Tag::all()->count();
        $online = User::where('last_seen', '>=', Carbon::now()->subMinutes(10))->orderByDesc('last_seen')->get();
        return view('frontend.stats',[
            'posts' => $posts,
            'users' => $users,
            'comments' => $comments,
            'replies' => $replies,
            'categories' => $categories,
            'tags' => $tags,
            'online' => $online
        ]);
    }

    public function plans()
    {
        $plans = Plans::where('status', 1)->orderBy('order', 'asc')->get();
        return view('frontend.plans', ['plans' => $plans]);
    }

    public function points()
    {
        $points = BuyPoint::orderBy('order', 'asc')->get();
        return view('frontend.points', ['points' => $points]);
    }

    public function buy_points(Request $request)
    {

        $id = $request->point_id;
        $point = BuyPoint::find($id);

        if ($point->price > Auth::user()->wallet)
        {
            return response()->json([
                'status' => 401,
                'messages' => 'You have less money in your Wallet to buy this Points'
            ]);
        }

        $amount = Auth::user()->wallet - $point->price;

        //User wallet
        User::where('id',Auth::user()->id)->update([
            'wallet' => $amount
        ]);

        $item = new Points();
        $item->user_id = Auth::user()->id;
        $item->type = '10';
        $item->score = $point->value;
        if ($item->save()) {

            Transaction::create([
                'user_id' => Auth::user()->id,
                'type_id' => $point->id,
                'type' => '2', //Buy Points
                'amount' => $point->price,
                'status' => '1'
            ]);

            return response()->json([
                'status' => 200,
                'messages' => 'You bought '. $point->value .' Points Successfully'
            ]);

        }else{

            return response()->json([
                'status' => 401,
                'messages' => 'Error, Something went wrong'
            ]);

        }
    }

    public function reportuser(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'reason' => 'required',
        ],[
            'reason.required' => 'Reason is Required',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }

        $item = new ReportUser();
        $item->sender_id = Auth::user()->id;
        $item->user_id = $request->user_id;
        $item->category = $request->category;
        $item->reason = $request->reason;
        if ($item->save()) {

            return response()->json([
                'status' => 200,
                'messages' => 'You have reported the User Successfully'
            ]);

        }else{

            return response()->json([
                'status' => 401,
                'messages' => 'Error, Something went wrong'
            ]);

        }
    }

    public function block(Request $request)
    {
        $id = $request->id;
        $item = new Block();
        $item->user_id = Auth::user()->id;
        $item->block_id = $id;
        $item->save();

        return response()->json(['status' => 'User Blocked Successfully']);
    }

    public function user_blocks()
    {
        $blocks = Block::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
        return view('user.blocks.index', ['blocks' => $blocks]);
    }

    public function unblock(Request $request)
    {
        $id = $request->id;
        $item = Block::find($id);
        $item->delete();

        return response()->json(['status' => 'User Unblocked Successfully']);
    }

    public function profile_viewers()
    {
        $users = UserViews::where('user_id', Auth::user()->id)->whereNotNull('viewer_id')->orderBy('created_at', 'desc')->get();
        return view('user.viewers.index', ['users' => $users]);
    }

    public function user_points()
    {
        $points = Points::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
        return view('user.points.index', ['points' => $points]);
    }

}
