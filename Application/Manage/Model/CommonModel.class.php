<?php

/**
 * @Dec    Manage模块主模型
 * @Auther QiuXiangCheng
 * @Date   2017/12/12
 */
namespace Manage\Model;
use Common\Model\BaseModel;

class CommonModel extends BaseModel {

	public function _initialize() {

		parent::_initialize();
	}

	public function baseAuthUsingPnId($pn_id, $bs_id) {

		return $this -> table('pn_ponds') -> where(['id' => $pn_id, 'base_id' => $bs_id]) -> count();
	}

	public function baseAuthUsingPnIdForRegion($pn_id, $re_id) {

		return $this -> table('pn_ponds') -> where(['id' => $pn_id, 'region_id' => $re_id]) -> count();
	}

	public function baseAuthUsingReId($re_id, $bs_id) {

		return $this -> table('re_region') -> where(['id' => $re_id, 'base_id' => $bs_id]) ->  count();
	}

	public function baseAuthUsingMtId($mt_id, $bs_id) {

		return $this -> table('mt_monitor') -> where(['id' => $mt_id, 'bs_id' => $bs_id]) -> count();
	}

	public function baseCheckMonitorInPn($mt_id, $pn_id) {

		return $this -> table('mt_monitor') -> where(['pn_id' => $pn_id, 'id' => $mt_id]) -> count();
	}
}