<?php
	/**
	 * Created by PhpStorm.
	 * User: BeenLee
	 * Date: 2020-12-17
	 * Time: 上午09:04
	 */

	namespace app\util;

	class FileUtil
	{
		// 错误信息
		private $error = '';
		public  $saveDir;

		/**
		 * Title: copyFile 复制文件
		 * User: BeenLee
		 * Date: 2020/12/17
		 * Time: 3:05 下午
		 *
		 * @param $savePath
		 * @param $oldPath
		 *
		 * @return bool|array
		 */
		public function copyFile ($savePath, $oldPath)
		{
			//如果不指定保存文件名，则由系统默认
			if (empty($savePath)) {
				$uploadPath = '/upload/' . $this->saveDir . '/' . date('Ymd') . '/';
				$savePath   = \Yii::getAlias('@upload') . '/' . $this->saveDir . '/' . date('Ymd') . '/';
			} else {
				$uploadPath = $savePath;
			}

			// 检查上传目录
			if (!is_dir($savePath)) {
				// 检查目录是否编码后的
				if (is_dir(base64_decode($savePath))) {
					$savePath = base64_decode($savePath);
				} else if (!mkdir($savePath, 0777, true) && !is_dir($savePath)) {// 尝试创建目录
					$this->error = '目标目录' . $savePath . '不存在';

					return false;
				}
			} else if (!is_writable($savePath)) {
				$this->error = '目标目录' . $savePath . '不可写';

				return false;
			}

			$file_name = basename($oldPath);
			$extension = pathinfo($oldPath, PATHINFO_EXTENSION);
			$file_name = time() . uniqid() . '.' . $extension;

			if (copy($oldPath, $savePath . $file_name)) {
				return ['file_name' => $file_name, 'local_path' => $savePath . $file_name, 'upload_path' => $uploadPath . $file_name];
			}

			$this->error = '保存失败';

			return false;
		}

		/**
		 * Title: getFileInfo 获取文件信息
		 * User: BeenLee
		 * Date: 2020/12/17
		 * Time: 4:17 下午
		 *
		 * @param     $filePath
		 * @param int $file_type
		 *
		 * @return array|false
		 */
		public function getFileInfo ($filePath, $file_type = 0)
		{
			$file['file_name']   = basename($filePath);
			$file['file_length'] = filesize($filePath);
			if ($file_type == 1) {
				//图片
				$img_data                  = getimagesize($filePath);
				$file['file_length']       = $img_data['bits'];
				$file['file_content_type'] = $img_data['mime'];
				$file['file_width']        = $img_data[0];
				$file['file_height']       = $img_data[1];
			} elseif ($file_type == 2) {
				// 获取音频的时长
				$runData = [];
				exec("ffmpeg -i " . $filePath . " 2>&1 | grep \"Duration:\" | awk -F ',' '{print $1}' | awk -F ' ' '{print $2}'", $runData);
				$file['media_duration'] = '';
				if (!empty($runData)) {
					$duration               = $runData[0];
					$file['media_duration'] = $duration;
				}
			} elseif ($file_type == 3) {
				// 获取视频文件的宽高
				$runData = [];
				exec("ffmpeg -i " . $filePath . " 2>&1 | grep ': Video:' | awk -F ':' '{print $4}'", $runData);
				$file['file_width']  = '';
				$file['file_height'] = '';
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
					if (isset($whString) && !empty($whString)) {
						$sizeArr = explode('x', trim($whString));
						if (isset($sizeArr[0]) && is_numeric($sizeArr[0])) {
							$file['file_width'] = $sizeArr[0];
						}
						if (isset($sizeArr[1]) && is_numeric($sizeArr[1])) {
							$file['file_height'] = $sizeArr[1];
						}
					}
				}

				// 获取视频的时长
				$runData = [];
				exec("ffmpeg -i " . $filePath . " 2>&1 | grep \"Duration:\" | awk -F ',' '{print $1}' | awk -F ' ' '{print $2}'", $runData);
				$file['media_duration'] = '';
				if (!empty($runData)) {
					$duration               = $runData[0];
					$file['media_duration'] = $duration;
				}
			} elseif ($file_type == 5) {
				//文件

			} else {
				$this->error = '参数不正确';

				return false;
			}

			if (empty($file['file_content_type'])) {
				if (function_exists('finfo_file')) {
					$finfo                     = finfo_open(PATHINFO_EXTENSION);
					$file['file_content_type'] = finfo_file($finfo, $filePath);
					finfo_close($finfo);
				}
			}

			if (empty($file['file_content_type'])) {
				if (function_exists('pathinfo')) {
					$file['file_content_type'] = pathinfo($filePath, PATHINFO_EXTENSION);
				}
			}

			if (empty($file['file_length'])) {
				if (function_exists('fstat')) {
					$handle              = fopen($filePath, 'rb');
					$fstat               = fstat($handle);
					$file['file_length'] = $fstat["size"];
					fclose($handle);
				}
			}

			return $file;
		}

		/**
		 * 取得最后一次错误信息
		 *
		 * @access public
		 * @return string
		 */
		public function getErrorMsg ()
		{
			return $this->error;
		}
	}