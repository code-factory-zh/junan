<?php

	/**
	 * @Dec    所有模块的模型基类
	 * @Auther QiuXiangCheng
	 * @Date   2017/12/12
	 */
	namespace Common\Model;
	use Think\Model;

	class BaseModel extends Model {

		public function _initialize() {}

		/**
		 * 获取一条记录
		 * @param $where 查询条件
		 * @return array 返回该查询条件下的一条记录
		 */
		public function getOne($where)
		{
			return $this->where($where)->find();
		}

		/**
		 * 根据条件删除
		 * @param $where array 删除条件
		 * @return mixed
		 */
		public function del($where){
			return $this->where($where)->delete();
		}

		/**
		 * 根据条件获取列表
		 * @param $where array 删除条件
		 * @return mixed
		 */
		public function getList($where = NULL, $order = NULL, $page = NULL , $page_size = NULL){

		}
	}
