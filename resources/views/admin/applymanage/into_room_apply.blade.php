@extends('layouts.public')

@section('bodyattr')class="gray-bg"@endsection

@section('body')

    <div class="wrapper wrapper-content">

        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="{{route('admin.exhibitmanage.instorageroom')}}">查询</a></li>
                        <li><a href="javascript:void(0)" onclick="into_room_audit('{{\App\Dao\ConstDao::EXHIBIT_INTO_ROOM_STATUS_PASS}}')">审核通过</a></li>
                        <li><a href="javascript:void(0)" onclick="into_room_audit('{{\App\Dao\ConstDao::EXHIBIT_INTO_ROOM_STATUS_REFUSE}}')">审核拒绝</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <form role="form" class="form-inline" method="get" action="{{route('admin.applymanage.export_collect_apply')}}">
                            <div class="form-group">
                                <select name="apply_type" class="form-control">
                                    @foreach(\App\Dao\ConstDao::$apply_desc as $key=>$v)
                                        @if($type == $key)
                                            <option selected value="{{$key}}">{{$v}}</option>
                                        @else
                                            <option value="{{$key}}">{{$v}}</option>
                                        @endif

                                    @endforeach
                                </select>
                            </div>
                            &nbsp;&nbsp;
                            <button type="submit" class="btn btn-primary">搜索</button>
                            <button type="button" class="btn btn-white" onclick="location.href='{{route('admin.applymanage.export_collect_apply')}}'">重置</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <table class="table table-striped table-bordered table-hover dataTables-example dataTable">
                            <thead>
                            <tr role="row">
                                <th>选择</th>
                                <th>入库馆凭证号</th>
                                <th>库房名称</th>
                                <th>展品名称</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            @foreach($exhibit_list as $exhibit)
                                <tr class="gradeA">
                                    <td><input type="checkbox" name="exhibit_into_room_id" value="{{$exhibit['exhibit_into_room_id']}}"></td>
                                    <td>{{$exhibit['in_room_recipe_num']}}</td>
                                    <td>{{$exhibit['room_name']}}</td>
                                    <td>{{$exhibit['name']}}</td>
                                    <td>{{\App\Dao\ConstDao::$intoroom_desc[$exhibit['status']]}} </td>
                                    <td>
                                        @if($exhibit['status'] == \App\Dao\ConstDao::EXHIBIT_INTO_ROOM_STATUS_DRAFT)
                                            <a href="{{route('admin.exhibitmanage.add_instorageroom')."?exhibit_into_room_id=".$exhibit['exhibit_into_room_id']}}">编辑</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                        <div class="row">
                            <div class="col-sm-12">
                                {!! $paginator->render() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    //功能函数，收集选中的申请项
    function get_collect_checked_ids() {
        checkd_list = $('input[name="exhibit_into_room_id"]:checked')
        collect_apply_ids = []
        for(i = 0; i<checkd_list.length;i++){
            collect_apply_ids.push($(checkd_list[i]).val())
        }
        return collect_apply_ids;
    }

    function into_room_audit(status) {
        apply_ids = get_collect_checked_ids();
        if(apply_ids.length==0){
            layer.alert("请至少选择一项")
            return
        }
        $.ajax('{{route("admin.applymanage.into_room_audit")}}', {
            method: 'POST',
            data: {'exhibit_into_room_id':apply_ids,'_token':"{{csrf_token()}}","audit":status},
            dataType: 'json'
        }).done(function (response) {
            layer.alert(response.msg)
            setTimeout("location.reload();", 3000)
        });
    }
</script>
