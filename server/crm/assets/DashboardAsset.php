<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/1/16
	 * Time: 16:56
	 */

	namespace app\assets;

	use yii\web\AssetBundle;

	class DashboardAsset extends AssetBundle
	{
		public $basePath = '@webroot';
		public $baseUrl = '@web';
		public $css = [];
		public $js = [];
		public $depends = [];
	}