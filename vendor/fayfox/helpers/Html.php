<?php
namespace fayfox\helpers;

use fayfox\core\FBase;
use fayfox\models\tables\Files;
use fayfox\models\File;
use fayfox\models\Qiniu;

/**
 * 构造html元素
 * 该类不会对标签属性做任何转义处理
 */
class Html extends FBase{
	public static function encode($input){
		return htmlentities($input, ENT_QUOTES, 'UTF-8');
	}
	
	public static function escape($input){
		return self::encode(stripslashes($input));
	}
	
	public static function input($name, $value = '', $type = 'text', $html_options = array()){
		return self::tag('input', array(
			'name'=>$name,
			'type'=>$type,
			'value'=>self::encode($value),
		) + $html_options);
	}

	public static function inputText($name, $value = '', $html_options = array()){
		return self::input($name, $value, 'text', $html_options);
	}
	
	public static function inputHidden($name, $value = '', $html_options = array()){
		return self::input($name, $value, 'hidden', $html_options);
	}
	
	public static function inputPassword($name, $value = '', $html_options = array()){
		return self::input($name, $value, 'password', $html_options);
	}
	
	public static function textarea($name, $value = '', $html_options = array()){
		return self::tag('textarea', array(
			'name'=>$name,
		) + $html_options, self::encode($value));
	}
	
	/**
	 * 生成一个复选框<br>
	 * 若在$html_options中指定label，则会在复选框外面套一个label标签，<br>
	 * 这是wrapper的一种便捷写法，故不可与wrapper属性合用
	 * @param string $name 复选框名称
	 * @param string $value 复选框值
	 * @param bool $checked 是否选中
	 * @param array $html_options
	 * @return string
	 */
	public static function inputCheckbox($name, $value, $checked = false, $html_options = array()){
		$html_options['name'] = $name;
		$html_options['type'] = 'checkbox';
		$html_options['value'] = self::encode($value);
		$html_options['checked'] = $checked ? 'checked' : false;
		
		if(isset($html_options['label'])){
			$html_options['wrapper'] = array('tag'=>'label', 'append'=>$html_options['label']);
			unset($html_options['label']);
		}
		return self::tag('input', $html_options);
	}
	
	/**
	 * 生成一个单选框<br>
	 * 若在$html_options中指定label，则会在单选框外面套一个label标签，<br>
	 * 这是wrapper的一种便捷写法，故不可与wrapper属性合用
	 * @param string $name 单选框名称
	 * @param string $value 单选框值
	 * @param bool $checked 是否选中
	 * @param array $html_options
	 * @return string
	 */
	public static function inputRadio($name, $value, $checked = false, $html_options = array()){
		$html_options['name'] = $name;
		$html_options['type'] = 'radio';
		$html_options['value'] = self::encode($value);
		$html_options['checked'] = $checked ? 'checked' : false;
		
		if(isset($html_options['label'])){
			$html_options['wrapper'] = array('tag'=>'label', 'append'=>$html_options['label']);
			unset($html_options['label']);
		}
		return self::tag('input', $html_options);
	}
	
	public static function select($name = '', $options = array(), $selected = array(), $html_options = array()){
		if(!is_array($selected)){
			$selected = array($selected);
		}
		
		$multiple = (isset($html_options['multiple']) && $html_options['multiple'] == true) ? ' multiple="multiple"' : '';
		unset($html_options['multiple']);
		$extra = '';
		foreach($html_options as $key => $val){
			if($val !== null && $val !== false){
				$extra .= " {$key}=\"{$val}\"";
			}
		}
		
		$form = '<select name="' . $name . '"' . $extra . $multiple . ">\n";
		
		foreach( $options as $key => $val ){
			if($val === false) continue;
			$key = (string) $key;
			if(is_array( $val ) && ! empty( $val )){
				$form .= '<optgroup label="' . $key . '">' . "\n";
				foreach( $val as $optgroup_key => $optgroup_val ){
					$sel =(in_array( $optgroup_key, $selected )) ? ' selected="selected"' : '';
						
					$form .= '<option value="' . $optgroup_key . '"' . $sel . '>' .( string ) $optgroup_val . "</option>\n";
				}
				$form .= '</optgroup>' . "\n";
			}else{
				$sel = (in_array($key, $selected)) ? ' selected="selected"' : '';
				$form .= '<option value="' . $key . '"' . $sel . '>' .( string ) $val . "</option>\n";
			}
		}
		
		$form .= '</select>';
		
		return $form;
	}
	
