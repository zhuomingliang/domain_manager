@extends('layouts.app')

@section('content')
    <div class="main animsition">
        <div class="container-fluid">

            <div class="row">
                <div class="">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">添加域名列表</h3>
                        </div>
                        <div class="panel-body">
                            <form class="form-horizontal" role="form" method="POST" action="/create">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                               <div class="form-group">
                                    <label for="tag" class="col-md-3 control-label">域名列表</label>
                                    <div class="col-md-5">
                                     <textarea class="form-control" name="domains" rows="10" cols="50">
									 </textarea>
									 <span class="text-danger">每一行一个域名！</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-7 col-md-offset-3">
                                        <button type="submit" class="btn btn-primary btn-md">
                                            <i class="fa fa-plus-circle"></i>
                                            添加
                                        </button>
                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop