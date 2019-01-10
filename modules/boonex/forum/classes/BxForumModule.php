<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Forum Forum
 * @ingroup     UnaModules
 *
 * @{
 */

class BxForumModule extends BxBaseModTextModule
{
    function __construct(&$aModule)
    {
        parent::__construct($aModule);
    }

    public function sortParticipants ($aParticipants, $iProfileIdLastComment, $iProfileIdAuthor, $iProfileIdCurrent = 0)
    {
        if (!$iProfileIdCurrent)
            $iProfileIdCurrent = bx_get_logged_profile_id();

        $aMoveUp = array($iProfileIdCurrent, $iProfileIdLastComment, $iProfileIdAuthor);

        asort($aParticipants, SORT_NUMERIC);

        foreach ($aMoveUp as $iProfileId) {
            if (!isset($aParticipants[$iProfileId]))
                continue;

            $a = array($iProfileId => $aParticipants[$iProfileId]);
            unset($aParticipants[$iProfileId]);
            $aParticipants = $a + $aParticipants;
        }

        return $aParticipants;
    }

    /**
     * Action methods
     */
    public function actionUpdateStatus($sAction = '', $iContentId = 0)
    {
    	if(empty($sAction) && bx_get('action') !== false)
   			$sAction = bx_process_input(bx_get('action'));

		if(empty($iContentId) && bx_get('id') !== false)
    		$iContentId = (int)bx_get('id');

    	$aContentInfo = $this->_oDb->getContentInfoById($iContentId);
        if(empty($aContentInfo) || !is_array($aContentInfo))
			return echoJson(array('code' => 1, 'message' => _t('_bx_forum_err_not_found')));

		$sMethodCheck = 'checkAllowed' . bx_gen_method_name($sAction) . 'AnyEntry';
		$sResult = $this->$sMethodCheck($aContentInfo);
        if($sResult !== CHECK_ACTION_RESULT_ALLOWED)
        	return echoJson(array('code' => 2, 'message' => $sResult));

        if(!$this->_oDb->updateStatus($sAction, $aContentInfo))
        	return echoJson(array('code' => 3, 'message' => _t('_error occured')));

    	echoJson(array('code' => 0, 'id' => $iContentId, 'reload' => 1));
    }

    public function actionAjaxGetAuthors()
    {
        $aResult = BxDolService::call('system', 'profiles_search', array(bx_get('term')), 'TemplServiceProfiles');

        header('Content-Type:text/javascript; charset=utf-8');
        echo json_encode($aResult);
    }

    /**
     * Service methods
     */
    /**
     * @page service Service Calls
     * @section bx_forum Discussions
     * @subsection bx_forum-other Other
     * @subsubsection bx_forum-get_info get_info
     * 
     * @code bx_srv('bx_forum', 'get_info', [...]); @endcode
     * 
     * Get content info by content ID. Is used in "Content Info Objects" system.
     * 
     * @param $iContentId integer value with content ID.
     * @param $bSearchableFieldsOnly (optional) boolean value determining all info or "searchable fields" only will be returned.
     * @return an array with content info. Empty array is returned if something is wrong.
     * 
     * @see BxForumModule::serviceGetInfo
     */
    /** 
     * @ref bx_forum-get_info "get_info"
     */
    public function serviceGetInfo ($iContentId, $bSearchableFieldsOnly = true)
    {
        $aContentInfo = $this->_getFields($iContentId);
        if(empty($aContentInfo))
            return array();

        return $aContentInfo;
    }

    /**
     * @page service Service Calls
     * @section bx_forum Discussions
     * @subsection bx_forum-page_blocks Page Blocks
     * @subsubsection bx_forum-browse_new browse_new
     * 
     * @code bx_srv('bx_forum', 'browse_new', [...]); @endcode
     * 
     * Get page block with a list of items ordered by newness and represented as table.
     * 
     * @param $sUnitView (optional) string with unit view type.
     * @param $bEmptyMessage (optional) boolean value determining whether an "Empty" message should be returned or not.
     * @param $bAjaxPaginate (optional) boolean value determining whether an Ajax based pagination should be used or not.
     * @return HTML string with block content to display on the site or false if something is wrong. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxForumModule::serviceBrowseNew
     */
    /** 
     * @ref bx_forum-browse_new "browse_new"
     */
	public function serviceBrowseNew ($sUnitView = false, $bEmptyMessage = true, $bAjaxPaginate = true)
    {
    	$sType = 'new';

    	if($sUnitView != 'table')   
        	return $this->_serviceBrowse($sType, $sUnitView ? array('unit_view' => $sUnitView) : false, BX_DB_PADDING_DEF, $bEmptyMessage, $bAjaxPaginate);

        return $this->_serviceBrowseTable(array('type' => $sType));
    }

    /**
     * @page service Service Calls
     * @section bx_forum Discussions
     * @subsection bx_forum-page_blocks Page Blocks
     * @subsubsection bx_forum-browse_latest browse_latest
     * 
     * @code bx_srv('bx_forum', 'browse_latest', [...]); @endcode
     * 
     * Get page block with a list of items ordered by Recent Posts in them and represented as table.
     * 
     * @param $sUnitView (optional) string with unit view type.
     * @param $bEmptyMessage (optional) boolean value determining whether an "Empty" message should be returned or not.
     * @param $bAjaxPaginate (optional) boolean value determining whether an Ajax based pagination should be used or not.
     * @param $bShowHeader (optional) boolean value determining whether a resulting table should have Header section or not.
     * @return HTML string with block content to display on the site or false if something is wrong. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxForumModule::serviceBrowseNew
     */
    /** 
     * @ref bx_forum-browse_latest "browse_latest"
     */
	public function serviceBrowseLatest($sUnitView = false, $bEmptyMessage = true, $bAjaxPaginate = true, $bShowHeader = true)
    {
    	$sType = 'latest';

    	if($sUnitView != 'table')
        	return $this->_serviceBrowse($sType, $sUnitView ? array('unit_view' => $sUnitView) : false, BX_DB_PADDING_DEF, $bEmptyMessage, $bAjaxPaginate);

		return $this->_serviceBrowseTable(array('type' => $sType), $bShowHeader);
    }

    /**
     * @page service Service Calls
     * @section bx_forum Discussions
     * @subsection bx_forum-page_blocks Page Blocks
     * @subsubsection bx_forum-browse_featured browse_featured
     * 
     * @code bx_srv('bx_forum', 'browse_featured', [...]); @endcode
     * 
     * Get page block with a list of featured items represented as table.
     * 
     * @param $sUnitView (optional) string with unit view type.
     * @param $bEmptyMessage (optional) boolean value determining whether an "Empty" message should be returned or not.
     * @param $bAjaxPaginate (optional) boolean value determining whether an Ajax based pagination should be used or not.
     * @param $bShowHeader (optional) boolean value determining whether a resulting table should have Header section or not.
     * @return HTML string with block content to display on the site or false if something is wrong. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxForumModule::serviceBrowseFeatured
     */
    /** 
     * @ref bx_forum-browse_featured "browse_featured"
     */
    public function serviceBrowseFeatured($sUnitView = false, $bEmptyMessage = false, $bAjaxPaginate = true, $bShowHeader = true)
    {
        $CNF = &$this->_oConfig->CNF;

    	$sType = 'featured';

    	if($sUnitView != 'table')
        	return $this->_serviceBrowse($sType, $sUnitView ? array('unit_view' => $sUnitView) : false, BX_DB_PADDING_DEF, $bEmptyMessage, $bAjaxPaginate);

		return $this->_serviceBrowseTable(array(
			'grid' => $CNF['OBJECT_GRID_FEATURE'],
			'type' => $sType, 
			'where' => array('fld' => 'featured', 'val' => 0, 'opr' => '<>'),
		    'empty_message' => $bEmptyMessage
		), $bShowHeader);
    }

    /**
     * @page service Service Calls
     * @section bx_forum Discussions
     * @subsection bx_forum-page_blocks Page Blocks
     * @subsubsection bx_forum-browse_top browse_top
     * 
     * @code bx_srv('bx_forum', 'browse_top', [...]); @endcode
     * 
     * Get page block with a list of items ordered by a number of comments and represented as table.
     * 
     * @param $sUnitView (optional) string with unit view type.
     * @param $bEmptyMessage (optional) boolean value determining whether an "Empty" message should be returned or not.
     * @param $bAjaxPaginate (optional) boolean value determining whether an Ajax based pagination should be used or not.
     * @return HTML string with block content to display on the site or false if something is wrong. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxForumModule::serviceBrowseTop
     */
    /** 
     * @ref bx_forum-browse_top "browse_top"
     */
	public function serviceBrowseTop($sUnitView = false, $bEmptyMessage = true, $bAjaxPaginate = true)
    {
    	$sType = 'top';

    	if($sUnitView != 'table')
        	return $this->_serviceBrowse($sType, $sUnitView ? array('unit_view' => $sUnitView) : false, BX_DB_PADDING_DEF, $bEmptyMessage, $bAjaxPaginate);

		return $this->_serviceBrowseTable(array('type' => $sType));
    }

    /**
     * @page service Service Calls
     * @section bx_forum Discussions
     * @subsection bx_forum-page_blocks Page Blocks
     * @subsubsection bx_forum-browse_popular browse_popular
     * 
     * @code bx_srv('bx_forum', 'browse_popular', [...]); @endcode
     * 
     * Get page block with a list of items ordered by a number of views and represented as table.
     * 
     * @param $sUnitView (optional) string with unit view type.
     * @param $bEmptyMessage (optional) boolean value determining whether an "Empty" message should be returned or not.
     * @param $bAjaxPaginate (optional) boolean value determining whether an Ajax based pagination should be used or not.
     * @return HTML string with block content to display on the site or false if something is wrong. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxForumModule::serviceBrowsePopular
     */
    /** 
     * @ref bx_forum-browse_popular "browse_popular"
     */
    public function serviceBrowsePopular ($sUnitView = false, $bEmptyMessage = true, $bAjaxPaginate = true)
    {
        $sType = 'popular';

        if($sUnitView != 'table')
            $this->_serviceBrowse($sType, $sUnitView ? array('unit_view' => $sUnitView) : false, BX_DB_PADDING_DEF, $bEmptyMessage, $bAjaxPaginate);

        return $this->_serviceBrowseTable(array('type' => $sType));
    }

    /**
     * @page service Service Calls
     * @section bx_forum Discussions
     * @subsection bx_forum-page_blocks Page Blocks
     * @subsubsection bx_forum-browse_updated browse_updated
     * 
     * @code bx_srv('bx_forum', 'browse_updated', [...]); @endcode
     * 
     * Get page block with a list of updated items represented as table.
     * 
     * @param $sUnitView (optional) string with unit view type.
     * @param $bEmptyMessage (optional) boolean value determining whether an "Empty" message should be returned or not.
     * @param $bAjaxPaginate (optional) boolean value determining whether an Ajax based pagination should be used or not.
     * @return HTML string with block content to display on the site or false if something is wrong. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxForumModule::serviceBrowseUpdated
     */
    /** 
     * @ref bx_forum-browse_updated "browse_updated"
     */
    public function serviceBrowseUpdated ($sUnitView = false, $bEmptyMessage = true, $bAjaxPaginate = true)
    {
        $sType = 'updated';

        if($sUnitView != 'table')
            $this->_serviceBrowse($sType, $sUnitView ? array('unit_view' => $sUnitView) : false, BX_DB_PADDING_DEF, $bEmptyMessage, $bAjaxPaginate);

        return $this->_serviceBrowseTable(array('type' => $sType));
    }

    /**
     * @page service Service Calls
     * @section bx_forum Discussions
     * @subsection bx_forum-page_blocks Page Blocks
     * @subsubsection bx_forum-browse_index browse_index
     * 
     * @code bx_srv('bx_forum', 'browse_index', [...]); @endcode
     * 
     * Get page block for Site's Home page with a list of items ordered by newness and represented as table.
     * 
     * @param $sUnitView (optional) string with unit view type.
     * @param $bEmptyMessage (optional) boolean value determining whether an "Empty" message should be returned or not.
     * @param $bAjaxPaginate (optional) boolean value determining whether an Ajax based pagination should be used or not.
     * @param $bShowHeader (optional) boolean value determining whether a resulting table should have Header section or not.
     * @return HTML string with block content to display on the site or false if something is wrong. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxForumModule::serviceBrowseIndex
     */
    /** 
     * @ref bx_forum-browse_index "browse_index"
     */
	public function serviceBrowseIndex($sUnitView = false, $bEmptyMessage = true, $bAjaxPaginate = true, $bShowHeader = true)
    {
    	$sType = 'index';

    	if($sUnitView != 'table')
        	return $this->_serviceBrowse($sType, $sUnitView ? array('unit_view' => $sUnitView) : false, BX_DB_PADDING_DEF, $bEmptyMessage, $bAjaxPaginate);

		return $this->_serviceBrowseTable(array(
			'type' => $sType,
			'per_page' => (int)$this->_oDb->getParam('bx_forum_per_page_index'),
			'empty_message' => $bEmptyMessage
		), $bShowHeader);
    }

    /**
     * @page service Service Calls
     * @section bx_forum Discussions
     * @subsection bx_forum-page_blocks Page Blocks
     * @subsubsection bx_forum-browse_author browse_author
     * 
     * @code bx_srv('bx_forum', 'browse_author', [...]); @endcode
     * 
     * Get page block with a list of items filtered by Author and represented as table.
     * 
     * @param $iProfileId (optional) integer value with author ID. If empty value is provided, an attempt to get it from GET/POST arrays will be performed.
     * @param $aParams (optional) an array of additional params. It's not used for now.
     * @return HTML string with block content to display on the site or false if something is wrong. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxForumModule::serviceBrowseAuthor
     */
    /** 
     * @ref bx_forum-browse_author "browse_author"
     */
	public function serviceBrowseAuthor ($iProfileId = 0, $aParams = array())
    {
        if(!$iProfileId)
            $iProfileId = bx_process_input(bx_get('profile_id'), BX_DATA_INT);

        if(!$iProfileId)
            return '';

        $bEmptyMessage = true;
        if(isset($aParams['empty_message'])) {
            $bEmptyMessage = (bool)$aParams['empty_message'];
            unset($aParams['empty_message']);
        }

        return $this->_serviceBrowseTable(array(
        	'type' => 'author', 
        	'author' => $iProfileId, 
        	'where' => array('fld' => 'author', 'val' => $iProfileId, 'opr' => '='), 
        	'per_page' => (int)$this->_oDb->getParam('bx_forum_per_page_profile'),
        	'empty_message' => $bEmptyMessage
        ), false);
    }

    /**
     * @page service Service Calls
     * @section bx_forum Discussions
     * @subsection bx_forum-page_blocks Page Blocks
     * @subsubsection bx_forum-browse_favorite browse_favorite
     * 
     * @code bx_srv('bx_forum', 'browse_favorite', [...]); @endcode
     * 
     * Get page block with a list of favorited items by some profile and represented as table.
     * 
     * @param $iProfileId (optional) integer value with profile ID. If empty value is provided, an attempt to get it from GET/POST arrays will be performed.
     * @param $aParams (optional) an array of additional params. It's not used for now.
     * @return HTML string with block content to display on the site or false if something is wrong. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxForumModule::serviceBrowseFavorite
     */
    /** 
     * @ref bx_forum-browse_favorite "browse_favorite"
     */
    public function serviceBrowseFavorite ($iProfileId = 0, $aParams = array())
    {
        $CNF = &$this->_oConfig->CNF;

        $oProfile = null;
        if((int)$iProfileId)
            $oProfile = BxDolProfile::getInstance($iProfileId);
        if(!$oProfile && bx_get('profile_id') !== false)
            $oProfile = BxDolProfile:: getInstance(bx_process_input(bx_get('profile_id'), BX_DATA_INT));
        if(!$oProfile)
            $oProfile = BxDolProfile::getInstance();
        if(!$oProfile)
            return '';

        $iProfileAuthor = $oProfile->id();
        $oFavorite = $this->getObjectFavorite();
        if(!$oFavorite->isPublic() && $iProfileAuthor != bx_get_logged_profile_id())
            return '';

        $bEmptyMessage = false;
        if(isset($aParams['empty_message'])) {
            $bEmptyMessage = (bool)$aParams['empty_message'];
            unset($aParams['empty_message']);
        }

        $aConditions = $oFavorite->getConditionsTrack($CNF['TABLE_ENTRIES'], 'id', $iProfileAuthor);
        if(empty($aConditions) || !is_array($aConditions)) 
            return '';

        $aJoinGroup = array('grp' => true, 'cnds' => array());
        if(!empty($aConditions['join']))
            foreach($aConditions['join'] as $aCondition)
                $aJoinGroup['cnds'][] = array(
                    'tp' => $aCondition['type'],
                    'tbl1' => $aCondition['table'],
                    'fld1' => $aCondition['onField'],
                    'tbl2' => $aCondition['mainTable'],
                	'fld2' => $aCondition['mainField']
                );

        $aWhereGroup = array('grp' => true, 'opr' => 'AND', 'cnds' => array());
        if(!empty($aConditions['restriction']))
            foreach($aConditions['restriction'] as $aCondition)
                $aWhereGroup['cnds'][] = array(
                	'tbl' => (!empty($aCondition['table']) ? $aCondition['table'] : ''), 
                	'fld' => $aCondition['field'], 
                	'val' => $aCondition['value'], 
                	'opr' => $aCondition['operator']
                );

        return $this->_serviceBrowseTable(array(
			'grid' => $CNF['OBJECT_GRID_FAVORITE'],
        	'type' => 'favorite', 
        	'author' => $iProfileId, 
            'join' => $aJoinGroup,
        	'where' => $aWhereGroup, 
        	'per_page' => (int)$this->_oDb->getParam('bx_forum_per_page_profile'),
        	'empty_message' => $bEmptyMessage
        ), false);
    }

    /**
     * @page service Service Calls
     * @section bx_forum Discussions
     * @subsection bx_forum-page_blocks Page Blocks
     * @subsubsection bx_forum-browse_category browse_category
     * 
     * @code bx_srv('bx_forum', 'browse_category', [...]); @endcode
     * 
     * Get page block with a list of items filter by some category and represented as table.
     * 
     * @param $sUnitView (optional) string with unit view type.
     * @param $bEmptyMessage (optional) boolean value determining whether an "Empty" message should be returned or not.
     * @param $bAjaxPaginate (optional) boolean value determining whether an Ajax based pagination should be used or not.
     * @return HTML string with block content to display on the site or false if something is wrong. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxForumModule::serviceBrowseCategory
     */
    /** 
     * @ref bx_forum-browse_category "browse_category"
     */
    public function serviceBrowseCategory($sUnitView = false, $bEmptyMessage = true, $bAjaxPaginate = true)
    {
    	$sType = 'category';
    	$iCategory = bx_process_input(bx_get('category'), BX_DATA_INT);

    	$aCategory = $this->_oDb->getCategories(array('type' => 'by_category', 'category' => $iCategory));
    	if(!empty($aCategory['visible_for_levels']) && !BxDolAcl::getInstance()->isMemberLevelInSet($aCategory['visible_for_levels']))
    		return $bEmptyMessage ? MsgBox(_t('_sys_txt_access_denied')) : '';

    	if($sUnitView != 'table')   
        	return $this->_serviceBrowse($sType, $sUnitView ? array('unit_view' => $sUnitView) : false, BX_DB_PADDING_DEF, $bEmptyMessage, $bAjaxPaginate);

		return $this->_serviceBrowseTable(array('type' => $sType, 'where' => array('fld' => 'cat', 'val' => $iCategory, 'opr' => '=')));
    }

    /**
     * @page service Service Calls
     * @section bx_forum Discussions
     * @subsection bx_forum-page_blocks Page Blocks
     * @subsubsection bx_forum-browse_keyword browse_keyword
     * 
     * @code bx_srv('bx_forum', 'browse_keyword', [...]); @endcode
     * 
     * Get page block with a list of items filter by some keyword and represented as table.
     * 
     * @param $sUnitView (optional) string with unit view type.
     * @param $bEmptyMessage (optional) boolean value determining whether an "Empty" message should be returned or not.
     * @param $bAjaxPaginate (optional) boolean value determining whether an Ajax based pagination should be used or not.
     * @return HTML string with block content to display on the site or false if something is wrong. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxForumModule::serviceBrowseKeyword
     */
    /** 
     * @ref bx_forum-browse_keyword "browse_keyword"
     */
	public function serviceBrowseKeyword($sUnitView = false, $bEmptyMessage = true, $bAjaxPaginate = true)
    {
    	$sType = 'keyword';
    	$sKeyword = bx_process_input(bx_get('keyword'));

    	if($sUnitView != 'table')   
        	return $this->_serviceBrowse($sType, $sUnitView ? array('unit_view' => $sUnitView) : false, BX_DB_PADDING_DEF, $bEmptyMessage, $bAjaxPaginate);

		return $this->_serviceBrowseTable(array('type' => $sType, 'where' => $this->_getSearchKeywordDescriptor('#' . $sKeyword)));
    }

    /**
     * @page service Service Calls
     * @section bx_forum Discussions
     * @subsection bx_forum-page_blocks Page Blocks
     * @subsubsection bx_forum-browse_search_results browse_search_results
     * 
     * @code bx_srv('bx_forum', 'browse_search_results', [...]); @endcode
     * 
     * Get page block with search results by Author(s), Category, Keyword represented as table.
     * 
     * @param $sUnitView (optional) string with unit view type.
     * @param $bEmptyMessage (optional) boolean value determining whether an "Empty" message should be returned or not.
     * @param $bAjaxPaginate (optional) boolean value determining whether an Ajax based pagination should be used or not.
     * @return HTML string with block content to display on the site or false if something is wrong. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxForumModule::serviceBrowseSearchResults
     */
    /** 
     * @ref bx_forum-browse_search_results "browse_search_results"
     */
    public function serviceBrowseSearchResults($sUnitView = false, $bEmptyMessage = true, $bAjaxPaginate = true)
    {
    	$sType = 'search';

    	$aAuthors = bx_process_input(bx_get('author'));
    	$iCategory = bx_process_input(bx_get('category'), BX_DATA_INT);
    	$sKeyword = bx_process_input(bx_get('keyword'));

    	$aCategory = $this->_oDb->getCategories(array('type' => 'by_category', 'category' => $iCategory));
    	if(!empty($aCategory['visible_for_levels']) && !BxDolAcl::getInstance()->isMemberLevelInSet($aCategory['visible_for_levels']))
    		return $bEmptyMessage ? MsgBox(_t('_sys_txt_access_denied')) : '';

    	if($sUnitView != 'table')   
        	return $this->_serviceBrowse('', $sUnitView ? array('unit_view' => $sUnitView) : false, BX_DB_PADDING_DEF, $bEmptyMessage, $bAjaxPaginate);

        $aWhereGroupAnd = array('grp' => true, 'opr' => 'AND', 'cnds' => array());
        if(!empty($aAuthors) && is_array($aAuthors))
        	$aWhereGroupAnd['cnds'][] = $this->_getSearchAuthorDescriptor($aAuthors);

        if(!empty($iCategory))
        	$aWhereGroupAnd['cnds'][] = array('fld' => 'cat', 'val' => $iCategory, 'opr' => '=');

        if(!empty($sKeyword))
        	$aWhereGroupAnd['cnds'][] = $this->_getSearchKeywordDescriptor($sKeyword);

		return $this->_serviceBrowseTable(array('type' => $sType, 'where' => $aWhereGroupAnd), false);
    }

    /**
     * @page service Service Calls
     * @section bx_forum Discussions
     * @subsection bx_forum-other Other
     * @subsubsection bx_forum-get_discussions_num get_discussions_num
     * 
     * @code bx_srv('bx_forum', 'get_discussions_num', [...]); @endcode
     * 
     * Get number of discussions for some profile.
     * 
     * @param $iProfileId (optional) profile to get discussions for, if omitted then currently logged in profile is used.
     * @return integer value with a number of discussions.
     * 
     * @see BxForumModule::serviceGetDiscussionsNum
     */
    /** 
     * @ref bx_forum-get_discussions_num "get_discussions_num"
     */
    public function serviceGetDiscussionsNum ($iProfileId = 0)
    {
        if (!$iProfileId)
            $iProfileId = bx_get_logged_profile_id();

        return $this->_oDb->getEntriesNumByAuthor((int)$iProfileId);
    }

    /**
     * @page service Service Calls
     * @section bx_forum Discussions
     * @subsection bx_forum-other Other
     * @subsubsection bx_forum-get_unreplied_discussions_num get_unreplied_discussions_num
     * 
     * @code bx_srv('bx_forum', 'get_unreplied_discussions_num', [...]); @endcode
     * 
     * Get number of unreplied discussions for some profile.
     * 
     * @param $iProfileId (optional) profile to get unreplied discussions for, if omitted then currently logged is profile is used.
     * @return integer value with a number of discussions.
     * 
     * @see BxForumModule::serviceGetUnrepliedDiscussionsNum
     */
    /** 
     * @ref bx_forum-get_unreplied_discussions_num "get_unreplied_discussions_num"
     */
    public function serviceGetUnrepliedDiscussionsNum ($iProfileId = 0)
    {
        if (!$iProfileId)
            $iProfileId = bx_get_logged_profile_id();

        return $this->_oDb->getUnrepliedDiscussionsNum((int)$iProfileId);
    }

    /**
     * 
     */
    /**
     * @page service Service Calls
     * @section bx_forum Discussions
     * @subsection bx_forum-other Other
     * @subsubsection bx_forum-trigger_comment_post trigger_comment_post
     * 
     * @code bx_srv('bx_forum', 'trigger_comment_post', [...]); @endcode
     * 
     * Update last comment time and author when new comment was added in the discussion.
     * 
     * @param $iContentId integer value with content ID.
     * @param $iProfileId integer value with profile ID.
     * @param $iCommentId integer value with comment ID.
     * @param $iTimestamp (optional) date in UNIX Timestamp format, if omitted then current date is used.
     * @param $sCommentText (optional) string value with text of comment.
     * @return boolean value determining where the operation is successful or not.
     * 
     * @see BxForumModule::serviceTriggerCommentPost
     */
    /** 
     * @ref bx_forum-trigger_comment_post "trigger_comment_post"
     */
    public function serviceTriggerCommentPost ($iContentId, $iProfileId, $iCommentId, $iTimestamp = 0, $sCommentText = '')
    {
    	$CNF = $this->_oConfig->CNF;

    	$iContentId = (int)$iContentId;
        if(!$iContentId)
            return false;

        $aContentInfo = $this->_oDb->getContentInfoById($iContentId);
        if(!$aContentInfo)
			return false;

        $oCmts = BxDolCmts::getObjectInstance($CNF['OBJECT_COMMENTS'], $iContentId);
        if(!$oCmts)
            return false;

        if(!$iTimestamp)
			$iTimestamp = time();

        if(!$this->_oDb->updateLastCommentTimeProfile((int)$iContentId, (int)$iProfileId, $iCommentId, $iTimestamp))
			return false;

        // send notification to author
        if($iProfileId != $aContentInfo[$CNF['FIELD_AUTHOR']]) {
	        $oProfile = BxDolProfile::getInstanceMagic($iProfileId);
	        if($oProfile) 
                sendMailTemplate('bx_forum_new_reply', 0, $aContentInfo[$CNF['FIELD_AUTHOR']], array(
                    'SenderDisplayName' => $oProfile->getDisplayName(),
                    'SenderUrl' => $oProfile->getUrl(),
                    'PageUrl' => $oCmts->getItemUrl($iCommentId),
                    'PageTitle' => $oCmts->getObjectTitle(),
                    'Message' => $sCommentText,
                ), BX_EMAIL_NOTIFY);
        }

        return true;
    }

    /**
     * @page service Service Calls
     * @section bx_forum Discussions
     * @subsection bx_forum-page_blocks Page Blocks
     * @subsubsection bx_forum-entity_participants entity_participants
     * 
     * @code bx_srv('bx_forum', 'entity_participants', [...]); @endcode
     * 
     * Get page block with discussion's collaborators.
     * 
     * @param $iContentId (optional) content ID. If empty value is provided, an attempt to get it from GET/POST arrays will be performed.
     * @return HTML string with block content to display on the site or false if something is wrong. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxForumModule::serviceEntityParticipants
     */
    /** 
     * @ref bx_forum-entity_participants "entity_participants"
     */
    public function serviceEntityParticipants ($iContentId = 0)
    {
        if (!$iContentId)
            $iContentId = bx_process_input(bx_get('id'), BX_DATA_INT);
        if (!$iContentId)
            return false;
        $aContentInfo = $this->_oDb->getContentInfoById($iContentId);
        if (!$aContentInfo)
            return false;

        return $this->_oTemplate->entryParticipants ($aContentInfo, 5, 'right');
    }

    /**
     * @page service Service Calls
     * @section bx_forum Discussions
     * @subsection bx_forum-page_blocks Page Blocks
     * @subsubsection bx_forum-search search
     * 
     * @code bx_srv('bx_forum', 'search', [...]); @endcode
     * 
     * Get page block with search form.
     * 
     * @return HTML string with block content to display on the site or false if something is wrong. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxForumModule::serviceSearch
     */
    /** 
     * @ref bx_forum-search "search"
     */
	public function serviceSearch()
    {
    	$CNF = $this->_oConfig->CNF;
    	$oForm = BxDolForm::getObjectInstance($CNF['OBJECT_FORM_SEARCH'], $CNF['OBJECT_FORM_SEARCH_DISPLAY_FULL'], $this->_oTemplate);
    	$oForm->initChecker();

        return $oForm->getCode();
    }

    public function checkAllowedSubscribe(&$aDataEntry, $isPerformAction = false)
    {
        $CNF = &$this->_oConfig->CNF;

        $sMsg = $this->checkAllowedView($aDataEntry);
        if($sMsg !== CHECK_ACTION_RESULT_ALLOWED)
            return $sMsg;

        return $this->_checkAllowedConnect ($aDataEntry, $isPerformAction, $CNF['OBJECT_CONNECTION_SUBSCRIBERS'], false, false);
    }

    public function checkAllowedUnsubscribe(&$aDataEntry, $isPerformAction = false)
    {
        $CNF = &$this->_oConfig->CNF;

        return $this->_checkAllowedConnect ($aDataEntry, $isPerformAction, $CNF['OBJECT_CONNECTION_SUBSCRIBERS'], false, true);
    }

    public function checkAllowedStickAnyEntry($aDataEntry, $isPerformAction = false)
    {
    	$CNF = &$this->_oConfig->CNF;

    	if((int)$aDataEntry[$CNF['FIELD_STICK']] != 0)
    		return false;

		return $this->_checkAllowedAction('stick any entry', $aDataEntry, $isPerformAction);
    }

    public function checkAllowedUnstickAnyEntry($aDataEntry, $isPerformAction = false)
    {
    	$CNF = $this->_oConfig->CNF;

    	if((int)$aDataEntry[$CNF['FIELD_STICK']] != 1)
			return false;

    	return $this->_checkAllowedAction('stick any entry', $aDataEntry, $isPerformAction);
    }

    public function checkAllowedLockAnyEntry($aDataEntry, $isPerformAction = false)
    {
    	$CNF = $this->_oConfig->CNF;

    	if((int)$aDataEntry[$CNF['FIELD_LOCK']] != 0)
    		return false;

		return $this->_checkAllowedAction('lock any entry', $aDataEntry, $isPerformAction);
    }

	public function checkAllowedUnlockAnyEntry($aDataEntry, $isPerformAction = false)
    {
    	$CNF = $this->_oConfig->CNF;

    	if((int)$aDataEntry[$CNF['FIELD_LOCK']] != 1)
    		return false;

		return $this->_checkAllowedAction('lock any entry', $aDataEntry, $isPerformAction);
    }

    public function checkAllowedHideAnyEntry($aDataEntry, $isPerformAction = false)
    {
    	$CNF = $this->_oConfig->CNF;

    	if($aDataEntry[$CNF['FIELD_STATUS_ADMIN']] == 'hidden')
    		return false;

		return $this->_checkAllowedAction('hide any entry', $aDataEntry, $isPerformAction);
    }

	public function checkAllowedUnhideAnyEntry($aDataEntry, $isPerformAction = false)
    {
    	$CNF = &$this->_oConfig->CNF;

    	if($aDataEntry[$CNF['FIELD_STATUS_ADMIN']] != 'hidden')
    		return false;

		return $this->_checkAllowedAction('hide any entry', $aDataEntry, $isPerformAction);
    }

    protected function _checkAllowedConnect (&$aDataEntry, $isPerformAction, $sObjConnection, $isMutual, $isInvertResult)
    {
        $CNF = &$this->_oConfig->CNF;

        if(!$this->_iProfileId)
            return _t('_sys_txt_access_denied');

        $isConnected = BxDolConnection::getObjectInstance($sObjConnection)->isConnected($this->_iProfileId, $aDataEntry[$CNF['FIELD_ID']], $isMutual);
        if($isInvertResult)
            $isConnected = !$isConnected;

        return $isConnected ? _t('_sys_txt_access_denied') : CHECK_ACTION_RESULT_ALLOWED;
    }

	/**
     * @return CHECK_ACTION_RESULT_ALLOWED if access is granted or error message if access is forbidden. So make sure to make strict(===) checking.
     */
    protected function _checkAllowedAction($sAction, $aDataEntry, $isPerformAction = false)
    {
        if($this->_isModerator($isPerformAction))
			return CHECK_ACTION_RESULT_ALLOWED;

		$aCheck = checkActionModule($this->_iProfileId, $sAction, $this->getName(), $isPerformAction);
    	if($aCheck[CHECK_ACTION_RESULT] === CHECK_ACTION_RESULT_ALLOWED)
    		return CHECK_ACTION_RESULT_ALLOWED;

        return _t('_sys_txt_access_denied');
    }

	protected function _serviceBrowseTable($aParams, $isDisplayHeader = true)
    {
        $sGrid = $this->_oConfig->CNF['OBJECT_GRID'];
        if(!empty($aParams['grid'])) {
            $sGrid = $aParams['grid'];
            unset($aParams['grid']);
        }

        $oGrid = BxDolGrid::getObjectInstance($sGrid);
        if(!$oGrid)
			return false;

		$oGrid->setBrowseParams($aParams);

        return $oGrid->getCode($isDisplayHeader);
    }

    protected function _getSearchAuthorDescriptor($aAutor)
    {
        return array('grp' => true, 'opr' => 'OR', 'cnds' => array(
            array('fld' => 'author', 'val' => $aAutor, 'opr' => 'IN'),
            array('fld' => 'author_comment', 'val' => $aAutor, 'opr' => 'IN'),
        ));
    }

    protected function _getSearchKeywordDescriptor($sKeyword)
    {
        return array('grp' => true, 'opr' => 'OR', 'cnds' => array(
            array('fld' => 'title', 'val' => $sKeyword, 'opr' => 'LIKE'),
            array('fld' => 'text', 'val' => $sKeyword, 'opr' => 'LIKE'),
            array('fld' => 'text_comments', 'val' => $sKeyword, 'opr' => 'LIKE')
        ));
    }
}

/** @} */
