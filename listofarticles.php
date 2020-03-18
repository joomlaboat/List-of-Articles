<?php
/**
* List of Articles Joomla! 3.x Native Plugin
* @version 1.3.9
* @author Ivan Komlev <support@joomlaboat.com>
* @co-developer Darren Forster <darrenforster99@gmail.com>
* @link https://joomlaboat.com
* @license GNU/GPL **/


defined('_JEXEC') or die('Restricted access');

	jimport('joomla.plugin.plugin');

	class plgContentListofArticles extends JPlugin
	{

		public function onContentPrepare($context, &$article, &$params, $limitstart=0)
		{
			require_once('render.php');
			$LOA=new ListofArticles();

			$LOA->articlecssclass=$this->params->get( 'articlecssclass' );
			$LOA->menucssclass=$this->params->get( 'menucssclass' );
			$LOA->addslash=(bool)(int)$this->params->get( 'addslash' );
			
			$article->text=$LOA->renderListOfArticles ($article->text, $params);
		}
	}
