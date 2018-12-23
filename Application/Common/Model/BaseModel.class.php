<?php

	/**
	 * @Dec    所有模块的模型基类
	 * @Auther QiuXiangCheng
	 * @Date   2017/12/12
	 */
	namespace Common\Model;
	use Think\Model;

	class BaseModel extends Model {

		public function _initialize() {

			// parent::_initialize();
		}

		/**
		 * 取得菜单
		 * @DateTime 2018-12-23T22:24:26+0800
		 */
		public function getMenu($where = []) {

			return $this -> table('menu') -> where($where) -> select();
		}
	}
