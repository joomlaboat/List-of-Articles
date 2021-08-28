<?php
/**
* List of Articles for Joomla!
* @author Ivan Komlev <support@joomlaboat.com>
* @co-developer Darren Forster <darrenforster99@gmail.com>
* @link https://joomlaboat.com
* @license GNU/GPL **/

if (!class_exists('ListofArticlesMisc'))
{
	class ListofArticlesMisc
	{
		public static function getURLQueryOption($urlstr, $opt)
		{
			$params = array();
			$query=explode('&',$urlstr);
			$newquery=array();

			for($q=0;$q<count($query);$q++)
			{
				$p=strpos($query[$q],$opt.'=');
				if($p!==false)
				{
					$parts=explode('=',$query[$q]);
					if(count($parts)>1)
						return $parts[1];
					else
						return '';
				}
			}
			return '';
		}
	}
}