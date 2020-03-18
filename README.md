# list-of-articles
A Joomla plugin to show the list of articles in selected category or menu items.

Enable plugin first !!!

{articlesofcategory=CATEGORY ID, |COLUMNS|, |START|, |LIMIT|, |ORDER BY|,|SHOW/HIDE Active Items|,|Separator|,|Value Field|,|Exclude ID list|,|CSS Style|,|Recursive|,|Orientation|}

Parameters

    Category - ID of category, example: 4
    Columns - Show results as a table with number of columns, values: 1 - Simple List, 2-9 Table grid, 0 a list separated by a character (set separator). example: 3
    Start - start index (skip # of articles) default "0", example: 2
    Limit - number of articles, example: 5
    Order By - order by field, possible values: "title"(default), "modified", "publish_up", "ordering", "hits", "featured", "language", "metakey", "metadata", "lft" (same as ordering but for "menuitemsoftype")
    SHOW/HIDE Active Items, values "show"(default), "hide"
    Separator - a character to separate the list, works only when number of columns set to "0"
    Value Field - show value of selected field, possible values: "title", "link", "encodedlink", "innertext", "fulltext", "modified", "publish_up", "hits", "metakey", "metadata". Works only when number of columns set to "0"
    Exclude ID list - Articles to exclude from the list. List of article IDs separated by comma. Example: 5,3,8
    CSS Style [PRO]. Example: color:#ff0000;margin: 5px;
    Recursive [PRO] - Show article from sub-category, values: "true", "false"(default)
    Orientation [PRO]- Table orientation, values: "horizontal"(default), "vertical". Works only when number of columns set between 2 and 9
    Menu Item image file name replaceer [PRO]- two value separated by com. Example: "32a,64b". This will replace "32a" with "64b" in Menu Item image file name: "about-us32a.png" will be replaced with "about-us64b.png"

example (articles of category Id=11, in 1 column, skip 0 articles and show all article, 0 means - no limit, order by "title" in descending order, hide active links, also exclude article with id=17):

CSS Style parameter and Recursive are available in PRO version only.

{articlesofcategory=11,1,0,0,title desc,hide,,,17}