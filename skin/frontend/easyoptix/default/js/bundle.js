/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    design
 * @package     base_default
 * @copyright   Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if(typeof Product=='undefined') {
    var Product = {};
}
/**************************** BUNDLE PRODUCT **************************/
Product.Bundle = Class.create();
Product.Bundle.prototype = {
    initialize: function(config){
        this.config = config;

        // Set preconfigured values for correct price base calculation
        if (config.defaultValues) {
            for (var option in config.defaultValues) {
                if (this.config['options'][option].isMulti) {
                    var selected = new Array();
                    for (var i = 0; i < config.defaultValues[option].length; i++) {
                        selected.push(config.defaultValues[option][i]);
                    }
                    this.config.selected[option] = selected;
                } else {
                    this.config.selected[option] = new Array(config.defaultValues[option] + "");
                }
            }
        }

        this.reloadPrice();
    },
    changeSelection: function(id, val){
        var parts = id.split('-'), value= val+'';
        if (value != '') {
            this.config.selected[parts[2]] = new Array(value);
        } else {
            this.config.selected[parts[2]] = new Array();
        }
        this.populateQty(parts[2], value);
        var tierPriceElement = $('bundle-option-' + parts[2] + '-tier-prices'),
            tierPriceHtml = '';
        if (value != '' && this.config.options[parts[2]].selections[value].customQty == 1) {
            tierPriceHtml = this.config.options[parts[2]].selections[value].tierPriceHtml;
        }
        tierPriceElement.update(tierPriceHtml);
        this.reloadPrice();
    },

    reloadPrice: function() {
        var calculatedPrice = 0;
        var dispositionPrice = 0;
        var includeTaxPrice = 0;

        for (var option in this.config.selected) {
            if (this.config.options[option]) {
                for (var i=0; i < this.config.selected[option].length; i++) {
                    var prices = this.selectionPrice(option, this.config.selected[option][i]);
                    calculatedPrice += Number(prices[0]);
                    dispositionPrice += Number(prices[1]);
                    includeTaxPrice += Number(prices[2]);
                }
            }
        }

        //Tax is calculated in a different way for the the TOTAL BASED method
        //We round the taxes at the end. Hence we do the same for consistency
        //This variable is set in the bundle.phtml
        if (taxCalcMethod == CACL_TOTAL_BASE) {
            var calculatedPriceFormatted = calculatedPrice.toFixed(10);
            var includeTaxPriceFormatted = includeTaxPrice.toFixed(10);
            var tax = includeTaxPriceFormatted - calculatedPriceFormatted;
            calculatedPrice = includeTaxPrice - Math.round(tax * 100) / 100;
        }

        //make sure that the prices are all rounded to two digits
        //this is needed when tax calculation is based on total for dynamic
        //price bundle product. For fixed price bundle product, the rounding
        //needs to be done after option price is added to base price
        if (this.config.priceType == '0') {
            calculatedPrice = Math.round(calculatedPrice*100)/100;
            dispositionPrice = Math.round(dispositionPrice*100)/100;
            includeTaxPrice = Math.round(includeTaxPrice*100)/100;

        }

        var event = $(document).fire('bundle:reload-price', {
            price: calculatedPrice,
            priceInclTax: includeTaxPrice,
            dispositionPrice: dispositionPrice,
            bundle: this
        });
        if (!event.noReloadPrice) {
            optionsPrice.specialTaxPrice = 'true';
            optionsPrice.changePrice('bundle', calculatedPrice);
            optionsPrice.changePrice('nontaxable', dispositionPrice);
            optionsPrice.changePrice('priceInclTax', includeTaxPrice);
            optionsPrice.reload();
        }

        return calculatedPrice;
    },

    selectionPrice: function(optionId, selectionId) {
        if (selectionId == '' || selectionId == 'none') {
            return 0;
        }
        var qty = null;
        var tierPriceInclTax, tierPriceExclTax;
        if (this.config.options[optionId].selections[selectionId].customQty == 1 && !this.config['options'][optionId].isMulti) {
            if ($('bundle-option-' + optionId + '-qty-input')) {
                qty = $('bundle-option-' + optionId + '-qty-input').value;
            } else {
                qty = 1;
            }
        } else {
            qty = this.config.options[optionId].selections[selectionId].qty;
        }
        if (this.config.priceType == '0') {
            price = this.config.options[optionId].selections[selectionId].price;
            tierPrice = this.config.options[optionId].selections[selectionId].tierPrice;

            for (var i=0; i < tierPrice.length; i++) {
                if (Number(tierPrice[i].price_qty) <= qty && Number(tierPrice[i].price) <= price) {
                    price = tierPrice[i].price;
                    tierPriceInclTax = tierPrice[i].priceInclTax;
                    tierPriceExclTax = tierPrice[i].priceExclTax;
                }
            }
        } else {
            selection = this.config.options[optionId].selections[selectionId];
            if (selection.priceType == '0') {
                price = selection.priceValue;
            } else {
                price = (this.config.basePrice*selection.priceValue)/100;
            }
        }
        //price += this.config.options[optionId].selections[selectionId].plusDisposition;
        //price -= this.config.options[optionId].selections[selectionId].minusDisposition;
        //return price*qty;
        var disposition = this.config.options[optionId].selections[selectionId].plusDisposition +
            this.config.options[optionId].selections[selectionId].minusDisposition;

        if (this.config.specialPrice) {
            newPrice = (price*this.config.specialPrice)/100;
            price = Math.min(newPrice, price);
        }

        selection = this.config.options[optionId].selections[selectionId];
        if (tierPriceInclTax !== undefined && tierPriceExclTax !== undefined) {
            priceInclTax = tierPriceInclTax;
            price = tierPriceExclTax;
        } else if (selection.priceInclTax !== undefined) {
            priceInclTax = selection.priceInclTax;
            price = selection.priceExclTax !== undefined ? selection.priceExclTax : selection.price;
        } else {
            priceInclTax = price;
        }

        if (this.config.priceType == '1' || taxCalcMethod == CACL_TOTAL_BASE) {
            var result = new Array(price*qty, disposition*qty, priceInclTax*qty);
            return result;
        }
        else if (taxCalcMethod == CACL_UNIT_BASE) {
            price = (Math.round(price*100)/100).toString();
            disposition = (Math.round(disposition*100)/100).toString();
            priceInclTax = (Math.round(priceInclTax*100)/100).toString();
            var result = new Array(price*qty, disposition*qty, priceInclTax*qty);
            return result;
        } else { //taxCalcMethod == CACL_ROW_BASE)
            price = (Math.round(price*qty*100)/100).toString();
            disposition = (Math.round(disposition*qty*100)/100).toString();
            priceInclTax = (Math.round(priceInclTax*qty*100)/100).toString();
            var result = new Array(price, disposition, priceInclTax);
            return result;
        }
    },

    populateQty: function(optionId, selectionId){
        if (selectionId == '' || selectionId == 'none') {
            this.showQtyInput(optionId, '0', false);
            return;
        }
        if (this.config.options[optionId].selections[selectionId].customQty == 1) {
            this.showQtyInput(optionId, this.config.options[optionId].selections[selectionId].qty, true);
        } else {
            this.showQtyInput(optionId, this.config.options[optionId].selections[selectionId].qty, false);
        }
    },

    showQtyInput: function(optionId, value, canEdit) {
        elem = $('bundle-option-' + optionId + '-qty-input');
        elem.value = value;
        elem.disabled = !canEdit;
        if (canEdit) {
            elem.removeClassName('qty-disabled');
        } else {
            elem.addClassName('qty-disabled');
        }
    },

    changeOptionQty: function (element, event) {
        var checkQty = true;
        if (typeof(event) != 'undefined') {
            if (event.keyCode == 8 || event.keyCode == 46) {
                checkQty = false;
            }
        }
        if (checkQty && (Number(element.value) == 0 || isNaN(Number(element.value)))) {
            element.value = 1;
        }
        parts = element.id.split('-');
        optionId = parts[2];
        if (!this.config['options'][optionId].isMulti) {
            selectionId = this.config.selected[optionId][0];
            this.config.options[optionId].selections[selectionId].qty = element.value*1;
            this.reloadPrice();
        }
    },

    validationCallback: function (elmId, result){
        if (elmId == undefined || $(elmId) == undefined) {
            return;
        }
        var container = $(elmId).up('ul.options-list');
        if (typeof container != 'undefined') {
            if (result == 'failed') {
                container.removeClassName('validation-passed');
                container.addClassName('validation-failed');
            } else {
                container.removeClassName('validation-failed');
                container.addClassName('validation-passed');
            }
        }
    }
}

