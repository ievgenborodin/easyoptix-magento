/**
 * Easy Optix
 *
 * @author      Ievgen Borodin
 * @copyright   Copyright (c) 2016 EasyOptix Inc. (http://www.easy-optix.com)
 */

jQuery(document).ready(function(){
 
    // init values arrays
    var itemsList = jQuery('.product-rows > li.item'), i, j, l, attr,
    secList = {}, sortSections = {},
    filters = {
        'eyewear_type': { 
            'label': 'Type', 
            'type': 'string',
            'urlRule': /sunglasses|eyeglasses/i
        }, 
        'prescription_ready': { 
            'label': 'Prescription Ready', 
            'type': 'boolean',
            'urlRule': /prescription\-sunglasses|eyeglasses/i 
        }, 
        'department': { 
            'label': 'Gender', 
            'type': 'string',
            'urlRule': /men|women|miu-miu/i 
        }, 
        'style': { 
            'label': 'Shape',               
            'type': 'string',
            'urlRule': /style/i 
        }, 
        'brand': { 
            'label': 'Brand',               
            'type': 'string',
            'urlRule': /brand/i 
        }, 
        'brand_model': { 
            'label': 'Model',               
            'type': 'string' 
        }, 
        'brand_ext': { 
            'label': 'Ext',               
            'type': 'string' 
        }, 
        'frame_material': { 
            'label': 'Frame Material',      
            'type': 'string' 
        }, 
        'lens_material': { 
            'label': 'Lens Material',       
            'type': 'string',
            'urlRule': /eyeglasses/i 
        }, 
        'colors': { 
            'label': 'Colors',              
            'type': 'color' 
        }, 
        'polarized': { 
            'label': 'Polarized',           
            'type': 'boolean',
            'urlRule': /eyeglasses/i 
        }, 
        'lens_width': { 
            'label': 'Lens Width',          
            'type': 'string' 
        } 
    };

    // set lists keys 
    for (l in filters)
        secList[l] = [];
    // set lists values

    jQuery.each(itemsList, function(i, el){
        attr = el.dataset.s.split('*'); i=0;
        for (l in secList)
        { 
            secList[l].push(attr[i]);
            i++;
        }
    });

    // filtering sorting sections (based on url)
    var url = jQuery('document').context.URL;

    for(var c in filters){
        var rule = filters[c]['urlRule'], apply;
        if (rule){
            apply = (url.match(rule)) ? true : false;
            (apply) && delete secList[c];
        }
    };
    delete secList['brand_ext'];

    var l, i, j, list, currList, currItem, split, html='', newList,be,
    itemsLength = secList['frame_material'].length;

    for (l in secList){
        list = [];
        currList = secList[l];
        newList = [];

        for (i=0; i<itemsLength; i++){
            currItem = currList[i];
            split = (currItem.split(','));
            if (split.length > 1) { // 'Red,Blue'
                for (j=0; j<split.length; j++)  
                    if (split[j] != 'r')                  
                        if (list.indexOf(split[j]) === -1) {
                            list.push(split[j]);
                            newList[split[j]] = [i];
                        } else 
                            newList[split[j]].push(i);
            } else {  // 'Red', ''
                if (currItem != 'r')
                    if (list.indexOf(currItem) === -1) {
                        list.push(currItem);
                        newList[currItem] = [i];
                    } else {
                        newList[currItem].push(i);  }
            }
        }
        secList[l] = newList;
        html += addSortButtons(l, list.sort());     
    }
    jQuery('.fs-filters-ul').html(html);


    function addSortButtons (code, list)
    {
        var i, j, length =list.length,
        html = '',
        filter = filters[code],
        label = filter['label'], 
        type = filter['type'],
        parent = (filter['parent']) ? filter['parent'] + ' ' : '',
        child = (filter['child']) ? filter['child'] + ' ' : '',
        btns = '', backColor = '', value = '';
        
        if (type==='boolean') {
            html = '<li><div class="sort-btn boolean" data-value="1" data-code="'+ code +'"><span>' + label + '<i class="fa fa-check" aria-hidden="true"></i></span></div></li>';
        } else if (type==='color'){
            if (length<2) return '';
            btns += '<ul>'; 
            for (i=0; i<length; i++)
            {
                backColor = 'style="background-color:' + list[i] + ';color:' + list[i] + '"';
                btns += '<li><div class="sort-btn color" data-value="' + list[i] + '" data-code="'+ code +'"' + backColor + '><i class="fa fa-check" aria-hidden="true"></i></div></li>';
            }
            btns += '</ul>';
            html = '<li><div class="sort-block-title"><span>' + label + '<i class="fa fa-angle-down"></i></span></div><div class="sort-block ' + code + ' animated-hide"><div class="sort-block-opts">'+ btns +'</div></div></li>';
        } else {
            if (length<2) return '';
            btns += '<ul>'; 
            for (i=0; i<length; i++)
            {
                btns += '<li><div class="sort-btn ' + child + parent + '" data-value="' + list[i] + '" data-code="'+ code +'"><i class="fa fa-check" aria-hidden="true"></i>' + list[i] + '</div></li>';
            }
            btns += '</ul>';
            html = '<li><div class="sort-block-title"><span>' + label + '<i class="fa fa-angle-down"></i></span></div><div class="sort-block ' + code + ' animated-hide"><div class="sort-block-opts">'+ btns +'</div></div></li>';
        }
        return html;
    }

    // click event
    jQuery('.sort-btn').on('click', function (e){
        jQuery(this).toggleClass('sort-btn-active');
        
        filterItems(secList);
        if (jQuery('.product-img.hid').length != 0) 
                getVisibles();
    });

    function filterItems(list) {
        // used vars
        var itemsList, 
            filterBlock, childFilterBlock, parentFilterBlock,
            activeBtns, parentActiveBtns, childBtns, btn, pBtn, cBtn,
            code, attrCode, childCode, parentCode,
            attrValue, parentValuesArr = [], childParentValue,
            indexArr = [], i, j, indx,  indexPools = {}, firstPoolArr, currIndex,
            filterRules, rulesLength, r = 0;

        // get list of all items
        itemsList = jQuery('li.item');

        // get list of all active buttons
        activeBtns = jQuery('.sort-btn-active');

        if (activeBtns.length){ 

            // hide all items 
            itemsList.addClass('hide');

            activeBtns.each(function (i, v){
                btn = jQuery(this);
                attrCode = btn.data('code')+'';    
                attrValue = btn.data('value')+''; 

                // check if pool exists
                if (!indexPools[attrCode])
                    indexPools[attrCode] = [];

                //[3,2,5].forEach(function(n){ if(arr.indexOf(n)==-1) arr.push(n) }); 

                list[attrCode][attrValue].forEach(function(j){ 
                    (indexPools[attrCode].indexOf(j) == -1) && indexPools[attrCode].push(j);
                });
            });

                // hide all child sort buttons 
                jQuery('.sort-btn.child').addClass('hide');

                // for each indexPool 
                for (code in indexPools){
                    filterBlock = jQuery('.sort-block.' + code);
                    if (filterBlock.hasClass('parent')){
                        childCode = filterBlock.data('child');
                        childFilterBlock = jQuery('.sort-block.' + childCode);

                        // show child block
                        childFilterBlock.removeClass('animated-hide');

                        // get array of parent active buttons
                        parentActiveBtns = filterBlock.find('.sort-btn-active');
                        
                        // get array of parent active buttons values
                        parentValuesArr = [];
                        parentActiveBtns.each(function(i, v){
                            pBtn = jQuery(this);
                            parentValuesArr.push(pBtn.data('value'));
                        })
                        
                        // get all child sort buttons
                        childBtns = childFilterBlock.find('.sort-btn');
                        
                        // loop through child buttons, get data-parent value, check indexOf "d-p v" 
                        // in parentValuesArr, if not -1 hide child button
                        childBtns.each(function(i, v){
                            cBtn = jQuery(this);
                            childParentValue = cBtn.data('parent');
                            if (parentValuesArr.indexOf(childParentValue) != -1)
                                cBtn.removeClass('hide');
                        });
                    } else if (filterBlock.hasClass('child')) { 
                        // find his parent
                        parentCode = filterBlock.data('parent'); 
                        parentFilterBlock = jQuery('.sort-block.' + parentCode);
                        parentActiveBtns = parentFilterBlock.find('.sort-btn-active');
                        // if no parent found 
                        
                        if (!parentActiveBtns.length) { 
                            // release child active buttons
                            filterBlock.find('.sort-btn.child').removeClass('sort-btn-active');
                            // hide child block
                            if (!filterBlock.hasClass('animated-hide')) {
                                filterBlock.addClass('animated-hide');
                            }
                            // delete indexPools[code]
                            delete indexPools[code];
                        }                        
                    }
                };

            // get list of filter categories (rules)
            filterRules = Object.keys(indexPools);
            rulesLength = filterRules.length;
            if (rulesLength == 0) {
                itemsList.removeClass('hide');
            }
            else if (rulesLength == 1) {
                indexArr = indexPools[filterRules[0]];
            }
            else {
                firstPoolArr = indexPools[filterRules[0]];
                for (i=0, len=firstPoolArr.length; i<len; i++){
                    currIndex = firstPoolArr[i];
                    compareRules(indexArr, indexPools, filterRules, currIndex, r, i);
                }
            }

            // show necessary items
            indexArr.each(function (i){
                itemsList.eq(i).removeClass('hide');
            });

        } else { 
            // if no filters applied - show all items
            itemsList.removeClass('hide');
        }
        
        // show reset button
        if (jQuery('.sort-btn-active').length) 
            jQuery('#filters-reset').css('display', 'block');
        else 
            jQuery('#filters-reset').css('display', 'none');

        // count visibles
        jQuery('#item-counter').html( jQuery('.item').not('.hide').length );
    }

    function checkActiveFilters(current){
        var activeFilters = jQuery('.active-filter'), that;

        activeFilters.not(current).each(function(){
            that = jQuery(this);
            (that.siblings().addClass('animated-hide').stop(true, true).slideUp(200).find('.sort-btn-active').length == 0) && that.removeClass('active-filter');
        });
    }

    function compareRules(indexArr, indexPools, filterRules, currIndex, r, i){
        var nextPoolArr;

        if (r + 1 < filterRules.length)
        {
            nextPoolArr = indexPools[filterRules[r+1]];

            if (nextPoolArr.indexOf(currIndex) != -1)
            {
                r++;
                compareRules(indexArr, indexPools, filterRules, currIndex, r, i);
            }
        }
        else 
        {
            indexArr.push(currIndex);
        }
    }

    jQuery('.sort-block-title').on('click', function(e){
        checkActiveFilters(jQuery(this));
        jQuery(this).addClass('active-filter')
            .siblings().toggleClass('animated-hide').stop(true, true).slideToggle(200);
    });

    jQuery('#filters-reset').on('click', function(e){
        jQuery('.sort-btn-active').removeClass('sort-btn-active');
        checkActiveFilters(jQuery(this));
        filterItems(secList);
    });

    jQuery('#filters-switch').on('click', function(e){
        var that = jQuery(this);
        that.toggleClass('active');
        if (that.hasClass('active')) {
            jQuery('.fs-filters-reset').css('display','block');
            jQuery('.fs-center').css('display','block');            
        } else {
            jQuery('.fs-filters-reset').css('display','none');
            jQuery('.fs-center').css('display','none');
        }
    });

    // vars
    var wh = jQuery(window).height(),
        ih = jQuery('.item').height(),
        wih = wh + ih;

    function getVisibles() {
       // var leftHidden = jQuery('.product-img.hid').length + jQuery('.child-img.hid').length; console.log(leftHidden);
        var items = jQuery('.item').filter(function(index){
            return (jQuery(this).position().top + ih) < (wih + jQuery(window).scrollTop());
        });

        var baseImages = items.find('.product-img.hid')
            .each(function(i, v){
                var that = jQuery(this);
                that.attr('src', that.data('src')).removeClass('hid');
            });

        var childImages = items.find('.child-img.hid')
            .each(function(i, v){
                var that = jQuery(this);
                that.attr('src', that.data('src')).removeClass('hid');
            });
    };

    function afixed(that){
        var currScroll = jQuery(this).scrollTop(),
        maxHeightRaw = that.css('max-height'),
        maxHeight = maxHeightRaw.substr(0, maxHeightRaw.length -2);
        newHeight = maxHeight - currScroll*0.5;
        (newHeight > 0) ? that.css('height', newHeight) : that.css('height', 0);
    };

    function setCategoryHeaderSize(){
        var currafix = jQuery('.category-image-wrap');

        // set category image height 1:5
        var catImageHeight = currafix.width() * .2;
        currafix.css('height', catImageHeight + 'px')
                .css('max-height', catImageHeight + 'px');
    }

    function getFSbreak(){
        var theWidth = jQuery(window).width();

        return (theWidth > 1023) ? 164 : (theWidth > 767) ? 100 : 50 + jQuery('.category-image-wrap').height();
    }

    var theWindow = jQuery(window),
        fsWrap = jQuery('.fs-wrap'),
        fsTopCatch,
        currafix = jQuery('.category-image-wrap');

    setCategoryHeaderSize();

    // top point to catch filters menu
    fsTopCatch = getFSbreak(); 

    theWindow.resize(function() {
        setCategoryHeaderSize();
        fsTopCatch = getFSbreak();
    });

    // first auto start
    getVisibles();
    if (theWindow.scrollTop()>fsTopCatch)
        fsWrap.addClass('fix');
    else 
        fsWrap.removeClass('fix');
    jQuery('#item-counter').html( jQuery('.item').not('.hide').length );

    var lastScroll = 0;
    theWindow.scroll(function() {
    	var scrol = jQuery(this).scrollTop(),
    	invisibles = jQuery('.product-img.hid').length;
        console.log(scrol);
        (currafix.length) && (theWindow.width()>767) && afixed(currafix);

        if (scrol>fsTopCatch)
            fsWrap.addClass('fix');
        else 
            fsWrap.removeClass('fix');
        if (scrol - lastScroll > 150 && invisibles != 0){
        	lastScroll = scrol;
        	getVisibles(); 
        }
    });

    var tempImg = new Image();
    jQuery('.child-img').hover(function(e){
        var that = jQuery(this), parent;
        parent = jQuery('#' + that.data('parent'));
        tempImg.src =  parent.attr('src');
        parent
            .attr('src', that.attr('src'))
            .addClass('slowFade');
    }, function(e){
        var that = jQuery(this), parent;
        parent = jQuery('#' + that.data('parent'));
        parent
            .attr('src', tempImg.src)
            .removeClass('slowFade');
        
    });    

});