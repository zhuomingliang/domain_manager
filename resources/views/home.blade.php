@extends('layouts.app')

@section('content')
    <div class="row page-title-row" style="margin:5px;">
        <div class="col-md-6">
        </div>
        <div class="col-md-6 text-right">
                <a href="/create" class="btn btn-primary btn-md">
                    <i class="fa fa-plus-circle"></i> 添加域名列表
                </a>
        </div>
    </div>
    <div class="row page-title-row" style="margin:5px;">
        <div class="col-md-6">
        </div>
        <div class="col-md-6 text-right">
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-primary">
            <div class="panel-heading">域名列表</div>
                <div class="panel-body">
                    <table id="tags-table" class="table table-bordered table-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th data-sortable="false" class="hidden-sm"></th>
                            <th class="hidden-sm">域名</th>
                            <th class="hidden-sm">注册地址</th>
                            <th class="hidden-sm">过期时间</th>
                            <th data-sortable="false">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    <div class="modal fade" id="modal-delete" tabIndex="-1">
        <div class="modal-dialog modal-danger">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        ×
                    </button>
                    <h4 class="modal-title">提示</h4>
                </div>
                <div class="modal-body">
                    <p class="lead">
                        <i class="fa fa-question-circle fa-lg"></i>
                        确认要删除这个域名吗?
                    </p>
                </div>
                <div class="modal-footer">
                    <form class="deleteForm" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-times-circle"></i>确认
                        </button>
                    </form>
                </div>

            </div>
            @stop

            @section('js')
            	<script src="/assets/js/app/common.js" charset="UTF-8"></script>
                <script>
                    $(function () {
                        var table = $("#tags-table").DataTable({
                            language:app.DataTable.language(),
                            order: [[3, "asc"]],
                            serverSide: true,
                            // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                            // 要 ajax(url, type) 必须加这两参数
                            ajax: app.DataTable.ajax(),
                            "columns": [
                                {"data": "id"},
                                {"data": "domain"},
                                {"data": "register_url"},
                                {"data": "expire_time"},
                                {"data": "action"}

                            ],
                            columnDefs: [
                                {
                                    'targets': -1, "render": function (data, type, row) {
                                    var str = '';

                                    //编辑
                                    str += '<a style="margin:3px;" href="/edit?id=' + row['id'] + '" class="X-Small btn-xs text-success "><i class="fa fa-edit"></i> 编辑</a>';


                                    //删除
                                    str += '<a style="margin:3px;" href="#" attr="' + row['id'] + '" class="delBtn X-Small btn-xs text-danger"><i class="fa fa-times-circle"></i> 删除</a>';

                                    return str;
                                }
                                }
                            ]
                        });

                        table.on('draw.dt', function () {
                            table.column(0).nodes().each(function (cell, i) {
                                cell.innerHTML = i + 1;
                            });
                        });

                        $("table").delegate('.delBtn', 'click', function () {
                            var id = $(this).attr('attr');
                            $('.deleteForm').attr('action', '/admin/?id=' + id);
                            $("#modal-delete").modal();
                        });

                    });
                </script>
@stop