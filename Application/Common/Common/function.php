<?php

	function du($data, $exit = true){

		echo '<pre>';
		var_dump($data);
		if($exit){
			exit;
		}
	}

	function pr($data, $exit = true){

		echo '<pre>';
		print_r($data);
		if($exit){
			exit;
		}
	}

	// 取得配置数据的值
	// conf('conf,region,name')
	function conf($data, $path = ''){

		if(empty($data)){
			return false;
		}
		$data = explode(',', $data);
		$data = array_map('trim', $data);
		$path = !empty($path) ? $path . $data[0] . '.php' : $_SERVER['DOCUMENT_ROOT'] . '/Application/' . MODULE_NAME . '/Conf/' . $data[0] . '.php';
		if(!is_file($path)){
			return false;
		}
		$tmp = returnIncluded($path);
		array_shift($data);
		foreach($data as $v){
			if(!isset($tmp[$v])){
				return false;
			}
			$tmp = $tmp[$v];
		}
		unset($data, $path);
		return $tmp;
	}

	/**
	 * 返回include之后的数据
	 * @param $file 地址
	 * @return (string/array/object)
	 */
	function returnIncluded($file){

		return include $file;
	}