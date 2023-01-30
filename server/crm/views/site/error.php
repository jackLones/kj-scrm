<?php

	/* @var $this yii\web\View */
	/* @var $name string */
	/* @var $message string */

	/* @var $exception Exception */

	use yii\helpers\Html;

	$errorCode = Yii::$app->response->getStatusCode() == "200" ? $exception->getCode() : Yii::$app->response->getStatusCode();

	$this->title           = "出错啦";
	$this->context->layout = "tiny";

	$this->registerCssFile("@web/css/404.css");

	$redirectUrl = "";
	$stateId     = !empty($_GET['stateId']) ? $_GET['stateId'] : "";

	if (!empty($stateId)) {
		$stateInfo = \app\models\State::findOne(['id' => $stateId]);

		if (!empty($stateInfo)) {
			$redirectUrl = $stateInfo->redirect_url;
		}
	}
?>
<div class="site-error">
	<div class="container container-star">
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-1"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
		<div class="star-2"></div>
	</div>
	<div class="container container-bird">
		<div class="bird bird-anim">
			<div class="bird-container">
				<div class="wing wing-left">
					<div class="wing-left-top"></div>
				</div>
				<div class="wing wing-right">
					<div class="wing-right-top"></div>
				</div>
			</div>
		</div>
		<div class="bird bird-anim">
			<div class="bird-container">
				<div class="wing wing-left">
					<div class="wing-left-top"></div>
				</div>
				<div class="wing wing-right">
					<div class="wing-right-top"></div>
				</div>
			</div>
		</div>
		<div class="bird bird-anim">
			<div class="bird-container">
				<div class="wing wing-left">
					<div class="wing-left-top"></div>
				</div>
				<div class="wing wing-right">
					<div class="wing-right-top"></div>
				</div>
			</div>
		</div>
		<div class="bird bird-anim">
			<div class="bird-container">
				<div class="wing wing-left">
					<div class="wing-left-top"></div>
				</div>
				<div class="wing wing-right">
					<div class="wing-right-top"></div>
				</div>
			</div>
		</div>
		<div class="bird bird-anim">
			<div class="bird-container">
				<div class="wing wing-left">
					<div class="wing-left-top"></div>
				</div>
				<div class="wing wing-right">
					<div class="wing-right-top"></div>
				</div>
			</div>
		</div>
		<div class="bird bird-anim">
			<div class="bird-container">
				<div class="wing wing-left">
					<div class="wing-left-top"></div>
				</div>
				<div class="wing wing-right">
					<div class="wing-right-top"></div>
				</div>
			</div>
		</div>
		<div class="container-title">
			<div class="title">
				<?php if (in_array($errorCode, ['403', '404', '500'])) : ?>
					<div class="number"><?= $errorCode == "500" ? 5 : 4 ?></div>
					<div class="moon">
						<div class="face">
							<div class="mouth"></div>
							<div class="eyes">
								<div class="eye-left"></div>
								<div class="eye-right"></div>
							</div>
						</div>
					</div>
					<div class="number"><?= $errorCode == "500" ? 0 : ($errorCode == "403" ? 3 : 4) ?></div>
				<?php else: ?>
					<div class="number"><?= $errorCode ?></div>
				<?php endif; ?>
			</div>

			<?php if ($errorCode == "403") : ?>
				<div class="subtitle"><?= strpos($message, 'api unauthorized hint') !== false ? str_replace('api unauthorized hint', "api 功能未授权，请确认公众号/小程序已获得该接口，可以在公众平台官网 - 开发者中心页中查看接口权限。描述", $message) : (strpos($message, 'not same contractor hint') !== false ? str_replace('not same contractor hint', "公众号与小程序的主体不一致。描述", $message) : nl2br(Html::encode($message))) ?></div>
			<?php elseif ($errorCode == "404") : ?>
				<div class="subtitle">很抱歉！没有找到您要访问的页面！</div>
			<?php elseif ($errorCode == "500") : ?>
				<div class="subtitle"><?= nl2br(Html::encode($message)) ?></div>
			<?php else : ?>
				<div class="subtitle"><?= nl2br(Html::encode($message)) ?></div>
			<?php endif; ?>

			<?php if (!empty($redirectUrl)) : ?>
				<a href="<?= urldecode($redirectUrl) ?>" target="_self">
					<button>返回</button>
				</a>
			<?php endif; ?>
		</div>
	</div>
</div>