jQuery(document).ready(function(){
    jQuery('.dark-wrapper').appendTo(jQuery('body'));
    jQuery('.lenstype-desc').html('Choose the lens type');
    jQuery('.lensmaterial-desc').html('Choose the material for your lens');
    jQuery('.lensanti-reflection-desc').html('Add anti-reflaction to lens');
    jQuery('.lenstransition-desc').html('Add transition or polarization to lens');

    var stepMarker = document.getElementById('step-marker');

    function focusOn(stepMarker){
        var theWindow = jQuery(window);

        // get current scroll top position
        var scrollTop = theWindow.scrollTop();

        // get current market top
        var markerTop = stepMarker.getBoundingClientRect().top;

        // get header adjustment
        var windowWidth = theWindow.width();
        var topAdjustment = (windowWidth < 1024) ? jQuery('.top-icon-menu').innerHeight() : jQuery('.header-wrap').innerHeight();

        var newScrollTop = scrollTop + markerTop - topAdjustment; 

        // animate scroll change
        jQuery('body,html').stop(false,false).animate({
            scrollTop: newScrollTop
        }, 1000);
    };

    // initial scroll move to the prescription block
    focusOn(stepMarker);

    jQuery('.bundle-option-button').on('click', function(e){
        var that = jQuery(this),
        value = that.data('value'),
        id = that.data('id');

        that.siblings('.bundle-option-button').removeClass('lens-marked');
        that.addClass('lens-marked');
        jQuery('#' + id).val(value);
        bundle.changeSelection(id, value);
        controlSteps();
    });



    function controlSteps(){
        var lensType = jQuery('.bundle-option-button.lenstype-col.lens-marked'),
            lensMaterial = jQuery('.bundle-option-button.lensmaterial-col.lens-marked'),
            lensTransition = jQuery('.lenstransition-block'), id;

        if (lensType.length){
            var lensmaterialBlock = jQuery('.lensmaterial-block');
            if (!lensmaterialBlock.hasClass('show-iblock')) {
                lensmaterialBlock.addClass('show-iblock').append(stepMarker);
                focusOn(stepMarker);
            }
            
            if (lensMaterial.length){
                var lensantireflectionBlock = jQuery('.lensanti-reflection-block');
                if (!lensantireflectionBlock.hasClass('show-iblock')) {
                    lensantireflectionBlock.addClass('show-iblock').append(stepMarker);
                    focusOn(stepMarker);
                }
                if (lensType.eq(0).data('index')!=2) 
                    lensTransition.addClass('show-iblock'); 
                else {
                    lensTransition.removeClass('show-iblock'); 
                    id = lensTransition.find('.bundle-option-button').removeClass('lens-marked').eq(0).addClass('lens-marked').data('id');
                    bundle.changeSelection(id, '');
                }
                jQuery('.product-options-bottom').addClass('show-iblock');
            }   
        }
    };

    jQuery('.terms-agree').on('click', function(e){
        var that = jQuery(this);
        if (!that.hasClass('lens-button-on')) that.addClass('lens-button-on');
        var lenstypeBlock = jQuery('.lenstype-block');
        if (!lenstypeBlock.hasClass('show-iblock')) {
            lenstypeBlock.addClass('show-iblock').append(stepMarker);
            //stepMarker.innerHTML = '2';
            focusOn(stepMarker);
        }

        /*lensTypeCols.eq(0).find('.recommended').addClass('recommended-on');
        lensTypeCols.eq(1).find('.recommended').addClass('recommended-on');

        lensMatCols.eq(1).find('.recommended').addClass('recommended-on');

        lensAntiReflectionCols.eq(1).find('.recommended').addClass('recommended-on');
        lensAntiReflectionCols.eq(2).find('.recommended').addClass('recommended-on');

        lensTransitionCols.eq(0).find('.recommended').addClass('recommended-on');
        lensTransitionCols.eq(1).find('.recommended').addClass('recommended-on');*/

        /*jQuery('.recommended-on').hover(function(){
            var that = jQuery(this),
            text = that.find('.why-text').html(),
            bb = that[0].getBoundingClientRect();

            descriptionBlock.css('display', 'block')
            .css('left', bb.left - bb.width + 'px')
            .css('top', bb.top - bb.height + jQuery(window).scrollTop() + 'px')
            .css('max-width', bb.width*2)
            .find('.description').html(text);
        }, function (){
            descriptionBlock.css('display', 'none');
        });*/
    });

    jQuery('.compare').on('click', function(e){
        var name = jQuery(this).data('infoname'), image;
        jQuery('.imagination > img').css('display','none');
        image = jQuery('.imagination > .' + name);
        image.attr('src', image.data('src')).css('display', 'block');
        jQuery('.dark-wrapper').css('display', 'block').css('height', jQuery(document).height() + 'px');
    });

    jQuery('.dark-wrapper').on('click', function(e){
        jQuery(this).css('display', 'none');
    });

});