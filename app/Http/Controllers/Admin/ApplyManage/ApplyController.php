<?php

namespace App\Http\Controllers\Admin\ApplyManage;

use App\Dao\ConstDao;
use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\CollectApply;
use App\Models\Exhibit;
use App\Models\ExhibitUse;
use App\Models\ExhibitUsedApply;
use App\Models\ExhibitUseItem;
use App\Models\IdentifyApply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApplyController extends BaseAdminController
{
    public function export_collect_apply(){
        $type = \request('apply_type', ConstDao::APPLY_TYPE_COLLECT);
        $res['type'] = $type;
        if($type == ConstDao::APPLY_TYPE_COLLECT){
            //征集申请
            $res['exhibit_list'] = CollectApply::whereIn('status', array_keys(ConstDao::$collect_apply_desc))->get();
            return view('admin.applymanage.collect_apply', $res);
        }elseif($type == ConstDao::APPLY_TYPE_IDENTIFY){
            //鉴定申请
            $exhibit_list = IdentifyApply::whereIn('status', array_keys(ConstDao::$identify_desc))->get();
            //添加展品信息
            foreach($exhibit_list as $key=>$item){
                $exhibit_sum_register_id = $item->exhibit_sum_register_id;
                $exhibit_sum_register_ids = explode(',',$exhibit_sum_register_id);

                $new_names = '';
                if(!empty($exhibit_sum_register_ids)){
                    $list = Exhibit::whereIn('exhibit_sum_register_id',$exhibit_sum_register_ids)->select('name')->get();

                    foreach($list as $item1){
                        $name = $item1->name;
                        $new_names = $new_names.$name.",";
                    }
                }
                $exhibit_list[$key]['exhibit_names'] = $new_names;
            }
            $res['exhibit_list'] = $exhibit_list;
            return view('admin.applymanage.identify_apply', $res);
        }elseif($type == ConstDao::APPLY_TYPE_OUTER){
            //出库申请
            $exhibit_list = ExhibitUsedApply::where('status', '!=',ConstDao::EXHIBIT_USED_APPLY_STATUS_DRAFT)->get();
            //添加展品信息
            foreach($exhibit_list as $key=>$item){
                $exhibit_sum_register_id = $item->exhibit_list;
                $exhibit_sum_register_ids = explode(',',$exhibit_sum_register_id);

                $new_names = '';
                if(!empty($exhibit_sum_register_ids)){
                    $list = Exhibit::whereIn('exhibit_sum_register_id',$exhibit_sum_register_ids)->select('name')->get();

                    foreach($list as $item1){
                        $name = $item1->name;
                        $new_names = $new_names.$name.",";
                    }
                }
                $exhibit_list[$key]['exhibit_names'] = $new_names;
            }
            $res['exhibit_list'] = $exhibit_list;
            return view('admin.applymanage.exhibit_used_apply', $res);
        }
    }

    /**
     * 批量审核通过
     */
    public function collect_apply_pass(){
        $collect_apply_ids = \request('collect_apply_ids');
        //检测是否存在已经审核过的数据
        $count = CollectApply::where('status', '!=',ConstDao::EXHIBIT_COLLECT_APPLY_WAITING_AUDIT)->whereIn('collect_apply_id', $collect_apply_ids)->count();
        if($count>0){
            return response_json(0, array(),'抱歉，所选项存在已审核过的数据');
        }else{
            CollectApply::where('status',ConstDao::EXHIBIT_COLLECT_APPLY_WAITING_AUDIT)->whereIn('collect_apply_id', $collect_apply_ids)->update(array('status'=>
            ConstDao::EXHIBIT_COLLECT_APPLY_AUDITED));
            return response_json(1, array(),'操作完成');
        }
    }

    /**
     * 征集申请拒绝
     * @return \Illuminate\Http\JsonResponse
     */
    public function collect_apply_refuse(){
        $collect_apply_ids = \request('collect_apply_ids');
        //检测是否存在已经审核过的数据
        $count = CollectApply::where('status', '!=',ConstDao::EXHIBIT_COLLECT_APPLY_WAITING_AUDIT)->whereIn('collect_apply_id', $collect_apply_ids)->count();
        if($count>0){
            return response_json(0, array(),'抱歉，所选项存在已审核过的数据');
        }else{
            CollectApply::where('status',ConstDao::EXHIBIT_COLLECT_APPLY_WAITING_AUDIT)->whereIn('collect_apply_id', $collect_apply_ids)->update(array('status'=>
                ConstDao::EXHIBIT_COLLECT_APPLY_REFUSED));
            return response_json(1, array(),'操作完成');
        }
    }

    /**
     * 鉴定申请 批量通过
     */
    public function identify_apply_pass(){
        $identify_apply_ids = \request('identify_apply_ids');
         //检测是否存在已经审核过的数据
        $count = IdentifyApply::where('status', '!=',ConstDao::EXHIBIT_IDENTIFY_APPLY_WAITING_AUDIT)->whereIn('identify_apply_id', $identify_apply_ids)->count();
        if($count>0){
            return response_json(0, array(),'抱歉，所选项存在已审核过的数据');
        }else{
            IdentifyApply::where('status',ConstDao::EXHIBIT_COLLECT_APPLY_WAITING_AUDIT)->whereIn('identify_apply_id', $identify_apply_ids)->update(array('status'=>
                ConstDao::EXHIBIT_IDENTIFY_APPLY_AUDITED));
            return response_json(1, array(),'操作完成');
        }
    }

    /**
     * 鉴定申请拒绝
     * @return \Illuminate\Http\JsonResponse
     */
    public function identify_apply_refuse(){
        $identify_apply_ids = \request('identify_apply_ids');
        //检测是否存在已经审核过的数据
        $count = IdentifyApply::where('status', '!=',ConstDao::EXHIBIT_IDENTIFY_APPLY_WAITING_AUDIT)->whereIn('identify_apply_id', $identify_apply_ids)->count();
        if($count>0){
            return response_json(0, array(),'抱歉，所选项存在已审核过的数据');
        }else{
            IdentifyApply::where('status',ConstDao::EXHIBIT_COLLECT_APPLY_WAITING_AUDIT)->whereIn('identify_apply_id', $identify_apply_ids)->update(array('status'=>
                ConstDao::EXHIBIT_IDENTIFY_APPLY_REFUSED));
            return response_json(1, array(),'操作完成');
        }
    }

    /**
     * 展品出库申请批量通过
     */
    public function exhibit_outer_pass(){
        $exhibit_use_apply_ids = \request('exhibit_use_apply_ids');
        $count = ExhibitUsedApply::where('status', '!=', ConstDao::EXHIBIT_USED_APPLY_STATUS_WAITING_AUDIT)
            ->whereIn('exhibit_used_apply_id', $exhibit_use_apply_ids)->count();
        if($count>0){
            return response_json('0', '', '选择项包含已审核的项');
        }
        foreach($exhibit_use_apply_ids as $exhibit_use_apply_id){
            //进行申请
            $exhibit_apply_model = ExhibitUsedApply::where('exhibit_used_apply_id', $exhibit_use_apply_id)->first();
            $exhibit_apply_model->status = ConstDao::EXHIBIT_USED_APPLY_STATUS_FINISHED;
            $exhibit_apply_model->save();
            //增加审核单据
            $exhibit_use = new ExhibitUse();
            $exhibit_use->exhibit_use_apply_id = $exhibit_use_apply_id;
            $exhibit_use->depart_name = $exhibit_apply_model->apply_depart_name;
            $exhibit_use->outer_destination = $exhibit_apply_model->outer_destination;
            $exhibit_use->outer_time = $exhibit_apply_model->outer_time;
            $exhibit_use->save();
            //增加审核条目
            $exhibit_ids = $exhibit_apply_model->exhibit_list;
            $exhibit_ids = explode(',', $exhibit_ids);
            foreach($exhibit_ids as $exhibit_id){
                $item = new ExhibitUseItem();
                $item->exhibit_sum_register_id = $exhibit_id;
                $item->exhibit_use_id = $exhibit_use->exhibit_use_id;
                $item->save();
            }
        }
        //对审核通过自动增加审核单据
        return response_json('1', '', '审核通过');
    }

    /**
     * 展品出库申请批量拒绝
     */
    public function exhibit_outer_refuse(){
        $exhibit_use_apply_ids = \request('exhibit_use_apply_ids');
        $count = ExhibitUsedApply::where('status', '!=', ConstDao::EXHIBIT_USED_APPLY_STATUS_WAITING_AUDIT)
            ->whereIn('exhibit_used_apply_id', $exhibit_use_apply_ids)->count();
        if($count>0){
            return response_json('0', '', '选择项包含已审核的项');
        }
        ExhibitUsedApply::whereIn('exhibit_used_apply_id', $exhibit_use_apply_ids)
            ->update(array('status'=>ConstDao::EXHIBIT_USED_APPLY_STATUS_REFUSED));;
        return response_json('1', '', '审核拒绝');
    }
}
