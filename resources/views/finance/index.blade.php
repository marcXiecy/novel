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
    {{--  <link rel="shortcut icon" href="/admin/favicon.ico">  --}}
</head>
<body style="overflow:hidden">
    <div id="wrapper">
                <div class="ibox-title">
                    <h5>账单配平查询</h5>
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

                    </div>
                    <div style='margin-top:20px'>
                        <button id='btnClear' onClick="clear()" class="glyphicon glyphicon-plus">清空</button>
                        <a style="color:black" target="_black" href="/api/finance/analysis" class="glyphicon glyphicon-plus">分析</a>
                    </div>
                </div>
                <div>
                    @foreach($keys as $value)
                    <div>{{ $value }}</div>
                    @endforeach
                </div>
            </div>
</body>
<script type="text/javascript">
        $('#btnClear').on('click',function(){
            $.get("/api/finance/clear" , function (data) {
                window.location.reload()
            }, 'json');
        })
</script>