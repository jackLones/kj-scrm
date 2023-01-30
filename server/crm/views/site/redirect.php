<?php

	/* @var $this yii\web\View */

	$this->title = isset($title) ? $title : '加载中';

	if (!empty($redirectUrl)) {
		$this->params['redirect']     = true;
		$this->params['redirect_url'] = $redirectUrl;
	} else {
		$this->params['redirect']     = true;
		$this->params['redirect_url'] = '#';
	}
?>
