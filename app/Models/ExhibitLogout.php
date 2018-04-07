<?php

namespace App\Models;
use App\Models\BaseMdl;

class ExhibitLogout extends BaseMdl
{
	protected $primaryKey = 'logout_id';
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'_token','file'
	];
	//申请状态 ：0 未提交申请，1 等待审批 2 审批通过 3 审批拒绝
	protected $apply_status=['未提交申请','等待审批','审批通过','审批拒绝'];
	//判断申请状态
	public function applyStatus($key)
	{
		return $key>3?'未知状态':$this->apply_status[$key];
	}
	//联查藏品表 collect_exhibit的collect_exhibit_id
	public function joinLeft()
	{
		return ExhibitLogout::leftjoin('collect_exhibit','collect_exhibit.collect_exhibit_id','exhibit_logout.exhibit_id');
	}
	//列出所有藏品名字
	public function collectName()
	{
		return CollectExhibit::select('name','collect_exhibit_id as id')->groupBy('name','id')->get()->toArray();
	}
}