	/**
	 * 把无限极分类转为一个带缩进前缀的一维数组
	 */
	public static function getSelectOptions($data, $key = 'id', $value = 'title', $dep = 0){
		$return = array();
		$i = 0;
		foreach($data as $d){
			$i++;
			$data_length = count($data);
			if($dep){
				if($dep > 1){
					$pre = '│' . str_repeat('│', $dep - 2);
				}else{
					$pre = '';
				}
				if($i == $data_length && empty($d['children'])){
					$return[$d[$key]] = $pre.'└'.Html::encode($d[$value]);
				}else{
					$return[$d[$key]] = $pre.'├'.Html::encode($d[$value]);
				}
			}else{
				$return[$d[$key]] = Html::encode($d[$value]);
			}
			if(!empty($d['children'])){
				$return = $return + self::getSelectOptions($d['children'], $key, $value, $dep + 1);
			}
		}
		return $return;
	}
	
	/**
	 * 显示一张图片<br>
	 * 需要跟FileController配合使用的一个方法
	 * @param int $id 一般为系统图片ID，若传入url路径则第二个参数type无效
	 * @param int $type
	 * @param array $html_options 属性值不会被HTML编码
	 * @return string
	 */
	public static function img($id, $type = 1, $html_options = array()){
		if(is_numeric($id)){
			if($id == 0){
				//若有设置spares，返回对应的默认图片
				//若未设置，返回空字符串
				$spares = \F::app()->config->get('spares');
				if(isset($html_options['spare']) && isset($spares[$html_options['spare']])){
					$html = '<img src="'.\F::app()->view->url().$spares[$html_options['spare']].'"';

					if(isset($html_options['dw'])){
						$html .= ' width="'.$html_options['dw'].'"';
					}
					if(isset($html_options['dh'])){
						$html .= ' height="'.$html_options['dh'].'"';
					}
					unset($html_options['spare'], $html_options['dw'], $html_options['dh'], $html_options['x'], $html_options['y'], $html_options['w'], $html_options['h']);
					foreach($html_options as $key => $val){
						if($val !== null && $val !== false){
							$html .= " {$key}=\"{$val}\"";
						}
					}
					$html .= ' />';
					return $html;
				}else{
					return '';
				}
			}
			$img_params = array('t'=>$type);
			$file = Files::model()->find($id, 'raw_name,file_ext,file_path,image_width,image_height,qiniu,is_image,file_type');
			switch($type){
				case 1:
					if(substr($file['file_path'], 0, 4) == './..'){
						//私有文件，不能直接访问文件
						$src = \F::app()->view->url('file/pic', array(
							'f'=>$id,
						));
					}else{
						//公共文件，直接返回真实路径
						if(\F::app()->config->get('qiniu') && $file['qiniu']){
							//若开启了七牛云存储，且文件已上传，则显示七牛路径
							$src = Qiniu::model()->getUrl($file);
						}else{
							$src = \F::app()->view->url() . ltrim($file['file_path'], './') . $file['raw_name'] . $file['file_ext'];
						}
					}
					
					isset($html_options['width']) || $html_options['width'] = $file['image_width'];
					isset($html_options['height']) || $html_options['height'] = $file['image_height'];
				break;
				case 2:
					//显示一张缩略图，若不是图片文件，显示一个图标
					if(\F::app()->config->get('qiniu') && $file['qiniu'] && $file['is_image']){
						//若开启了七牛云存储，且文件已上传，则利用七牛在线裁剪为100x100图片
						$src = Qiniu::model()->getUrl($file, array(
							'dw'=>100,
							'dh'=>100,
						));
					}else{
						$src = File::model()->getThumbnailUrl($file);
					}
				break;
				case 3:
					if(isset($html_options['x'])){
						$img_params['x'] = $html_options['x'];
						unset($html_options['x']);
					}
					if(isset($html_options['y'])){
						$img_params['y'] = $html_options['y'];
						unset($html_options['y']);
					}
					if(isset($html_options['dw'])){
						$img_params['dw'] = $html_options['dw'];
						unset($html_options['dw']);
					}
					if(isset($html_options['dh'])){
						$img_params['dh'] = $html_options['dh'];
						unset($html_options['dh']);
					}
					if(isset($html_options['w'])){
						$img_params['w'] = $html_options['w'];
						unset($html_options['w']);
					}
					if(isset($html_options['h'])){
						$img_params['h'] = $html_options['h'];
						unset($html_options['h']);
					}
					ksort($img_params);
					$img_params['f'] = $id;
					$src = \F::app()->view->url('file/pic', $img_params);
				break;
				case 4:
					if(\F::app()->config->get('qiniu') && $file['qiniu']){
						//若开启了七牛云存储，且文件已上传，则利用七牛进行裁剪输出
						$src = Qiniu::model()->getUrl($file, array(
							'dw'=>isset($html_options['dw']) ? $html_options['dw'] : false,
							'dh'=>isset($html_options['dh']) ? $html_options['dh'] : false,
						));
					}else{
						if(isset($html_options['dw'])){
							$img_params['dw'] = $html_options['dw'];
						}
						if(isset($html_options['dh'])){
							$img_params['dh'] = $html_options['dh'];
						}
						ksort($img_params);
						$img_params['f'] = $id;
						$src = \F::app()->view->url('file/pic', $img_params);
					}
					unset($html_options['dw'], $html_options['dh']);
				break;
			}
			unset($html_options['spare']);
			$html = '<img src="'.$src.'" ';
			foreach($html_options as $key => $val){
				if($val !== null && $val !== false){
					$html .= " {$key}=\"{$val}\"";
				}
			}
			$html .= ' />';
			return $html;
		}else{
			$html = '<img src="'.$id.'" ';
			foreach($html_options as $key => $val){
				if($val !== null && $val !== false){
					$html .= " {$key}=\"{$val}\"";
				}
			}
			$html .= ' />';
			return $html;
		}
	}
	
