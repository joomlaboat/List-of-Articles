<?php
/**
* List of Articles for Joomla!
* @version 1.3.9
* @author Ivan Komlev <support@joomlaboat.com>
* @co-developer Darren Forster <darrenforster99@gmail.com>
* @link https://joomlaboat.com
* @license GNU/GPL **/

if (!class_exists('ListofArticles')) {


defined('_JEXEC') or die('Restricted access');

//Which mode to operate in - ArticleList or MenuList
defined('ARTICLELIST') or define ('ARTICLELIST', 0 ) ;
defined('MENULIST') or define ('MENULIST', 1 ) ;

//Search Text Options
defined('BEGIN') or define ('BEGIN',0 ) ;
defined('END') or define ('END',1 ) ;

//Return options
defined('PLUGIN') or define ('PLUGIN',0 ) ;
defined('DB') or define ('DB', 1 ) ;
defined('SEARCH') or define ('SEARCH', 2 ) ;
defined('COLS') or define ('COLS', 3 ) ;
defined('ORDER') or define ('ORDER', 4 ) ;

//Parameter positions
defined('COL') or define ( 'COL', 0 ) ;
defined('START') or define ( 'START', 2 ) ;
defined('LIMIT') or define ( 'LIMIT', 3 ) ;
defined('SHOWACTIVELINK') or define ( 'SHOWACTIVELINK', 5 ) ;
defined('OPT_SEPARATOR') or define ( 'OPT_SEPARATOR', 6 ) ;
defined('OPT_VALUEFIELD') or define ( 'OPT_VALUEFIELD', 7 ) ;
defined('EXCLUDELIST') or define ( 'EXCLUDELIST', 8 ) ;
defined('CSSSTYLE') or define ( 'CSSSTYLE', 9 ) ;
defined('RECURSIVE') or define ( 'RECURSIVE', 10 ) ;
defined('ORIENTATION') or define ( 'ORIENTATION', 11 ) ;
defined('IMAGEREPLACER_POS') or define ( 'IMAGEREPLACER_POS', 12 ) ;

//Seperators
defined('PLUGSEP') or define ( 'PLUGSEP', '=' ) ;
defined('OPTSEP') or define ( 'OPTSEP', ',' ) ;
defined('PLUGSTART') or define ( 'PLUGSTART', '{' ) ;
defined('PLUGEND') or define ( 'PLUGEND', '}' ) ;

class ListofArticles
{
    var $articlecssclass;
    var $menucssclass;
	var $addslash;

    function renderListOfArticles($text, &$params)
    {
    	$this->CreateList ( $text, ARTICLELIST) ;
    	$this->CreateList ( $text, MENULIST) ;

    	return $text;
    }

    //Returns various information
    function strip_html_tags_textarea( $text )
    {

	    $text = preg_replace(
        array(
          // Remove invisible content
            '@<textarea[^>]*?>.*?</textarea>@siu',
        ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',"$0", "$0", "$0", "$0", "$0", "$0","$0", "$0",), $text );

		return $text ;
    }

    function retVals ( $Val, $Mode )
    {


		switch ( $Mode )
		{
			case	MENULIST	:	switch ( $Val )
								{
									case	PLUGIN	:	return ( 'menuitemsoftype' ) ;
									case	DB		:	return ( 'menu' ) ;
									case	SEARCH	:	return ( 'menutype' ) ;
									case	COLS	:	return ( 'id,title,link,menutype,type,params' ) ;
									case	ORDER	:	return ( 'lft' ) ;
								}
			case	ARTICLELIST	:	switch ( $Val )
								{
									case	PLUGIN	:	return ( 'articlesofcategory' ) ;
									case	DB		:	return ( 'content' ) ;
									case	SEARCH	:	return ( '#__content.catid' ) ;
									case	COLS	:	return ( '
																#__content.id AS id,
																#__content.title AS title,
																#__content.introtext AS introtext,
																#__content.created AS created,
                                                                #__content.modified AS modified,
																#__content.metadesc AS metadesc,
																#__menu.link AS link,
																#__menu.id AS Itemid
																') ;
									case	ORDER	:	return ( '#__content.ordering,#__content.title' ) ;
								}
		}


		return '';


}



function CreateList ( &$text_original, $Mode)
{

    $text=$this->strip_html_tags_textarea($text_original);

	$options = array () ;
    $looking_for=$this->retVals (PLUGIN,$Mode);

	$fList=$this->LOAgetListToReplace($this->retVals (PLUGIN,$Mode),$options,$text);


	$text_original = $this->replaceText ( $fList, $options, $text_original, $Mode, 0 ) ;

}

function replaceWith ( $options, $mode )
{
	$catid =0;
	$opts = $this->Options ( $this->LOAcsv_explode ( OPTSEP,$options, '"',false), $catid ) ;

	$orderby_field=$opts[ORDER]; //{***=CATEGORY ID, |COLUMNS|, |START|, |LIMIT|, |ORDER BY|}


	$rows = $this->LOAgetRows ( $catid, $mode, $orderby_field, $opts[START], $opts[LIMIT], $opts[SHOWACTIVELINK], $opts[EXCLUDELIST], $opts[RECURSIVE] ) ;

	if(!isset($opts[COL]))
		$opts[COL]=1;


	switch ( $mode )
	{
		case	ARTICLELIST	:

            if($opts[OPT_VALUEFIELD])
				$valueoption=$opts[OPT_VALUEFIELD];
			else
				$valueoption='title';


			if($opts[COL] == 0)
			{

				if($opts[OPT_SEPARATOR])
					$separator=$opts[OPT_SEPARATOR];
				else
					$separator=',';

				return $this->LOAmakeArticleCleanLinks ( $rows, 0, $opts[SHOWACTIVELINK], $separator, $valueoption,$opts[CSSSTYLE]);
			}
			elseif($opts[COL] == 1)
				return $this->LOAmakeArticleList ( $rows,  $opts[SHOWACTIVELINK], $valueoption, $opts[CSSSTYLE]);
			else
				return $this->LOAmakeArticleListTable ( $rows, $opts[COL],  $opts[SHOWACTIVELINK], $valueoption, $opts[CSSSTYLE],$opts[ORIENTATION]=='vertical');



			break;
		case	MENULIST	:

			if($opts[COL] == 0)
			{
				if($opts[OPT_SEPARATOR])
					$separator=$opts[OPT_SEPARATOR];
				else
					$separator=',';

				if($opts[OPT_VALUEFIELD])
					$valueoption=$opts[OPT_VALUEFIELD];
				else
					$valueoption='title';


				return $this->LOAmakeMenuCleanLinks ( $rows, 0, $opts[SHOWACTIVELINK], $separator, $valueoption,  $opts[CSSSTYLE],$opts[IMAGEREPLACER_POS] );
			}
			elseif($opts[COL] == 1)
				return $this->LOAmakeMenuList ( $rows, $opts[SHOWACTIVELINK], $opts[CSSSTYLE]);
			else
				return $this->LOAmakeMenuListTable ( $rows, $opts[COL],  $opts[SHOWACTIVELINK], $opts[CSSSTYLE]);

			break;
	}
}


function Options ( $opts, &$catid )
{
	$ret = array () ;
	$catid = ( $opts[0]==''?'%':$opts[0] );

	if(isset($opts[COL+1]))
	{
		$ret[COL] = (int)strip_tags($opts[COL+1]);
		$ret[COL] = ( $ret[COL]<0  ? 0  : $ret[COL] ) ;
		$ret[COL] = ( $ret[COL]>10 ? 10 : $ret[COL] ) ;
	}
	else
		$ret[COL]=1;

	if(isset(  $opts[START] ))
		$ret[START] = (int)strip_tags($opts[START]);
	else
		$ret[START] = 0;

	if(isset(  $opts[LIMIT] ))
		$ret[LIMIT] = (int)strip_tags($opts[LIMIT]);
	else
		$ret[LIMIT] = 0;

	if(isset(  $opts[ORDER] ))
		$ret[ORDER] = strip_tags($opts[ORDER]);
	else
		$ret[ORDER] ='';



	if(isset($opts[SHOWACTIVELINK]))
		$ret[SHOWACTIVELINK] = strip_tags($opts[SHOWACTIVELINK]);
	else
		$ret[SHOWACTIVELINK]='';

	if(isset($opts[OPT_SEPARATOR]))
		$ret[OPT_SEPARATOR]  = strip_tags($opts[OPT_SEPARATOR]);
	else
		$ret[OPT_SEPARATOR]  =',';

	if(isset($opts[OPT_VALUEFIELD]))
		$ret[OPT_VALUEFIELD] = $opts[OPT_VALUEFIELD];
	else
		$ret[OPT_VALUEFIELD] = 'title';

	if(isset($opts[EXCLUDELIST]))
		$ret[EXCLUDELIST] = explode(',',strip_tags($opts[EXCLUDELIST]));
	else
		$ret[EXCLUDELIST] = array();

	if(isset($opts[CSSSTYLE]))
		$ret[CSSSTYLE] = strip_tags($opts[CSSSTYLE]);
	else
		$ret[CSSSTYLE] = '';

	if(isset($opts[RECURSIVE]))
		$ret[RECURSIVE] = strip_tags($opts[RECURSIVE]);
	else
		$ret[RECURSIVE] = '';

	if(isset($opts[ORIENTATION]))
		$ret[ORIENTATION] = strip_tags($opts[ORIENTATION]);
	else
		$ret[ORIENTATION] = '';

    if(isset($opts[IMAGEREPLACER_POS]))
		$ret[IMAGEREPLACER_POS] = strip_tags($opts[IMAGEREPLACER_POS]);
	else
		$ret[IMAGEREPLACER_POS] = '';

	return ( $ret ) ;
}

function isTrue ( $check, $val1, $val2 ) {	return ( $check ? $val1 : $val2 ) ; }
function retSearch ( $Search ) { return ( '="'.( $Search == '' ? '%' : $Search ).'"' ) ; }
function replaceText ( $fList, $options, $text, $Mode,  $count)
{


	if($count==count( $fList ))
		return $text;
	else
	{
		return $this->replaceText ($fList, $options, str_replace ( $fList[$count], $this->replaceWith ( $options[$count], $Mode ), $text ), $Mode, $count+1 ) ;
	}

}

function LOAmakeLink ($link, $title, $sep=false, $showactivelink,$id, $current_id, $metadesc, $cssstyle)
{
	$metadesc_=urldecode($metadesc);
	$metadesc_=str_replace('"','',$metadesc_);

	if($showactivelink=='showinactive' and $id==$current_id)
		$linkitem =$this->JTextExtended($title);
	else
		$linkitem = '<a href="'.$link.'"'.($metadesc_!='' ? ' title="'.$metadesc_.'"' : '').($cssstyle!='' ? ' style="'.$cssstyle.'"' : '').'>'.$this->JTextExtended($title).'</a>';

	return ( $sep ? '<li class="separator">'.$this->JTextExtended($title).'</li>':'<li>'.$linkitem .'</li>' );
}
function LOAmakeLink_forTable ($link, $title, $sep=false, $showactivelink, $id, $current_id, $metadesc, $cssstyle)
{
	$metadesc_=urldecode($metadesc);
	$metadesc_=str_replace('"','',$metadesc_);

	if(($showactivelink=='showinactive' and $id==$current_id) or $sep)
		$linkitem =$this->JTextExtended($title);
	else
		$linkitem = '<a href="'.$link.'"'.($metadesc_!='' ? ' title="'.$metadesc_.'"' : '').($cssstyle!='' ? ' style="'.$cssstyle.'"' : '').'>'.$this->JTextExtended($title).'</a>';

	return $linkitem;
}

function LOAmakeArticleCleanLink ( $row,$showactivelink, $valueoption_withparams, $cssstyle)
{
    $jinput = JFactory::getApplication()->input;

    $parts=explode(':',$valueoption_withparams);

    $valueoption=$parts[0];
    $params='';
    if(isset($parts[1]))
    {
        $p=array();
        for($i=1;$i<count($parts);$i++)
            $p[]=$parts[$i];

        $params=implode(':',$p);
    }

    //article fields
	if($valueoption=='title')
    {
		return $row->title;
    }
	elseif($valueoption=='created')
    {
        if($params!='')
        {
            $phpdate =strtotime($row->created);
			return date($params, $phpdate);
        }
        else
    		return $row->created;

    }
    elseif($valueoption=='modified')
    {
        if($params!='')
        {
            $phpdate =strtotime($row->modified);
			return date($params, $phpdate);
        }
        else
    		return $row->modified;
    }
	elseif($valueoption=='publish_up')
    {
        if($params!='')
        {
            $phpdate =strtotime($row->publish_up);
			return date($params, $phpdate);
        }
        else
    		return $row->publish_up;
    }
	elseif($valueoption=='ordering')
		return $row->ordering;
	elseif($valueoption=='metadata')
		return $row->metadata;
	elseif($valueoption=='metakey')
		return $row->metakey;
	elseif($valueoption=='featured')
		return $row->featured;
	elseif($valueoption=='language')
		return $row->language;
	elseif($valueoption=='hits')
		return $row->hits;
	elseif($valueoption=='link')
		return JRoute::_($this->LOAmakeArticleLinkOnly($row));
	elseif($valueoption=='linkandtitle')
	{
	    if($showactivelink=='showinactive')
	    {
            if($jinput->getInt ('id', 0)==$row->id)
                return $this->JTextExtended($row->title);
	    }

	    return '<a href="'.$this->LOAmakeArticleLinkOnly($row).'" title="'.$this->JTextExtended($row->title).'"'.($cssstyle!='' ? ' style="'.$cssstyle.'"' : '').'>'.$this->JTextExtended($row->title).'</a>';
	}
	elseif($valueoption=='encodedlink')
		return urlencode(JRoute::_($this->LOAmakeArticleLinkOnly($row)));
    else
        return 'Field "'.$valueoption.'" not found/accepted.';

}


function LOAmakeArticleLink ($row, $showactivelink, $valueoption_withparams, $cssstyle)
{
    $jinput = JFactory::getApplication()->input;
	$aLink=$this->LOAmakeArticleLinkOnly($row);

    $parts=explode(':',$valueoption_withparams);

    $valueoption=$parts[0];
    $params='';
    if(isset($parts[1]))
    {
        $p=array();
        for($i=1;$i<count($parts);$i++)
            $p[]=$parts[$i];

        $params=implode(':',$p);
    }

    if($valueoption=='title')
    {
		$title=$row->title;
    }
	elseif($valueoption=='created')
    {
        if($params!='')
        {
            $phpdate =strtotime($row->created);
			$title=date($params, $phpdate);
        }
        else
    		$title=$row->created;
    }
    elseif($valueoption=='modified')
    {
        if($params!='')
        {
            $phpdate =strtotime($row->modified);
			$title=date($params, $phpdate);
        }
        else
    		$title=$row->modified;
    }
	elseif($valueoption=='publish_up')
    {
        if($params!='')
        {
            $phpdate =strtotime($row->publish_up);
			$title=date($params, $phpdate);
        }
        else
    		$title=$row->publish_up;
    }
	elseif($valueoption=='ordering')
		$title=$row->ordering;
	elseif($valueoption=='metadata')
		$title=$row->metadata;
	elseif($valueoption=='metakey')
		$title=$row->metakey;
	elseif($valueoption=='featured')
		$title=$row->featured;
	elseif($valueoption=='language')
		$title=$row->language;
	elseif($valueoption=='hits')
		$title=$row->hits;
    else
        $title='Field "'.$valueoption.'" not found/accepted.';

	return $this->LOAmakeLink ( $aLink, $this->JTextExtended($title),false, $showactivelink,$row->id,$jinput->getInt ('id', 0), $row->metadesc, $cssstyle);
}
function LOAmakeArticleLinkOnly($row)
{
    $jinput = JFactory::getApplication()->input;

	$Itemid=0;
	//$aLink='index.php?option=com_content&view=article&id='.$row->id;

	if(isset($row->link) and strpos($row->link,'&id='.$row->id)!==false)
    {
        $aLink='index.php?option=com_content&view=article&id='.$row->id;
		$Itemid=$row->Itemid;
    }
	else
	{
		$Itemid=$jinput->getInt ('Itemid', 0);
		$Option_=$jinput->getCmd ('option', '');
		$View_=$jinput->getCmd ('view','');

        $aLink='/index.php?option=com_content&view=article&id='.$row->id;
	}


	if($Itemid!=0)
		$aLink.='&Itemid='.$Itemid;

	return $aLink;
}

function LOAmakeArticleLink_forTable ($row, $showactivelink, $valueoption_withparams, $cssstyle)
{
    $jinput = JFactory::getApplication()->input;
	$aLink=$this->LOAmakeArticleLinkOnly($row);

	$metadesc='';
	if(isset($row->metadesc))
		$metadesc=$row->metadesc;

    $parts=explode(':',$valueoption_withparams);

    $valueoption=$parts[0];
    $params='';
    if(isset($parts[1]))
    {
        $p=array();
        for($i=1;$i<count($parts);$i++)
            $p[]=$parts[$i];

        $params=implode(':',$p);
    }

    if($valueoption=='title')
		$title=$row->title;
	elseif($valueoption=='created')
    {
        if($params!='')
        {
            $phpdate =strtotime($row->created);
			$title=date($params, $phpdate);
        }
        else
    		$title=$row->created;
    }
    elseif($valueoption=='modified')
    {
        if($params!='')
        {
            $phpdate =strtotime($row->modified);
			$title=date($params, $phpdate);
        }
        else
    		$title=$row->modified;
    }
	elseif($valueoption=='publish_up')
    {
        if($params!='')
        {
            $phpdate =strtotime($row->publish_up);
			$title=date($params, $phpdate);
        }
        else
    		$title=$row->publish_up;
    }
	elseif($valueoption=='ordering')
		$title=$row->ordering;
	elseif($valueoption=='metadata')
		$title=$row->metadata;
	elseif($valueoption=='metakey')
		$title=$row->metakey;
	elseif($valueoption=='featured')
		$title=$row->featured;
	elseif($valueoption=='language')
		$title=$row->language;
	elseif($valueoption=='hits')
		$title=$row->hits;
    else
        $title='Field "'.$valueoption.'" not found/accepted.';

	return $this->LOAmakeLink_forTable ( $aLink, $this->JTextExtended($title),false, $showactivelink, $row->id,$jinput->getInt ('id', 0), $metadesc, $cssstyle);
}

function LOAmakeArticleLinks ( $rows, $count, $showactivelink, $valueoption, $cssstyle)
{
	if($count==count ($rows))
	{
		return '';
	}
	else
	{
		return $this->LOAmakeArticleLink ($rows[$count],$showactivelink, $valueoption, $cssstyle).$this->LOAmakeArticleLinks ($rows, $count+1, $showactivelink,$valueoption, $cssstyle) ;
	}


}

function LOAmakeArticleCleanLinks ( $rows, $count, $showactivelink, $separator, $valueoption,$cssstyle)
{
	if($count==count ($rows))
	{
		return '';
	}
	else
	{
		$v=$this->LOAmakeArticleCleanLink ($rows[$count],$showactivelink, $valueoption,$cssstyle);
		$v_next=$this->LOAmakeArticleCleanLinks (  $rows, $count+1, $showactivelink, $separator, $valueoption,$cssstyle);
		if($v_next!='')
			return $v.$separator.$v_next;
		else
			return $v;

	}


}



function LOAmakeArticleList($rows, $showactivelink, $valueoption, $cssstyle)
{
	return ( '<!-- System Plugin:List of Article List Style -->'
				.($this->articlecssclass ? '<div class="'.$this->articlecssclass.'">' : '')
				.'<ul>'
				.$this->LOAmakeArticleLinks ( $rows, 0, $showactivelink, $valueoption, $cssstyle)
				.'</ul>'
				.($this->articlecssclass ? '</div>' : '')
				)
			;
}

function LOAmarkArticleLinkCol ($rows, $cols, $start, $count, $showactivelink, $valueoption, $cssstyle)
{
	if(($count+$start == count ( $rows ) )	 ||	( $count==$cols ) ) return '';

	$result='<td>';

	$result.=$this->LOAmakeArticleLink_forTable ( $rows[$count+$start], $showactivelink, $valueoption, $cssstyle).'</td>';


	$result_=$this->LOAmarkArticleLinkCol ( $rows, $cols, $start, $count+1, $showactivelink, $valueoption, $cssstyle);
	if($result_=='' and $cols-$count-1>0)
		$result.=str_repeat ('<td></td>', $cols-$count-1);
	else
		$result.=$result_;

	return $result;
}

function LOAmarkArticleLinkTable ($rows, $cols, $count,$showactivelink, $valueoption, $cssstyle)
{
	if($count >= count ( $rows ))
		return '';
	else
		return '<tr>'.$this->LOAmarkArticleLinkCol ($rows, $cols, $count, 0,$showactivelink, $valueoption, $cssstyle).'</tr>'
        .$this->LOAmarkArticleLinkTable ( $rows, $cols, $count + $cols, $showactivelink, $valueoption, $cssstyle) ;
}

function LOAmakeArticleListTable($rows,$cols,$showactivelink, $valueoption, $cssstyle,$isVertical)
{


	$result ='';
	$result.='
<!-- List of Article Table Style-->';

	$result.=($this->articlecssclass ? '<div class="'.$this->articlecssclass.'">' : '');
	$result.='<table><tbody>';

	if($isVertical)
	{
	    if($cols<1)
		$cols=1;

	    $r=count($rows);

	    $computed_rows=floor($r/$cols);
	    if($r % $cols>0)
		$computed_rows++;

	    $result.='<!-- Vertical-->';
	    $newRows=array();

	    for($x=0;$x<$computed_rows;$x++)
	    {
		for($y=0;$y<$r;$y+=$computed_rows)
		{
		    $p=$x+$y;
		    if($p>=$r)
			break;

		    $newRows[]=$rows[$p];
		}
	    }

	    $result.='
'.$this->LOAmarkArticleLinkTable ($newRows, $cols, 0, $showactivelink, $valueoption, $cssstyle);
	}
	else
	{
	    $result.='
'.$this->LOAmarkArticleLinkTable ($rows, $cols, 0, $showactivelink, $valueoption, $cssstyle);
	}

	$result.='</tbody></table>';
	$result.=($this->articlecssclass ? '</div>' : '');


	return $result;
}

function LOAmakeMenuListLink ($row, $showactivelink, $cssstyle)
{
	$jinput = JFactory::getApplication()->input;

	if($row->type=='url')
		$menuitem_Link=$row->link;
	else
	{
		if(strpos($row->link,'?')===false)
			$menuitem_Link=$row->link.'?Itemid='.$row->id;
		else
			$menuitem_Link=$row->link.'&amp;Itemid='.$row->id;
	}


	$metadesc=$this->getMenuParam('menu-meta_description', $row->params);
	return $this->LOAmakeLink ( $menuitem_Link, $row->title, ( $row->menutype == 'separator' ),$showactivelink, $row->id, $jinput->getInt ('Itemid', 0), $metadesc, $cssstyle) ;

}
function LOAmakeMenuListLink_forTable ($row ,$showactivelink, $cssstyle)
{
    $jinput = JFactory::getApplication()->input;

	if($row->type=='url')
	{
		$menuitem_Link=$row->link;
	}
	else
	{
		if(strpos($row->link,'?')===false)
			$menuitem_Link=$row->link.'?Itemid='.$row->id;
		else
			$menuitem_Link=$row->link.'&amp;Itemid='.$row->id;
	}


	$metadesc=$this->getMenuParam('menu-meta_description', $row->params);
	return $this->LOAmakeLink_forTable ( $menuitem_Link, $row->title, ( $row->menutype == 'separator' ),$showactivelink, $row->id, $jinput->getInt ('Itemid', 0), $metadesc, $cssstyle);

}


function LOAmakeMenuCleanLinks ($rows, $count, $showactivelink, $separator, $valueoption, $cssstyle,$imagereplacer)
{
	if($count==count ($rows))
	{
		return '';
	}
	else
	{
		$v=$this->LOAmakeMenuCleanLink ($rows[$count],$showactivelink, $valueoption, $cssstyle,$imagereplacer);
		$v_next=$this->LOAmakeMenuCleanLinks (  $rows, $count+1, $showactivelink, $separator, $valueoption, $cssstyle,$imagereplacer);
		if($v_next!='')
			return $v.$separator.$v_next;
		else
			return $v;

	}

}


function LOAmakeMenuCleanLink ( $row,$showactivelink, $valueoption_str, $cssstyle,$imagereplacer)
{
	$output='';
	$valueoptions=explode(',',$valueoption_str);

	if(count($valueoptions)==1)
		return $this->LOAmakeMenuCleanLink_Item ( $row,$showactivelink, $valueoption_str, $cssstyle,true,$imagereplacer);

	foreach($valueoptions as $valueoption)
	{
		$output.=$this->LOAmakeMenuCleanLink_Item ( $row,$showactivelink, $valueoption, $cssstyle,false,$imagereplacer);
	}

	$title=$row->title;

	$link=JRoute::_($row->link.'&Itemid='.$row->id);

	$output='<div>'.$output.'</div>';

	$a='<a href="'.$link.'" title="'.$title.'"'.($cssstyle!='' ? ' style="'.$cssstyle.'"' : '').'>'.$output.'</a>';

	return $a;
}

function LOAmakeMenuCleanLink_Item ( $row,$showactivelink, $valueoption, $cssstyle,$addlink=true,$imagereplacer)
{
    $jinput = JFactory::getApplication()->input;

	if($valueoption=='title' or $valueoption=='name')
	{
        $title=$this->JTextExtended($row->title);
		return '<span>'.$title.'</span>';
	}
	elseif($valueoption=='lft')
		return $row->lft;
	elseif($valueoption=='rtg')
		return $row->rtg;
	elseif($valueoption=='language')
		return $row->language;
	elseif($valueoption=='link')
		return JRoute::_($row->link.'&Itemid='.$row->id);
	elseif(strpos($valueoption,'params:')!==false)
	{
		$title=$this->JTextExtended($row->title);

		$src='';

		$pair=explode(':',$valueoption);

		if(isset($pair[1]))
		{
			//$options=explode(',',$pair[1]);
			$j=(array)json_decode($row->params);


			if(isset($j[$pair[1]]))
			{
				$src=$j[$pair[1]];
				if($src!="")
				{

					if($imagereplacer!="")
					{
						$pair=explode(',',$imagereplacer);
						if(count($pair)==2)
						{
							$new_src=str_replace($pair[0],$pair[1],$src);

							$imagefile=JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$new_src);

							if(file_exists($imagefile))
								$src=$new_src;
						}
					}
				}

			}
		}

        //echo '$valueoption='.$valueoption.'<br/>';
        //echo '$options[1]='.$options[1];

		$link=JRoute::_($row->link.'&Itemid='.$row->id);

		if($src=="")
		{
			if($addlink)
            {
                if(isset($pair[2]) and $pair[2]=='button')
                    return '<a href="'.$link.'" title="'.$title.'"><button class="btn button btn-primary"><div>'.$title.'</div></button></a>';
                else
                    return '<a href="'.$link.'" title="'.$title.'"'.($cssstyle!='' ? ' style="'.$cssstyle.'"' : '').'><span>'.$title.'</span></a>';
            }
			else
				return '<span>'.$title.'</span>';
		}
		else
		{
			$img='<img src="'.($this->addslash ? '/' : '').$src.'" title="'.$title.'" alt="'.$title.'" />';

			if($addlink)
            {
                if(isset($pair[2]) and $pair[2]=='button')
                    return '<a href="'.$link.'" title="'.$title.'"><div><button class="btn button btn-primary">'.$img.'<span>'.$title.'</span></button></div></a>';
				else
                    return '<a href="'.$link.'" title="'.$title.'"'.($cssstyle!='' ? ' style="'.$cssstyle.'"' : '').'>'.$img.'</a>';
            }
			else
				return $img;
		}
	}
    elseif($valueoption=='button')
    {
        $title=$this->JTextExtended($row->title);
        $j=(array)json_decode($row->params);

		$src=$j['menu_image'];

				if($src!="")
				{

					if($imagereplacer!="")
					{
						$pair=explode(',',$imagereplacer);
						if(count($pair)==2)
						{
							$new_src=str_replace($pair[0],$pair[1],$src);

							$imagefile=JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$new_src);

							if(file_exists($imagefile))
								$src=$new_src;
						}
					}
				}



		$link=JRoute::_($row->link.'&Itemid='.$row->id);

		if($src=="")
		{
			if($addlink)
				return '<a href="'.$link.'" title="'.$title.'"'.($cssstyle!='' ? ' style="'.$cssstyle.'"' : '').'><div><button class="btn button btn-primary">'.$title.'</button></div></a>';
			else
				return '<span>'.$title.'</span>';
		}
		else
		{
			$img='<img src="'.($this->addslash ? '/' : '').$src.'" title="'.$title.'" alt="'.$title.'" />';

			if($addlink)
				return '<a href="'.$link.'" title="'.$title.'"'.($cssstyle!='' ? ' style="'.$cssstyle.'"' : '').'><div><button class="btn button btn-primary">'.$img.'<span>'.$title.'</span></button></div></a>';
			else
				return $img;
		}

    }
	elseif($valueoption=='linkandtitle')
	{
		$title=$this->JTextExtended($row->title);

	    if($showactivelink=='showinactive')
	    {
		if($jinput->getInt ('Itemid', 0)==$row->id)
		    return $title;
	    }
	    $link=JRoute::_($row->link.'&Itemid='.$row->id);

		if($addlink)
			return '<span>'.$title.'</span>';
		else
			return '<a href="'.$link.'" title="'.$title.'"'.($cssstyle!='' ? ' style="'.$cssstyle.'"' : '').'><span>'.$title.'</span></a>';
	}
	elseif($valueoption=='encodedlink')
		return urlencode(JRoute::_($row->link.'&Itemid='.$row->id));

}


function LOAmakeMenuListLinks ($rows, $count, $showactivelink, $cssstyle)
{
	return (
			$count==count($rows)
			?
				''
			:
				$this->LOAmakeMenuListLink ($rows[$count],$showactivelink, $cssstyle).$this->LOAmakeMenuListLinks ($rows, $count+1, $showactivelink, $cssstyle)
		);
}
function LOAmakeMenuList($rows,$showactivelink, $cssstyle)
{
	return (

			($this->menucssclass ? '<div class="'.$this->menucssclass.'">' : '')
			.'<ul>'
			.$this->LOAmakeMenuListLinks ($rows, 0, $showactivelink, $cssstyle)
			.'</ul>'
			.($this->menucssclass ? '</div>' : '')
			)
	;

}



function LOAmakeMenuListCol ($rows, $cols, $start, $count, $showactivelink, $cssstyle)
{

	if(($count+$start == count ( $rows ) )	||	( $count==$cols ) ) return '';


	$result='<td>';

	$result.=$this->LOAmakeMenuListLink_forTable ($rows[$count+$start], $showactivelink, $cssstyle).'</td>';


	$result_=$this->LOAmakeMenuListCol ($rows, $cols, $start, $count+1, $showactivelink, $cssstyle);
	if($result_=='' and $cols-$count-1>0)
		$result.=str_repeat ('<td></td>', $cols-$count-1);
	else
		$result.=$result_;

	return $result;
}
function LOAmakeMenuListRow ($rows, $cols, $count, $showactivelink, $cssstyle)
{
	if($count >= count ( $rows ))
		return '';
	else
		return  '<tr>'.$this->LOAmakeMenuListCol ($rows, $cols, $count, 0,$showactivelink, $cssstyle).'</tr>'.$this->LOAmakeMenuListRow ($rows, $cols, $count + $cols, $showactivelink, $cssstyle);

}


function LOAmakeMenuListTable ($rows, $cols, $showactivelink, $cssstyle)
{
	return (

			($this->menucssclass ? '<div class="'.$this->menucssclass.'">' : '')
			.'<table><tbody>'
			.$this->LOAmakeMenuListRow ($rows, $cols, 0, $showactivelink, $cssstyle)



			.'</tbody></table>'
			.($this->menucssclass ? '</div>' : '')
			)
	;
}


//Builds SQL
function buildSelect ( $Mode ) { return ( 'SELECT '.$this->retVals( COLS, $Mode ) ) ; }
function buildFrom ( $Mode ) { return ( ' FROM #__'.$this->retVals ( DB, $Mode ) ) ; }

function buildOrder ( $Mode, $orderby_field)
{
	if($orderby_field=='')
		$orderby_field=$this->retVals ( ORDER, $Mode );

	$orderby_field=strtolower($orderby_field);

	$isDesc=true;
	if(strpos($orderby_field,' desc')===false)
		$isDesc=false;
	else
		$orderby_field=str_replace(' desc','',$orderby_field);


	if($Mode==ARTICLELIST)
	{
            $parts=explode(':',$orderby_field);//to separate from posible parameters, like date format

			$fieldlist=array('title','created','modified','publish_up','ordering','metadata','metakey','featured','language','hits');

			if(!in_array($parts[0],$fieldlist))
				$orderby_field='ordering';

			$orderby_field='#__content.'.$orderby_field;

	}
	else
	{
			$fieldlist=array('title','lft','rtg','language');
			if(!in_array($orderby_field,$fieldlist))
				$orderby_field='lft';

			$orderby_field='#__menu.'.$orderby_field;
	}

	if($isDesc)
		$orderby_field=$orderby_field.' DESC ';

	return ( ' ORDER BY '.$orderby_field ) ;
}

function buildQuery ( $Search, $Mode, $orderby_field, $showactivelink, $excludelist,$Recursive)
{


		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($this->retVals(COLS, $Mode ));
		$query->from('#__'.$this->retVals ( DB, $Mode ));

		if($Mode == ARTICLELIST)
		{
			//For MySQL
			$query->join('LEFT', '#__menu ON INSTR(`link`, CONCAT("index.php?option=com_content&view=article&id=",#__content.id)) ' );
		}

		$query->where($this->buildSearch ( $Search, $Mode, $showactivelink, $excludelist,$Recursive ));



	if($Mode==ARTICLELIST)
			$query.=' GROUP BY #__content.id';


	$query .=	$this->buildOrder ( $Mode, $orderby_field );


	return $query;
}


function buildSearch ( $Search, $Mode, $showactivelink, $excludelist, $Recursive)
{
	$jinput = JFactory::getApplication()->input;

	$db = JFactory::getDBO();

	$where=array();

	if($Mode == MENULIST)
	{
		if($Search=='%')
			$Search='' ;

		$where[]='published=1';

		$where[]=$this->retVals( SEARCH,$Mode ).$this->retSearch ($Search );

        if(!$Recursive)
		{
			$where[]='parent_id=1';
		}

		    $langObj=JFactory::getLanguage();
		    $nowLang=$langObj->getTag();

			$where[]='(language="*" OR language="'.$nowLang.'")';
			$where[]='parent_id!=0';


		if($showactivelink=='' or $showactivelink=='no' or $showactivelink=='hide')
			$where[]='id!='.$jinput->getInt ('Itemid',0);

		if(count($excludelist))
		{
			foreach($excludelist as $excludeitem)
				$where[]='id!='.(int)$excludeitem;
		}
		$w=array();


		$user = JFactory::getUser();

      $userid = $user->get('id');
		if($userid==0)
		{
			$w[]='access=1';
		}
		else
		{
			$groups = JAccess::getGroupsByUser($userid);


			foreach($groups as $group)
				$w[]='access in (SELECT id FROM #__viewlevels WHERE FIND_IN_SET("'.$group.'",rules )
	OR INSTR(rules,"['.$group.',")
	OR INSTR(rules,",'.$group.']")
	OR INSTR(rules,"['.$group.']")
				)';
		}

		$where[]='('.implode(' OR ',$w).')';

		$where[]="!INSTR(params,'\"menu_show\":0')";

	}
	else
	{

		if($showactivelink=='' or $showactivelink=='no' or $showactivelink=='hide')
			$where[]='#__content.id!='.$jinput->getInt ('id',0);

	        $langObj=JFactory::getLanguage();
			$nowLang=$langObj->getTag();

			// Filter by start and end dates.
			$nullDate = $db->Quote($db->getNullDate());
			$date = JFactory::getDate();
			$nowDate = $db->Quote($date->toSql());

			$where[]='(#__content.language="*" OR #__content.language="'.$nowLang.'")';
			$where[]='#__content.state=1';
			$where[]='(#__content.publish_up = ' . $nullDate . ' OR #__content.publish_up <= ' . $nowDate . ')';
			$where[]='(#__content.publish_down = ' . $nullDate . ' OR #__content.publish_down >= ' . $nowDate . ')';




		if($Recursive)
		{
			$w=$this->getWhere(true,(int)$Search);
			if($w!='()')
				$where[]=$w;
		}
		else
		{
			$w=$this->retVals ( SEARCH,$Mode ).$this->retSearch ($Search );
			if($w!='')
				$where[]=$w;
		}

		if(count($excludelist))
		{
			foreach($excludelist as $excludeitem)
				$where[]='#__content.id!='.(int)$excludeitem;
		}

	}

		$where_str=implode(' AND ' , $where);
		return $where_str;
}




function LOAgetRows ( $Search, $Mode, $orderby_field, $startindex, $limit, $showactivelink, $excludelist,$Recursive)
{

	if($Recursive=='true')
        $Recursive=true;
    else
        $Recursive=false;



	$langObj=JFactory::getLanguage();
	$nowLang=$langObj->getTag();
	$db = JFactory::getDBO();
	if($startindex>0 and (int)$limit<1)
		$limit='99999999999999999';

	$query=$this->buildQuery ( $Search, $Mode, $orderby_field, $showactivelink, $excludelist,$Recursive);

		if($limit>0 and $startindex>0)
			$db->setQuery($query, $startindex, $limit);
		elseif($limit>0)
			$db->setQuery($query, 0, $limit);
		elseif($startindex>0)
			$db->setQuery($query, $startindex);
		elseif($startindex==0 && $limit ==0)
			$db->setQuery($query);



	if (!$db->query())
		die ( $db->stderr());

	$rows=$db->loadObjectList();



	return ( $rows );
}


function Length ( $Text, $Match, $Offset, $Mode ) {	$ret = $this->Find ( $Text, $Match, $Offset, $Mode ) ;	return ( $ret !== -1 ? $ret - $Offset + 2 : $ret ) ; }
function SplitOptions ( $Text, $PreTxt ) { return ( $this->MidStr ( $Text, strlen ( $PreTxt ) - 1, strlen ( $Text ) -1 ) ) ; }

//Various string functions equivalent to C++ Left, Right and Mid string functions
function LeftStr ( $str, $end ) { return ( $this->MidStr ( $str, 0, $end ) ) ; }
function RightStr ( $str, $start ) { return ( $this->MidStr ( $str, $start, strlen ( $str ) ) ) ; }
function MidStr ( $str, $start, $end ) { return ( substr ( $str, $start, $end-$start ) ) ; }

function LOAgetListToReplace($par,&$options,&$text)
{
		$fList=array();
		$l=strlen($par)+2;

		$offset=0;
		do{
			if($offset>=strlen($text))
				break;

			$ps=strpos($text, '{'.$par.'=', $offset);
			if($ps===false)
				break;


			if($ps+$l>=strlen($text))
				break;

		$pe=strpos($text, '}', $ps+$l);

		if($pe===false)
			break;

		$notestr=substr($text,$ps,$pe-$ps+1);

			$options[]=substr($text,$ps+$l,$pe-$ps-$l);
			$fList[]=$notestr;


		$offset=$ps+$l;


		}while(!($pe===false));

		return $fList;
}


function LOAcsv_explode($delim=',', $str, $enclose='"', $preserve=false)
{
		$resArr = array();
		$n = 0;
		$expEncArr = explode($enclose, $str);
		foreach($expEncArr as $EncItem)
		{
			if($n++%2){
				array_push($resArr, array_pop($resArr) . ($preserve?$enclose:'') . $EncItem.($preserve?$enclose:''));
			}else{
				$expDelArr = explode($delim, $EncItem);
				array_push($resArr, array_pop($resArr) . array_shift($expDelArr));
			    $resArr = array_merge($resArr, $expDelArr);
			}
		}
	return $resArr;
}

function getMenuParam($param, $rawparams)
{
			if(strlen($rawparams)<8)
				return '';

			$rawparams=substr($rawparams,1,strlen($rawparams)-2);


			$paramslist=$this->LOAcsv_explode(',', $rawparams,'"', true);

			foreach($paramslist as $pl)
			{

				$pair=$this->LOAcsv_explode(':', $pl,'"', false);
				if($pair[0]==$param)
					return $pair[1];
			}

		return '';

}



	function getWhere($recursive,$catid)
	{
		$where=array();

		if($recursive)
		{
			$cat_list=$this->getCategoriesRecursive($catid);
			foreach($cat_list as $c)
			{
				$where[]='#__content.catid='.$c['id'];
			}
		}
		else
		{
			$where[]='#__content.catid='.$catid; //Parent Category
		}


		$where_query='('.implode(' OR ',$where).')';


		return $where_query;
	}

	function getCategoriesRecursive($catid,$add_parent=true)
	{
		$cat_list=array();

		$db = JFactory::getDBO();


			$query = $db->getQuery(true);
	                $query->select('`id`, `parent_id`, `title`');
	                $query->from('#__categories');
	                $query->where('`extension`="com_content"');

			if ($add_parent)
				$query->where('(`parent_id`='.$catid.' or `id`='.$catid.')');
			else
				$query->where('`parent_id`='.$catid);

			$query->order('`title`');

                $db->setQuery((string)$query);
                $recs = $db->loadObjectList();


		foreach($recs as $c)
		{

				if($c->id!=$catid)
				{
					$cat_list[]=array('id'=>$c->id, 'parent_id'=>$c->parent_id, 'title'=>$c->title);
					$kids=$this->getCategoriesRecursive($c->id,false);
					if(count($kids)!=0)
					{
						$cat_list=array_merge($cat_list,$kids);
					}

				}
				elseif($add_parent)
					$cat_list[]=array('id'=>$c->id, 'parent_id'=>$c->parent_id, 'title'=>$c->title);

		}
		return $cat_list;

	}







    function JTextExtended($text)
    {
        $new_text=JText::_($text);
        if($new_text==$text)
        {
            $parts=explode('_',$text);
            if(count($parts)>1)
            {
                $type=$parts[0];
                if($type=='PLG' and count($parts)>2)
                {
                    $extension=strtolower($parts[0].'_'.$parts[1].'_'.$parts[2]);
                }
                else
                    $extension=strtolower($parts[0].'_'.$parts[1]);

                $lang = JFactory::getLanguage();
                $lang->load($extension,JPATH_BASE);

                return JText::_($text);
			}
            else
                return $text;
        }
        else
            return $new_text;

    }

}//class



}//if(!method_exists(__CLASS__, 'ListofArticles')){
