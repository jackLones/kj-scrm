<?php

	/**
	 * 文件上传
	 * User: xcy
	 * Date: 19-10-12
	 * Time: 17:00
	 */

	namespace app\util;

	use yii\web\UploadedFile;

	/**
	 * Class UploadFileUtil
	 * @package app\util
	 * @property int    $maxSize
	 * @property int    $minSize
	 * @property array  $allowExts
	 * @property array  $allowTypes
	 * @property string $saveDir
	 * @property string $saveRule
	 */
	class UploadFileUtil
	{
		private $config
			= [
				'maxSize'    => -1,    // 上传文件的最大值
				'minSize'    => 6,    // 上传文件的最小值
				'allowExts'  => ['jpeg', 'jpg', 'png', 'mp3', 'gif', 'pem'], // 允许上传的文件后缀 留空不作后缀检查
				'allowTypes' => [],    // 允许上传的文件类型 留空不做检查
				'saveDir'    => 'images',// 上传文件保存路径
				'saveRule'   => 'uniqid',// 上传文件命名规则
			];
		// 错误信息
		private $error = '';
		// 上传成功的文件信息
		private $uploadFileList;

		public $saveDir;

		public function __get ($name)
		{
			if (isset($this->config[$name])) {
				return $this->config[$name];
			}

			return NULL;
		}

		public function __set ($name, $value)
		{
			if (isset($this->config[$name])) {
				$this->config[$name] = $value;
			}
		}

		public function __isset ($name)
		{
			return isset($this->config[$name]);
		}

		/**
		 * 架构函数
		 * @access public
		 *
		 * @param array $config 上传参数
		 */
		public function __construct ($config = [])
		{
			if (is_array($config)) {
				$this->config = array_merge($this->config, $config);
			}
		}

		public function upload ($savePath = '', $is_heard = 0)
		{
			//如果不指定保存文件名，则由系统默认
			if (empty($savePath)) {
				$savePath = \Yii::getAlias('@upload') . '/' . $this->saveDir . '/' . date('Ymd') . '/';
			}

			// 检查上传目录
			if (!is_dir($savePath)) {
				// 检查目录是否编码后的
				if (is_dir(base64_decode($savePath))) {
					$savePath = base64_decode($savePath);
				} else {
					// 尝试创建目录
					if (!mkdir($savePath, 0777, true)) {
						$this->error = '上传目录' . $savePath . '不存在';

						return false;
					}
				}
			} else {
				if (!is_writeable($savePath)) {
					$this->error = '上传目录' . $savePath . '不可写';

					return false;
				}
			}

			$uploadList = [];
			$isUpload   = false;
			$appPath    = \Yii::getAlias('@app');
			foreach ($_FILES as $key => $file) {
				//过滤无效的上传
				if (!empty($file['name'])) {
					//获取上传的文件信息
					$fileInfo          = UploadedFile::getInstanceByName($key);
					$file['extension'] = $fileInfo->extension ? $fileInfo->extension : 'jpg';
					$rule              = $this->saveRule;
					if (!empty($rule)) {
						$filename = time() . $rule() . '.' . $file['extension'];
					} else {
						$filename = $file['name'];
					}
					$file['filename'] = $filename;
					$file['size']     = (string) $file['size'];

					$saveDir = explode('/', $this->saveDir);
					if ($saveDir[0] == 'images') {
						$imgData      = getimagesize($file['tmp_name']);
						$file['type'] = $imgData['mime'];
					}

					//检查
					if (!$this->check($file)) {
						return false;
					}

					//保存上传文件
					if (!$fileInfo->saveAs($savePath . $filename)) {
						return false;
					}

					$local_path         = str_replace($appPath, '', $savePath);
					$file['local_path'] = $local_path . $filename;
					$file['realPath']   = $savePath . $filename;
					unset($file['tmp_name'], $file['error']);

					if ($saveDir[0] == 'images') {
						$imgData        = getimagesize($savePath . $filename);
						$file['width']  = $w = $imgData[0];
						$file['height'] = $h = $imgData[1];

						//生成缩略图
						if (in_array($file['type'], ['image/jpeg', 'image/png']) && !($w < 250 && $h < 250)) {
							if ($w > $h) {
								$width  = 250;
								$height = $width / $w * $h;
							} else if ($w < $h) {
								$height = 250;
								$width  = $height / $h * $w;
							} else {
								$width  = 250;
								$height = 250;
							}
							//创建空白新图片
							$newImage = imagecreatetruecolor($width, $height);

							// 启用混色模式
							imagealphablending($newImage, false);

							// 保存PNG alpha通道信息
							imagesavealpha($newImage, true);
							if ($imgData['mime'] == 'image/jpeg') {
								$image = imagecreatefromjpeg($file['realPath']);
							} elseif ($imgData['mime'] == 'image/png') {
								$image = imagecreatefrompng($file['realPath']);
							} else {
								$image = imagecreatefromstring(file_get_contents($file['realPath']));
							}
							//copy源图片内容 copy新图片
							imagecopyresized($newImage, $image, 0, 0, 0, 0, $width, $height, $w, $h);
							$s_filename = $savePath . 's_' . $filename;
							if ($imgData['mime'] == 'image/jpeg') {
								imagejpeg($newImage, $s_filename);
							} else {
								imagepng($newImage, $s_filename);
							}
							$file['s_local_path'] = $local_path . 's_' . $filename;

							imagedestroy($newImage);
						}
					} elseif ($saveDir[0] == 'voices') {
						// 获取音频的时长
						$runData = [];
						exec("ffmpeg -i " . $savePath . $filename . " 2>&1 | grep \"Duration:\" | awk -F ',' '{print $1}' | awk -F ' ' '{print $2}'", $runData);
						$file['media_duration'] = '';
						if (!empty($runData)) {
							$duration               = $runData[0];
							$file['media_duration'] = $duration;
						}
					} elseif ($saveDir[0] == 'videos') {
						if ($file['extension'] != 'mp4') {
							$audioFileName = time() . $rule() . '.mp4';
							\Yii::error($savePath . $filename, 'sym-save-path1');
							\Yii::error($savePath . $audioFileName, 'sym-save-path1');
							shell_exec('ffmpeg -i ' . $savePath . $filename . ' -vcodec copy -acodec copy ' . $savePath . $audioFileName);
							unlink($savePath . $filename);
							$filename           = $audioFileName;
							$file['local_path'] = $local_path . $audioFileName;
						}
						// 获取视频文件的宽高
						$runData = [];
						exec("ffmpeg -i " . $savePath . $filename . " 2>&1 | grep ': Video:' | awk -F ':' '{print $4}'", $runData);
						$file['width']  = '';
						$file['height'] = '';
						if (!empty($runData)) {
							$videoStr = $runData[0];
							if (!empty($videoStr)) {
								$videoArr = explode(',', $videoStr);
								if (count($videoArr) == 8) {
									if (isset($videoArr[2])) {
										$whString = $videoArr[2];
									}
								} elseif (count($videoArr) == 9) {
									if (isset($videoArr[3])) {
										$whString = $videoArr[3];
									}
								}
							}
							if (!empty($whString)) {
								$sizeArr = explode('x', trim($whString));
								if (isset($sizeArr[0]) && is_numeric($sizeArr[0])) {
									$file['width'] = $sizeArr[0];
								}
								if (isset($sizeArr[1]) && is_numeric($sizeArr[1])) {
									$file['height'] = $sizeArr[1];
								}
							}
						}

						// 获取视频的时长
						$runData = [];
						exec("ffmpeg -i " . $savePath . $filename . " 2>&1 | grep \"Duration:\" | awk -F ',' '{print $1}' | awk -F ' ' '{print $2}'", $runData);
						$file['media_duration'] = '';
						if (!empty($runData)) {
							$duration               = $runData[0];
							$file['media_duration'] = $duration;
						}
					} else if ($saveDir[0] == 'temp') {
						$imgData        = getimagesize($savePath . $filename);
						$file['width']  = $w = $imgData[0];
						$file['height'] = $h = $imgData[1];
						$is_save        = false;
						$width          = $w;
						$height         = $h;
						//朋友圈图片大于1M等比例压缩2倍
						if ($is_heard > 1 && ($file['size'] > 1024 * 1024 ||  ( $w > 1920  || $w > 1080) )) {
							if ($w > $h) {
								$per = $h / $w;//计算比例
							} else {
								$per = $w / $h;//计算比例
							}
							$width   = ($w * $per) / 2;
							$height  = ($h * $per) / 2;
							$is_save = true;
						}
						//创建空白新图片
						$newImage = imagecreatetruecolor($width, $height);

						// 启用混色模式
						imagealphablending($newImage, false);

						// 保存PNG alpha通道信息
						imagesavealpha($newImage, true);
						if ($imgData['mime'] == 'image/jpeg') {
							$image = imagecreatefromjpeg($file['realPath']);
						} elseif ($imgData['mime'] == 'image/png') {
							$image = imagecreatefrompng($file['realPath']);
						} else {
							$image = imagecreatefromstring(file_get_contents($file['realPath']));
						}
						//$is_heard 朋友圈头像是否裁剪
//						if ($is_heard == 1) {
//							$newImage = imagecreatetruecolor(250, 250);
//							imagealphablending($newImage, false);
//							imagesavealpha($newImage, true);
//							imagecopyresized($newImage, $image, 0, 0, 0, 0, 250, 250, 250, 250);
//							$is_save = true;
//						} else {
						if ($is_save) {
							imagecopyresized($newImage, $image, 0, 0, 0, 0, $width, $height, $w, $h);
						}
//						}
						$s_filename = $savePath . 's_' . $filename;
						//不满足压缩保存原图做缩略图
						if ($is_save) {
							$saveImage = $newImage;
						} else {
							$saveImage = $image;
						}
						if ($imgData['mime'] == 'image/jpeg') {
							imagejpeg($saveImage, $s_filename);
						} else {
							imagepng($saveImage, $s_filename);
						}
						$file['s_local_path'] = $local_path . 's_' . $filename;

						imagedestroy($newImage);
					} else if ($saveDir[0] == 'activity') {
						if ($file['size'] > 500 * 1024 ) {
							$percent = 0.5;
							$source  = \Yii::$app->basePath . $local_path . $filename;//原图文件名
							$dst_img = \Yii::$app->basePath . $local_path . 's_' . $filename;//保存图片的文件名
							(new Imgcompress($source, $percent))->compressImg($dst_img);
							$file['local_path'] = $file['s_local_path'] = $local_path . 's_' . $filename;
						}

//						$imgData        = getimagesize($savePath . $filename);
//						$file['width']  = $w = $imgData[0];
//						$file['height'] = $h = $imgData[1];
//						//任务宝图片大于1M等比例压缩0.89倍
//						if ($file['size'] > 500 * 1024 ) {
//							if ($w > $h) {
//								$per = $h / $w;//计算比例
//							} else {
//								$per = $w / $h;//计算比例
//							}
//							$width  = $w * $per * 0.7;
//							$height = $h * $per * 0.7;
//							//创建空白新图片
//							$newImage = imagecreatetruecolor($width, $height);
//
//							// 启用混色模式
//							imagealphablending($newImage, false);
//							// 保存PNG alpha通道信息
//							imagesavealpha($newImage, true);
//							if ($imgData['mime'] == 'image/jpeg') {
//								$image = imagecreatefromjpeg($file['realPath']);
//							} elseif ($imgData['mime'] == 'image/png') {
//								$image = imagecreatefrompng($file['realPath']);
//							} else {
//								$image = imagecreatefromstring(file_get_contents($file['realPath']));
//							}
//							imagecopyresized($newImage, $image, 0, 0, 0, 0, $width, $height, $w, $h);
//							$s_filename = $savePath . 's_' . $filename;
//							//活动海报压缩
//							$saveImage = $newImage;
//							if ($imgData['mime'] == 'image/jpeg') {
//								imagejpeg($saveImage, $s_filename);
//							} else {
//								imagepng($saveImage, $s_filename);
//							}
//							$file['local_path'] = $local_path . 's_' . $filename;
//
//							imagedestroy($newImage);
//						}
					}

					$uploadList[] = $file;
					$isUpload     = true;
				}
			}
			if ($isUpload) {
				$this->uploadFileList = $uploadList;

				return true;
			} else {
				$this->error = '没有选择上传文件';

				return false;
			}
		}

		/**
		 * 检查上传的文件
		 * @access private
		 *
		 * @param array $file 文件信息
		 *
		 * @return boolean
		 */
		private function check ($file)
		{
			if ($file['error'] !== 0) {
				//文件上传失败
				//捕获错误代码
				$this->error($file['error']);

				return false;
			}

			//检查文件大小
			if (!$this->checkSize($file['size'])) {
				$this->error = '上传文件大小不符！';

				return false;
			}

			//检查文件Mime类型
			if (!$this->checkType($file['type'])) {
				$this->error = '上传文件MIME类型不允许！';

				return false;
			}

			//检查文件类型
			if (!$this->checkExt($file['extension'])) {
				$this->error = '上传文件类型不允许';

				return false;
			}

			//检查是否合法上传
			if (!$this->checkUpload($file['tmp_name'])) {
				$this->error = '非法上传文件！';

				return false;
			}

			return true;

		}

		/**
		 * 检查文件大小是否合法
		 * @access private
		 *
		 * @param integer $size 数据
		 *
		 * @return boolean
		 */
		private function checkSize ($size)
		{
			return !($size > $this->maxSize || $size < $this->minSize) || (-1 == $this->maxSize);
		}

		/**
		 * 检查上传的文件类型是否合法
		 * @access private
		 *
		 * @param string $type 数据
		 *
		 * @return boolean
		 */
		private function checkType ($type)
		{
			if (!empty($this->allowTypes)) {
				return in_array(strtolower($type), $this->allowTypes);
			}

			return true;
		}

		/**
		 * 检查上传的文件后缀是否合法
		 * @access private
		 *
		 * @param string $ext 后缀名
		 *
		 * @return boolean
		 */
		private function checkExt ($ext)
		{
			if (!empty($this->allowExts)) {
				return in_array(strtolower($ext), $this->allowExts, true);
			}

			return true;
		}

		/**
		 * 检查文件是否非法提交
		 * @access private
		 *
		 * @param string $filename 文件名
		 *
		 * @return boolean
		 */
		private function checkUpload ($filename)
		{
			return is_uploaded_file($filename);
		}

		/**
		 * 取得上传文件的信息
		 * @access public
		 * @return array
		 */
		public function getUploadFileList ()
		{
			return $this->uploadFileList;
		}

		/**
		 * 取得最后一次错误信息
		 * @access public
		 * @return string
		 */
		public function getErrorMsg ()
		{
			return $this->error;
		}

		/**
		 * 获取错误代码信息
		 * @access public
		 *
		 * @param string $errorNo 错误号码
		 *
		 * @return void
		 */
		protected function error ($errorNo)
		{
			switch ($errorNo) {
				case 1:
					$this->error = '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值';
					break;
				case 2:
					$this->error = '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值';
					break;
				case 3:
					$this->error = '文件只有部分被上传';
					break;
				case 4:
					$this->error = '没有文件被上传';
					break;
				case 6:
					$this->error = '找不到临时文件夹';
					break;
				case 7:
					$this->error = '文件写入失败';
					break;
				default:
					$this->error = '未知上传错误！';
			}

			return;
		}
	}