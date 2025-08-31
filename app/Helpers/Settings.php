<?php

use Illuminate\Support\Str;
use App\Utility\SettingsUtility;
use Illuminate\Support\Facades\File;
use App\Services\StorageService;

//return file path with public
if (!function_exists('my_asset')) {
    /**
     * Generate an asset path for the application.
     * Supports CDN and cloud storage configuration
     *
     * @param string $path
     * @param bool|null $secure
     * @return string
     */
    function my_asset($path, $secure = null)
    {
        // Check if it's a static asset (css, js, images in assets folder)
        if (Str::startsWith($path, ['assets/', 'css/', 'js/', 'images/'])) {
            return StorageService::assetUrl($path);
        }
        
        // Check if it's an upload path that should use storage
        if (Str::startsWith($path, ['uploads/', 'storage/'])) {
            return StorageService::url($path);
        }
        
        // Default to local public path
        return app('url')->asset('public/' . $path, $secure);
    }
}

//Get Settings
if (!function_exists('get_setting')) {
    function get_setting($key, $default = "")
    {
        $setting =  SettingsUtility::get_settings_value($key) ;
        return $setting == "" ? $default : $setting;
    }
}

//overWriteENVFile
if (!function_exists('overWriteEnvFile'))
{
    function overWriteEnvFile($type, $val)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            $val = '"'.trim($val).'"';
            if(is_numeric(strpos(file_get_contents($path), $type)) && strpos(file_get_contents($path), $type) >= 0){
                file_put_contents($path, str_replace(
                    $type.'="'.env($type).'"', $type.'='.$val, file_get_contents($path)
                ));
            }
            else{
                file_put_contents($path, file_get_contents($path)."\r\n".$type.'='.$val);
            }
        }
    }
}

if (!function_exists('getSocialAvatar')) {

    function getSocialAvatar($file, $path, $user){
        $fileContents = file_get_contents($file);
        $filename = md5(microtime()).'_'.$user->getId().'.jpg';
        $file = File::put(public_path() . $path . $filename, $fileContents);
        return $filename;
    }
}

if(!function_exists('generateUsername')){
    function generateUsername($name){
        $username = Str::lower(Str::slug($name));
        $uniqueUserName = $username.'-'.Str::lower(Str::random(5));
        return $uniqueUserName;
    }
}

if( !function_exists('process_string') ){

	function process_string($search_replace, $string){
	   	$result = $string;
	   	foreach($search_replace as $key=>$value){
			$result = str_replace($key, $value, $result);
	   	}
	   	return $result;
	}

}


if ( ! function_exists('xss_clean')){
	function xss_clean($data)
	{
		// Fix &entity\n;
		$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
		$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
		$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
		$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

		// Remove any attribute starting with "on" or xmlns
		$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

		// Remove javascript: and vbscript: protocols
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

		// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

		// Remove namespaced elements (we do not need them)
		$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

		do
		{
		    // Remove really unwanted tags
		    $old_data = $data;
		    $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
		}
		while ($old_data !== $data);

		// we are done...
		return $data;
	}
}

?>
