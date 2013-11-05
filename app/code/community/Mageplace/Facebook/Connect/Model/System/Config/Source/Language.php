<?php
/**
 * Mageplace Facebook "Like" Button
 *
 * @category	Mageplace_Facebook
 * @package		Mageplace_Facebook_Like
 * @copyright	Copyright (c) 2011 Mageplace. (http://www.mageplace.com)
 * @license		http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Facebook_Connect_Model_System_Config_Source_Language
{
    const URL_FACEBOOK_XML = "http://www.facebook.com/translations/FacebookLocales.xml";
			
    public function toOptionArray()
    {
		
		$xml = @simplexml_load_file(self::URL_FACEBOOK_XML);
 
		if($xml) {
			$lang = array();
			foreach ($xml as $object)
			{
				$lang[]=array('value' => $object->codes->code->standard->representation,
								'label' => $object->englishName);
			}
			return $lang;
		}else{			
			return array(
				array('value' => 'af_ZA','label' =>'Afrikaans'),
				array('value' => 'ar_AR','label' =>'Arabic'),
				array('value' => 'az_AZ','label' =>'Azerbaijani'),
				array('value' => 'be_BY','label' =>'Belarusian'),
				array('value' => 'bg_BG','label' =>'Bulgarian'),
				array('value' => 'bn_IN','label' =>'Bengali'),
				array('value' => 'bs_BA','label' =>'Bosnian'),
				array('value' => 'ca_ES','label' =>'Catalan'),
				array('value' => 'cs_CZ','label' =>'Czech'),
				array('value' => 'cy_GB','label' =>'Welsh'),
				array('value' => 'da_DK','label' =>'Danish'),
				array('value' => 'de_DE','label' =>'German'),
				array('value' => 'el_GR','label' =>'Greek'),
				array('value' => 'en_GB','label' =>'English (UK)'),
				array('value' => 'en_PI','label' =>'English (Pirate)'),
				array('value' => 'en_UD','label' =>'English (Upside Down)'),
				array('value' => 'en_US','label' =>'English (US)'),
				array('value' => 'eo_EO','label' =>'Esperanto'),
				array('value' => 'es_ES','label' =>'Spanish (Spain)'),
				array('value' => 'es_LA','label' =>'Spanish'),
				array('value' => 'et_EE','label' =>'Estonian'),
				array('value' => 'eu_ES','label' =>'Basque'),
				array('value' => 'fa_IR','label' =>'Persian'),
				array('value' => 'fb_LT','label' =>'Leet Speak'),
				array('value' => 'fi_FI','label' =>'Finnish'),
				array('value' => 'fo_FO','label' =>'Faroese'),
				array('value' => 'fr_CA','label' =>'French (Canada)'),
				array('value' => 'fr_FR','label' =>'French (France)'),
				array('value' => 'fy_NL','label' =>'Frisian'),
				array('value' => 'ga_IE','label' =>'Irish'),
				array('value' => 'gl_ES','label' =>'Galician'),
				array('value' => 'he_IL','label' =>'Hebrew'),
				array('value' => 'hi_IN','label' =>'Hindi'),
				array('value' => 'hr_HR','label' =>'Croatian'),
				array('value' => 'hu_HU','label' =>'Hungarian'),
				array('value' => 'hy_AM','label' =>'Armenian'),
				array('value' => 'id_ID','label' =>'Indonesian'),
				array('value' => 'is_IS','label' =>'Icelandic'),
				array('value' => 'it_IT','label' =>'Italian'),
				array('value' => 'ja_JP','label' =>'Japanese'),
				array('value' => 'ka_GE','label' =>'Georgian'),
				array('value' => 'km_KH','label' =>'Khmer'),
				array('value' => 'ko_KR','label' =>'Korean'),
				array('value' => 'ku_TR','label' =>'Kurdish'),
				array('value' => 'la_VA','label' =>'Latin'),
				array('value' => 'lt_LT','label' =>'Lithuanian'),
				array('value' => 'lv_LV','label' =>'Latvian'),
				array('value' => 'mk_MK','label' =>'Macedonian'),
				array('value' => 'ml_IN','label' =>'Malayalam'),
				array('value' => 'ms_MY','label' =>'Malay'),
				array('value' => 'nb_NO','label' =>'Norwegian (bokmal)'),
				array('value' => 'ne_NP','label' =>'Nepali'),
				array('value' => 'nl_NL','label' =>'Dutch'),
				array('value' => 'nn_NO','label' =>'Norwegian (nynorsk)'),
				array('value' => 'pa_IN','label' =>'Punjabi'),
				array('value' => 'pl_PL','label' =>'Polish'),
				array('value' => 'ps_AF','label' =>'Pashto'),
				array('value' => 'pt_BR','label' =>'Portuguese (Brazil)'),
				array('value' => 'pt_PT','label' =>'Portuguese (Portugal)'),
				array('value' => 'ro_RO','label' =>'Romanian'),
				array('value' => 'ru_RU','label' =>'Russian'),
				array('value' => 'sk_SK','label' =>'Slovak'),
				array('value' => 'sl_SI','label' =>'Slovenian'),
				array('value' => 'sq_AL','label' =>'Albanian'),
				array('value' => 'sr_RS','label' =>'Serbian'),
				array('value' => 'sv_SE','label' =>'Swedish'),
				array('value' => 'sw_KE','label' =>'Swahili'),
				array('value' => 'ta_IN','label' =>'Tamil'),
				array('value' => 'te_IN','label' =>'Telugu'),
				array('value' => 'th_TH','label' =>'Thai'),
				array('value' => 'tl_PH','label' =>'Filipino'),
				array('value' => 'tr_TR','label' =>'Turkish'),
				array('value' => 'uk_UA','label' =>'Ukrainian'),
				array('value' => 'vi_VN','label' =>'Vietnamese'),
				array('value' => 'zh_CN','label' =>'Simplified Chinese (China)'),
				array('value' => 'zh_HK','label' =>'Traditional Chinese (Hong Kong)'),
			);
		}
    }

}
