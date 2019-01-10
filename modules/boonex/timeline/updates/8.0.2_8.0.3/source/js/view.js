function BxTimelineView(oOptions) {
	this._sActionsUri = oOptions.sActionUri;
    this._sActionsUrl = oOptions.sActionUrl;
    this._sObjName = oOptions.sObjName == undefined ? 'oTimelineView' : oOptions.sObjName;
    this._iOwnerId = oOptions.iOwnerId == undefined ? 0 : oOptions.iOwnerId;
    this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'slide' : oOptions.sAnimationEffect;
    this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
    this._aHtmlIds = oOptions.aHtmlIds == undefined ? {} : oOptions.aHtmlIds;
    this._oRequestParams = oOptions.oRequestParams == undefined ? {} : oOptions.oRequestParams;

    var $this = this;
    $(document).ready(function() {
    	$this.initMasonry();
    	$('.bx-tl-item').resize(function() {
    		$this.reloadMasonry();
    	});
    	$('img.bx-tl-item-image').load(function() {
    		$this.reloadMasonry();
    	});
    });
}

BxTimelineView.prototype = new BxTimelineMain();

BxTimelineView.prototype.changePage = function(oElement, iStart, iPerPage) {
	this._oRequestParams.start = iStart;
    this._oRequestParams.per_page = iPerPage;

    this._getPosts(oElement, 'page');
};

BxTimelineView.prototype.changeFilter = function(oLink) {
    var sId = $(oLink).attr('id');

    this._oRequestParams.start = 0;
    this._oRequestParams.filter = sId.substr(sId.lastIndexOf('-') + 1, sId.length);

    this._getPosts(oLink, 'filter');
};

BxTimelineView.prototype.changeTimeline = function(oLink, iYear) {
	this._oRequestParams.start = 0;
    this._oRequestParams.timeline = iYear;

	this._getPosts(oLink, 'timeline');
};

BxTimelineView.prototype.deletePost = function(oLink, iId) {
    var $this = this;
    var oView = $(this.sIdView);
    var oData = this._getDefaultData();
    oData['id'] = iId;

    this.loadingInBlock(oLink, true);

    $.post(
        this._sActionsUrl + 'delete/',
        oData,
        function(oData) {
        	$this.loadingInBlock(oLink, false);

        	if(oData && oData.msg != undefined)
                alert(oData.msg);

            if(oData && oData.code == 0)
            	$(oLink).parents('.bx-popup-applied:first:visible').dolPopupHide();

                $($this.sIdItem + oData.id).bx_anim('hide', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
                    $(this).remove();

                    if(oView.find('.bx-tl-item').length != 0) {
                    	$this.reloadMasonry();
                    	return;
                    } 

                    $this.destroyMasonry();
                    oView.find('.bx-tl-load-more').hide();
                    oView.find('.bx-tl-empty').show();
                });                        
        },
        'json'
    );
};

BxTimelineView.prototype.showMoreContent = function(oLink) {
	$(oLink).parent('span').next('span').show().prev('span').remove();
	this.reloadMasonry();
};

BxTimelineView.prototype.showManageMenu = function(oLink, iId) {
	var oData = this._getDefaultData();    
    oData['id'] = iId;

    $(this.sIdView).dolPopupAjax({
		id: this._aHtmlIds['menu_popup'] + iId,
		url: bx_append_url_params(this._sActionsUri + 'get_manage_menu_popup/', oData),
		pointer:{
			el:$(oLink)
		}
	});

	return false;
};

BxTimelineView.prototype.showPhoto = function(oLink, sUrl) {
	$('#' + this._aHtmlIds['photo_popup']).dolPopupImage(sUrl, $(oLink).parent());
};

BxTimelineView.prototype.commentItem = function(oLink, sSystem, iId) {
	var $this = this;

    var oData = this._getDefaultData();
    oData['system'] = sSystem;    
    oData['id'] = iId;

    var oComments = $(oLink).parents('.' + this.sClassItem + ':first').find('.' + this.sClassItemComments);
    if(oComments.children().length > 0) {
    	oComments.bx_anim('toggle', this._sAnimationEffect, this._iAnimationSpeed);
    	return;
    }

    if(oLink)
    	this.loadingInItem(oLink, true);

    jQuery.get (
        this._sActionsUrl + 'get_comments',
        oData,
        function(oData) {
        	if(oLink)
        		$this.loadingInItem(oLink, false);

        	if(!oData.content)
        		return;

        	oComments.html($(oData.content).hide()).children(':hidden').bxTime().bx_anim('show', $this._sAnimationEffect, $this._iAnimationSpeed);
        },
        'json'
    );
};

BxTimelineView.prototype._getPosts = function(oElement, sAction) {
    var $this = this;
    var oView = $(this.sIdView);

	switch(sAction) {
		case 'page':
			this.loadingInButton(oElement, true);
			break;

		default:
			this.loadingInBlock(oElement, true);
			break;
	}

    jQuery.get(
        this._sActionsUrl + 'get_posts/',
        this._getDefaultData(),
        function(oData) {
        	if(oData && oData.items != undefined) {
        		var sItems = $.trim(oData.items);

	        	switch(sAction) { 
	        		case 'page':
	        			$this.loadingInButton(oElement, false);
	        			$this.appendMasonry($(sItems).bxTime());
			            break;

	        		default:
	        			$this.loadingInBlock(oElement, false);

	        			oView.find('.' + $this.sClassItems).bx_anim('hide', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
			                $(this).html(sItems).show().bxTime();

			                if($this.isMasonryEmpty()) {
			                	$this.destroyMasonry();
			                	return;
			                }

			                if($this.isMasonry())
			                	$this.reloadMasonry();
			        		else
			        			$this.initMasonry();
			            });
		            	break;
	            }
        	}

        	if(oData && oData.load_more != undefined)
        		oView.find('.' + $this.sSP + '-load-more-holder').html($.trim(oData.load_more));

        	if(oData && oData.back != undefined)
        		oView.find('.' + $this.sSP + '-back-holder').html($.trim(oData.back));
        },
        'json'
    );
};
