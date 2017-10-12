<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Domain;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function postIndex(Request $request) {
        if ($request->ajax()) {
            $data = array();
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');
            $search = $request->get('search');
            $data['recordsTotal'] = Domain::count();
            $query = Domain::select([
                '*'
            ]) ->skip($start)->take($length)
                ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir']);

            if (strlen($search['value']) > 0) {
                $data['recordsFiltered'] = Domain::where(function ($query) use ($search) {
                    $query->where('domain', 'LIKE', '%' . $search['value'] . '%');
                })->count();
                $data['data']= $query->where(function ($query) use ($search) {
                    $query->where('usernick', 'LIKE', '%' . $search['value'] . '%');
                })->get();
            } else {
                $data['recordsFiltered'] = $data['recordsTotal'];
                $data['data']= $query->get();
            }
            return response()->json($data);
        }
    }

    public function create()
    {
        return view('create');
    }

    public function postCreate(Request $request)
    {
        $domains = $request->get('domains');

        $domains = str_replace("\r", '', $domains);

        foreach(explode("\n", $domains) as $domain) {
            $domain = parse_url(str_start($domain, 'http://'),  PHP_URL_HOST);;

            $store = new Domain;

            $store->domain = str_after(parse_url(str_start($domain, 'http://'),  PHP_URL_HOST), 'www.');
            $store->register_url = '';
            $store->save();
        }

        return redirect('/')->withSuccess('添加成功');
    }
}
