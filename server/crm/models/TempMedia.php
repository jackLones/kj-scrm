<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\UploadFileUtil;
	use Yii;

	/**
	 * This is the model class for table "{{%temp_media}}".
	 *
	 * @property int    $id
	 * @property string $md5          临时资源的MD5值
	 * @property string $media_id     媒体id
	 * @property string $local_path   本地地址
	 * @property int    $is_use       是否已经被使用：0、未使用；1、已使用
	 * @property string $use_time     使用时间
	 * @property string $create_time  创建时间
	 * @property string $s_local_path 缩略图地址
	 */
	class TempMedia extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%temp_media}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['local_path', 's_local_path'], 'string'],
				[['is_use'], 'integer'],
				[['use_time', 'create_time'], 'safe'],
				[['md5'], 'string', 'max' => 32],
				[['media_id'], 'string', 'max' => 80],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => Yii::t('app', 'ID'),
				'md5'          => Yii::t('app', '临时资源的MD5值'),
				'media_id'     => Yii::t('app', '媒体id'),
				'local_path'   => Yii::t('app', '本地地址'),
				'is_use'       => Yii::t('app', '是否已经被使用：0、未使用；1、已使用'),
				'use_time'     => Yii::t('app', '使用时间'),
				'create_time'  => Yii::t('app', '创建时间'),
				's_local_path' => Yii::t('app', '缩略图地址'),
			];
		}

		public static function UploadTempFile ($type, $md5, $is_heard)
		{
			$type    = explode("/", $type);
			$saveDir = 'temp';
			if ($type[1] == 'png' || $type[1] == 'jpeg') {
				$maxSize = -1;//大小限制
			} else if ($type[1] == "mp4" || $type[1] == "quicktime") {
				$maxSize = 1024 * 1024 * 50;//大小限制
				$saveDir = 'videos';
			} else {
				throw new InvalidDataException("图片只允许png和jpg,视频只允许mp4");
			}
			$file                 = TempMedia::findOne(["md5" => $md5]);
			$data["md5"]          = $md5;
			$data["local_path"]   = '';
			$data["s_local_path"] = '';
			if (!empty($file) && !empty($file->local_path)) {
				$data["local_path"]   = $file->local_path;
				$data["s_local_path"] = $file->s_local_path;

				return $data;
			}
			Yii::error($_FILES, '$_FILES');
			if (empty($_FILES)) {
				return $data;
			}
			$uploadFileUtil            = new UploadFileUtil();
			$uploadFileUtil->saveDir   = $saveDir;//上传文件保存路径
			$uploadFileUtil->maxSize   = $maxSize;
			$uploadFileUtil->allowExts = ['png', 'jpeg', "mp4", "jpg", 'mov'];
			$result                    = $uploadFileUtil->upload("", ($is_heard === true) ? 1 : 2);
			if (empty($result)) {
				throw new InvalidDataException($uploadFileUtil->getErrorMsg());
			}
			$uploadFileList = $uploadFileUtil->getUploadFileList();
			foreach ($uploadFileList as $uploadInfo) {
				$momentsUpload               = new TempMedia();
				$momentsUpload->md5          = $md5;
				$momentsUpload->local_path   = $uploadInfo['local_path'];
				$momentsUpload->s_local_path = !isset($uploadInfo['s_local_path']) ? NULL : $uploadInfo['s_local_path'];
				$momentsUpload->save();
				$data["local_path"]   = $uploadInfo['local_path'];
				$data["s_local_path"] = !isset($uploadInfo['s_local_path']) ? NULL : $uploadInfo['s_local_path'];
			}

			return $data;
		}

	}
