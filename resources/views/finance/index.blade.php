<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <title>后台</title>
    <meta name="keywords" content="Select Health管理后台">
    <meta name="description" content="Select Health管理后台">
    <script src="https://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <link rel="shortcut icon" href="/admin/favicon.ico">
</head>
<body style="overflow:hidden">
    <div id="wrapper">
                <div class="ibox-title">
                    <h5>第三方订单导入</h5>
                    <div class="ibox-tools">
                        <form style="display: inline-block;margin-right:100px" action="/api/finance/excel/import" method="post" enctype="multipart/form-data">
                            <input style="display: inline-block" type="file" style="color:black" name='report' tooltip='导入Excel'></input>
                            <select name="type">
                                <option value="left"
                                >左侧</option>
                                <option value="right"
                                >右侧</option>
                            </select>
                            <button style="display: inline-block" class="btn btn-sm btn-primary" type="submit">
                                导入
                            </button>
                        </form>
                        <a style="color:black" href="/admins/third_orders_update" class="glyphicon glyphicon-plus"
                           tooltip='添加第三方订单'></a>
                    </div>
                </div>
                <div>
                    @foreach($keys as $value)
                    <div>{{ $value }}</div>
                    @endforeach
                </div>
                <div class="ibox-content">

                    <form action="/admins/third_orders" method="get">
                        <div class="input-group">
                            <div class="col-sm-3">
                                <input type="text" placeholder="手机号码" value="{{ $request->phone }}" name="phone"
                                       class="form-control input-lg">
                            </div>
                            <div class="col-sm-3">
                                <input type="text" placeholder="第三方订单号" value="{{ $request->third_order_id }}"
                                       name="third_order_id" class="form-control input-lg">
                            </div>

                            <div class="col-sm-3">
                                <select class="form-control input-lg" name="source">
                                    <option @if($request->source == '')
                                            selected='true'
                                            @endif
                                            value=""
                                    >全部</option>
                                    <option @if($request->source == 'jd')
                                            selected='true'
                                            @endif
                                            value="jd"
                                    >京东</option>
                                </select>
                            </div>
                            <div class="input-group-btn">
                                <button class="btn btn-lg btn-primary" type="submit">
                                    搜索
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="ibox-content">
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>订单号</th>
                            <th>来源</th>
                            <th>用户电话</th>
                            <th>权益包</th>
                            <th>核销数</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{--  @foreach($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->third_order_id }}</td>
                                <td>{{ $log->source_show }}</td>
                                <td>{{ $log->phone }}</td>
                                <td>{{ $log->Package->name }} * {{ $log->package_num }}</td>
                                @if(is_null($log->deleted_at))

                                    @if ($log->package_num == $log->is_used)
                                        <td class="text-warning">已激活</td>
                                    @else
                                        <td class='text-info'>未激活</td>
                                    @endif
                                @else
                                    <td class="text-danger">已删除</td>

                                @endif
                                <td>
                                    <a href="/admins/third_orders_update?log_id={{ $log->id }}" title="编辑"><span
                                            class="fa fa-edit"></span></a>
                                    @if(is_null($log->deleted_at))
                                        <a href="/api/third/delete?log_id={{ $log->id }}" title="删除"><span
                                                class="fa fa-stop"></span></a>
                                    @else
                                        <a href="/api/third/delete?log_id={{ $log->id }}" title="恢复"><span
                                                class="fa fa-play"></span></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach  --}}
                        </tbody>
                    </table>

                </div>
            </div>
</body>