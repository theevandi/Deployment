/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Shopify Shopify
 * @ingroup     UnaModules
 *
 * @{
 */

function BxShopifyShop(oOptions) {
    this._sDomain = oOptions.sDomain;
    this._sAccessToken = oOptions.sAccessToken;

    this._sActionsUrl = oOptions.sActionUrl;
    this._sObjName = oOptions.sObjName == undefined ? 'oBxShopifyShop' : oOptions.sObjName;

    this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'fade' : oOptions.sAnimationEffect;
    this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
    this._sParamsDivider = oOptions.sParamsDivider == undefined ? '#-#' : oOptions.sParamsDivider;

    this._aHtmlIds = oOptions.aHtmlIds == undefined ? {} : oOptions.aHtmlIds;
    this._oRequestParams = oOptions.oRequestParams == undefined ? {} : oOptions.oRequestParams;

    this._sClassCover = 'bx-cover-wrapper';
    this._sClassLoading = 'bx-spf-loading';
    this._sClassLoaded = 'bx-spf-loaded';

    this._oShopClient = window.ShopifyBuy.buildClient({
        domain: this._sDomain,
        storefrontAccessToken: this._sAccessToken
    });
}

BxShopifyShop.prototype.getUnit = function(sProduct) {
    var $this = this;

    var oUnit = $('.' + this._aHtmlIds['unit'] + sProduct + ':not(.' + this._sClassLoading + ', .' + this._sClassLoaded + '):first');
    if(!oUnit.length)
        return;

    bx_loading(oUnit.addClass(this._sClassLoading), true);

    this.fetchProductByHandle(sProduct, function (oProduct) {
        $this.onGetUnit(oProduct);
    }, function() {
        bx_loading(oUnit.removeClass(this._sClassLoading), false);

        bx_alert(_t('_bx_shopify_err_load_product'));
    });
};

BxShopifyShop.prototype.onGetUnit = function(oProduct) {
    if(!oProduct)
        return;

    var oUnit = $('.' + this._aHtmlIds['unit'] + oProduct.handle + '.' + this._sClassLoading + ':first');
    if(!oUnit.length)
        return;

    //--- Fetch product description.
    oUnit.find('.bx-spf-unit-title').html(oProduct.title);

    //--- Fetch product description.
    oUnit.find('.bx-spf-unit-summary').html(oProduct.description);
    oUnit.find('.bx-base-text-unit-strecher').html(oProduct.description);

    //--- Fetch price.
    if(oProduct.variants.length > 0) {
        var oBuy = oUnit.find('.bx-shopify-buy');
        var sBuyContent = oBuy.html();
        oBuy.html(sBuyContent.replace('{price}', oProduct.variants[0].price)).show();
    }

    bx_loading(oUnit.removeClass(this._sClassLoading).addClass(this._sClassLoaded), false);
};

BxShopifyShop.prototype.getEntry = function(sProduct) {
    var $this = this;
    var oLoading = $('.' + this._sClassCover);

    bx_loading(oLoading, true);

    this.fetchProductByHandle(sProduct, function(oProduct) {
        $this.onGetEntry(oProduct);
    }, function() {
        bx_loading(oLoading, false);

        bx_alert(_t('_bx_shopify_err_load_product'));
    });
};

BxShopifyShop.prototype.onGetEntry = function(oProduct) {
    if(!oProduct)
        return;

    //--- Fetch product title.
    $('#' + this._aHtmlIds['entry_content']).find('.bx-spf-title').html(oProduct.title);

    //--- Fetch product description.
    $('#' + this._aHtmlIds['entry_content']).find('.bx-spf-text').html(oProduct.description);

    //--- Fetch product images.
    var oSample = $('#' + this._aHtmlIds['entry_attachment_sample']);
    var oContainer = $('#' + this._aHtmlIds['entry_attachments']);
    var iImage = 0;
    oProduct.images.map(function(oImage) {
        var oAttachment = oSample.clone().removeAttr('id').removeClass('bx-spf-attachment-sample');

        var oLink = oAttachment.find('.bx-spf-attachment-link');
        var sLinkOnClick = oLink.attr('onclick');
        oLink.attr('onclick', sLinkOnClick.replace('{id}', iImage).replace('{url}', oImage.src));
        oLink.find('.bx-spf-attachment-img').attr('src', oImage.src);

        var oPopup = oAttachment.find('.bx-popup-wrapper');
        var sPopupId = oPopup.attr('id');
        oPopup.attr('id', sPopupId.replace('{id}', iImage));
        oPopup.find('.bx-spf-attachment-popup-img').attr('src', oImage.src);

        oContainer.prepend(oAttachment);

        iImage += 1;
    });
    oSample.remove();

    //--- Fetch price.
    if(oProduct.variants.length > 0) {
        var oBuy = $('#' + this._aHtmlIds['entry_buy']);
        var sBuyContent = oBuy.html();
        oBuy.html(sBuyContent.replace('{price}', oProduct.variants[0].price)).show();
    }

    bx_loading($('.' + this._sClassCover), false);
};

BxShopifyShop.prototype.buyProductByHandle = function(sProduct, oLink) {
    var $this = this;
    oLink = $(oLink);

    bx_loading_btn(oLink, true);

    $this.fetchProductByHandle(sProduct, function(oProduct) {
        if(oProduct.variants.length <= 0)
            return;

        $this.createCheckout(function(oCheckout) {
            $this.addItems(oCheckout.id, [{variantId:oProduct.variants[0].id, quantity: 1}], function(oCheckout){
                bx_loading_btn(oLink, false);

                location.href = oCheckout.webUrl;
            }, function() {
                bx_loading_btn(oLink, false);
                bx_alert(_t('_bx_shopify_err_purchase_product'));
            });
        }, function() {
            bx_loading_btn(oLink, false);
            bx_alert(_t('_bx_shopify_err_purchase_product'));
        });
    }, function() {
        bx_loading_btn(oLink, false);
        bx_alert(_t('_bx_shopify_err_load_product'));
    });
}
    
BxShopifyShop.prototype.fetchProduct = function(iProduct, onComplete, onError) {
    var $this = this;

    this._oShopClient.product.fetch(iProduct).then(function(oProduct) {
        if(typeof(onComplete) == 'function')
            onComplete(oProduct);
        else 
            $this.onFetchProduct(oProduct);
    }).catch(function() {
        if(typeof(onError) == 'function')
            onError();
        else 
            bx_alert(_t('_bx_shopify_err_load_product'));
    });
};

BxShopifyShop.prototype.fetchProductByHandle = function(sProduct, onComplete, onError) {
    var $this = this;

    this._oShopClient.product.fetchByHandle(sProduct).then(function(oProduct) {
        if(typeof(onComplete) == 'function')
            onComplete(oProduct);
        else 
            $this.onFetchProduct(oProduct);
    }).catch(function(oError) {
        if(typeof(onError) == 'function')
            onError();
        else 
            bx_alert(_t('_bx_shopify_err_load_product'));
    });
};

BxShopifyShop.prototype.fetchCollection = function(iCollection, onComplete, onError) {
    var $this = this;

    this._oShopClient.fetchQueryProducts({collection_id: iCollection}).then(function(oProducts) {
        if(typeof(onComplete) == 'function')
            onComplete(oProduct);
        else 
            $this.onFetchCollection(oProducts);
    }).catch(function() {
        if(typeof(onError) == 'function')
            onError();
        else 
            bx_alert(_t('_bx_shopify_err_load_collection'));
    });
};

BxShopifyShop.prototype.createCheckout = function(onComplete, onError) {
    var $this = this;

    this._oShopClient.checkout.create().then(function(oCheckout) {
        if(typeof(onComplete) == 'function')
            onComplete(oCheckout);
        else 
            $this.onCreateCheckout(oCheckout);
    }).catch(function() {
        if(typeof(onError) == 'function')
            onError();
        else 
            bx_alert(_t('_bx_shopify_err_purchase_product'));
    });
};

BxShopifyShop.prototype.addItems = function(sCheckoutId, aLineItems, onComplete, onError) {
    var $this = this;

    this._oShopClient.checkout.addLineItems(sCheckoutId, aLineItems).then(function(oCheckout) {
        if(typeof(onComplete) == 'function')
            onComplete(oCheckout);
        else 
            $this.onAddItems(oCheckout);
    }).catch(function() {
        if(typeof(onError) == 'function')
            onError();
        else 
            bx_alert(_t('_bx_shopify_err_purchase_product'));
    });
};

BxShopifyShop.prototype.onFetchProduct = function(oProduct) {
    //--- Some default action can be put here.
};

BxShopifyShop.prototype.onFetchCollection = function(oProducts) {
    //--- Some default action can be put here.
};

BxShopifyShop.prototype.onCreateCheckout = function(oCheckout) {
    //--- Some default action can be put here.
};

BxShopifyShop.prototype.onAddItems = function(oCheckout) {
    //--- Some default action can be put here.
};

/** @} */
