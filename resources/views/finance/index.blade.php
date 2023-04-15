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
                    <h2>账单配平查询</h2>
                    <div class="ibox-tools">
                        <form style="display: inline-block;margin-right:100px" action="/api/finance/excel/import" method="post" enctype="multipart/form-data">
                            <input style="display: inline-block" type="file" style="color:black" name='report' tooltip='导入Excel'></input>
                            <select name="type">
                                <option value="daily"
                                >日记账</option>
                                <option value="bank"
                                >银行对账单</option>
                            </select>
                            <button style="display: inline-block" class="btn btn-sm btn-primary" type="submit">
                                导入
                            </button>
                        </form>

                    </div>
                    <div style="margin-top:20px;border:1px solid"></div>
                    <div style='margin-top:20px'>
                        <button id='btnClear' onClick="clear()" class="glyphicon glyphicon-plus">清空</button>
                        <a style="color:rgb(134, 2, 2);display:inline-block;margin-left:46px" target="_black" href="/api/finance/analysis" class="glyphicon glyphicon-plus">分析</a>
                    </div>
                </div>
                <div style="margin-top:20px;border:1px solid"></div>
                <div style="margin-top:20px">
                    说明：
                    <div>1. 日记账 第一列是日期，第二列是凭证号 第三列是公司名称 第四列是借 第五列是贷</div>
                    <div>2. 银行对账单第一列是日期 第二列是借 第三列是贷 第四列是公司名称</div>
                </div>
   
                <div style="margin-top: 20px">
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