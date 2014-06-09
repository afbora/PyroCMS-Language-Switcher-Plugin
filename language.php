<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Language Switcher Plugin
 *
 * Language switcher plugin to demonstrate how things work
 *
 * @author		AFBora
 * @package		PyroCMS\Addon\Plugins
 * @copyright	Copyright (c) 2014, CodeQube
 * @link      	http://www.codeqube.com
 */
class Plugin_Language extends Plugin
{
	public $version = '1.0.1';

	public $name = array(
		'en'	=> 'Language Switcher'
	);

	public $description = array(
		'en'	=> 'Language Switcher of PyroCMS plugin structure.'
	);

	/**
	 * Returns a PluginDoc array that PyroCMS uses 
	 * to build the reference in the admin panel
	 *
	 * All options are listed here but refer 
	 * to the Blog plugin for a larger example
	 *
	 * @return array
	 */
	public function _self_doc()
	{
		$info = array(
			'switcher' => array(
				'description' => array(// a single sentence to explain the purpose of this method
					'en' => 'Switching public languages function'
				),
				'single' => false,// will it work as a single tag?
				'double' => true,// how about as a double tag?
				'variables' => 'code|name|direction|img|link|folder|selected', // list all variables available inside the double tag. Separate them|like|this
				'attributes' => array(
					'mode' => array(// this is the name="World" attribute
						'type' => 'text',// Can be: slug, number, flag, text, array, any.
						'flags' => '',// flags are predefined values like asc|desc|random.
						'default' => 'png',// this attribute defaults to this if no value is given
						'required' => false,// is this attribute required?
					),
				),
			),
		);
	
		return $info;
	}

	/**
	 * Language switcher allow to display selected site language as text or
	 * image flags and allow users to change current language.
	 *
	 * Options list :
	 *
	 * mode : select mode txt or gif or png
	 *
	 * 	Usage switcher 	
	 *
	 * 	<ul class="dropdown-menu">
	 * 		{{ language:switcher mode="png" }}
	 *
	 * 		<li><a href="{{ link }}">{{ img }} {{ name }}</a></li>
	 *
	 * 		{{ /language:switcher }}
	 * 	</ul>
	 */
	public function	switcher()
	{
		$data		= array();
		
		// Languages display mode png or gif. Default is png
		$mode			= $this->attribute('mode', 'png');		
		
		// Add language images path
		foreach (array(APPPATH, ADDONPATH, SHARED_ADDONPATH) as $directory)
    	{
			// some servers return false instead of an empty array
			if ($directory && file_exists($directory.'plugins/language/'))
			{
				Asset::add_path('language', $directory.'plugins/language/');
			}
		}
		
		// Get Supported Languages
		$languages		= config_item('supported_languages');
		
		// Get Site Lang
		$site_lang		= $languages[$this->settings->site_lang]['folder'];
		
		// Get Current Language
		$default_lang	= config_item('language') != $site_lang ? config_item('language') : $site_lang;
      			
		// Get languages data from cache
		if ( !($data = $this->pyrocache->get('language-switcher-'.$default_lang)))
		{	
			// Get Only Public Languages
			$public			= explode(",", Settings::get('site_public_lang'));
			
			if (isset($languages) && !empty($languages))
			{      		
				// Languages Loop
				foreach ($languages as $code => $language)
				{
					// If language is not in publics, do not show
					if(!in_array($code , $public)) continue;
					
					// Language Data Araray
					$lang				= array();
					$lang['code']		= $code; 											// Language code: en tr
					$lang['name']		= $language['name'];								// Language name: English Turkish
					$lang['direction']	= $language['direction'];							// Language direction: ltr rtl
					$lang['folder']		= $language['folder'];								// Language folder: english turkish
					
					// delete and edit language query because of prevent loop like > /?lang=en?lang=en
					if(isset($_GET['lang']))
					{
						unset($_GET['lang']); 										// delete lang parameter;
						$_GET['lang'] = $code;										// add new lang uri
						
						$uri = trim('/', current_url()).'?'.http_build_query($_GET); // build query
					}
					else
					{
						$uri = trim('/', current_url()).'?lang='.$code;
					}
					
					$qs = http_build_query($_GET);
					
					$lang['link'] 		= $uri;	// Language switcher link: ?lang=en
				
					switch ($mode)
					{
						case 'gif':
							$lang['img'] = Asset::img('language::gif/'.$code.'.gif', $code, array());
						break;
	
						case 'png':
						default:
							$lang['img'] = Asset::img('language::png/'.$code.'.png', $code, array());
					} 	// Language Image: <img src="addons/shared_addons/plugins/language/img/png/en.png" />
					
					// Keep lang if current
					if($default_lang == $lang['folder'])
					{
						$lang['selected']		= 'selected';
						$current_lang 			= $lang;
					}
					else
					{
						$lang['selected']		= '';
						$data[] = $lang;
					}
				}
			}			
			
			// Put the first the current language
			array_unshift($data, $current_lang);
			
			$this->pyrocache->write($data, 'language-switcher-'.$default_lang, 1000); // Save to cache
		}
		
		return $data;
	}
	
}

/* End of file example.php */