	/**
	 * 构造一个超链接
	 * @param string $text
	 * @param url|array $uri
	 * @param array $html_options
	 * @param boolean $checkPermission 若该参数为true，且传入的uri是个数组，且存在权限验证方法的情况下，会做权限验证
	 * @return string
	 */
	public static function link($text, $uri = 'javascript:;', $html_options = array(), $checkPermission = false){
		if(is_array($uri)){
			if($checkPermission && method_exists(\F::app(), 'checkPermission')){
				if(!\F::app()->checkPermission($uri[0])){
					return '';
				}
			}
			$uri = \F::app()->view->url($uri[0],
				empty($uri[1]) ? array() : $uri[1],
				isset($uri[2]) && $uri[2] === false ? false : true);
		}
		
		$html_options['href'] = $uri;
		
		if(!empty($html_options['escape'])){
			$text = self::escape($text);
		}else if(!isset($html_options['encode']) || $html_options['encode'] == true){
			$text = self::encode($text);
		}
		if(!isset($html_options['title'])){
			$html_options['title'] = $text;
		}
		return self::tag('a', $html_options, $text);
	}
	
	/**
	 * 跳转到站外地址。
	 * 出于seo考虑，不直接显示站外地址，而是通过tools/redirect/index来跳转
	 * @param string $text
	 * @param string $url 包括http://在内的完整url
	 * @param array $html_options
	 */
	public static function outsideLink($text, $url, $html_options = array()){
		return self::link($text, \F::app()->view->url('redirect', array(
			'url'=>base64_encode($url),
		), false), $html_options);
	}
	
	/**
	 * 生成一个html标签<br>
	 * 该函数只负责拼装html，不做转义处理<br>
	 * 同时该函数不做参数格式正确性验证，传错了可能会出现报错
	 * @param string $tag
	 * @param array $html_options
	 * @param string|false $text 若为false，则视为自封闭标签
	 */
	public static function tag($tag, $html_options, $text = false){
		$before = '';
		$after = '';
		$append = '';
		$prepend = '';
		
		//4个特殊属性
		foreach(array('before', 'after', 'append', 'prepend') as $v){
			if(!empty($html_options[$v])){
				if(is_array($html_options[$v]) && isset($html_options[$v]['tag'])){
					$tag2 = $html_options[$v]['tag'];
					$text2 = isset($html_options[$v]['text']) ? $html_options[$v]['text'] : false;
					unset($html_options[$v]['tag'], $html_options[$v]['text']);
					$$v = self::tag($tag2, $html_options[$v], $text2);
				}else{
					$$v = $html_options[$v];
				}
				unset($html_options[$v]);
			}
		}
		
		if(!empty($html_options['wrapper'])){
			$wrapper = $html_options['wrapper'];
			unset($html_options['wrapper']);
		}
		
		$html = "<{$tag}";
		foreach($html_options as $name => $value){
			if($value === false)continue;
			$html .= ' ' . $name . '="' . $value . '"';
		}
		
		if($text === false){
			$html .= ' />';
		}else if(is_array($text)){
			if(isset($text['tag'])){
				$text_tag = $text['tag'];
				$text_text = $text['text'];
				unset($text['tag'], $text['text']);
				$html = $html . '>' . $prepend . self::tag($text_tag, $text, $text_text) . $append . "</{$tag}>";
			}else{
				$elements = array();
				foreach($text as $t){
					if(empty($t['tag'])){
						continue;
					}
					$t_tag = $t['tag'];
					$t_text = isset($t['text']) ? $t['text'] : false;
					unset($t['tag'], $t['text']);
					$elements[] = self::tag($t_tag, $t, $t_text);
				}
				$html = $html . '>' . $prepend . implode("\r\n", $elements) . $append . "</{$tag}>";
			}
		}else{
			$html = $html . '>' . $prepend . $text . $append . "</{$tag}>";
		}
		
		if(isset($wrapper)){
			if(is_array($wrapper) && isset($wrapper['tag'])){
				$wrapper_tag = $wrapper['tag'];
				unset($wrapper['tag']);
				return self::tag($wrapper_tag, $wrapper, $before . $html . $after);
			}else{
				return self::tag($wrapper, array(), $before . $html . $after);
			}
		}
		return $before . $html . $after;
	}
}