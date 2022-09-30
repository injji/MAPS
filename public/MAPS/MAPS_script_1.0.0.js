/* MAPS Script v1.0.0
 * setting: v1.0 2021-04-16
 */
(function(wd,dc) {
    "use strict";
    let protocol=wd.location.protocol;
    let path=wd.location.pathname;
    let host=wd.location.host;
    let refer=dc.referrer;
    let agent=navigator.userAgent;
    let elementBody = dc.body||dc.getElementsByTagName("body")[0];
    // meta 정보값 리턴
    let getMeta=function(a){try{return dc.querySelector(a).getAttribute("content")}catch(e){return ""}}
    // url 파라메터값 리턴
    let getParam=function(a,b){var r='';a.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(str, k, v){if(k==b) r=v;});return r.split('#')[0];}
    // 이벤트 트리거(상품정보,장바구니,찜,구매완료,회원가입;MAPS.item/add_to_cart/add_to_favorite/purchase/join)
    let eventTrigger=function(e,d=null){
        let event;
        if(typeof wd.CustomEvent === "function"){
            event = new CustomEvent('MAPS.'+e,{detail:d});
        }else{//for IE9 polyfill 구현했으나 Edge이상 지원하는것을 원칙?? 마테크업체에 따라 polyfill cdn 지원검토
            event = dc.createEvent("CustomEvent");
            event.initCustomEvent('MAPS.'+e, true, false, d);
        }
        wd.dispatchEvent(event);
    }
    
    //카페24, 메이크샵, 고도몰 이벤트 분기처리
    //페이지 정의(카페24)
    let getPageRole=function(){return getMeta('[name="path_role"]')}
    //페이지별 이벤트 정의(카페24)
    let eventPages={
        PRODUCT_DETAIL:function(){
            if(typeof wd['basket_result_action']==='function'){
                let callback=wd['basket_result_action'];
                wd['basket_result_action']=function(){
                    eventPages.ADD_TO_CART();
                    return callback.apply(callback,arguments);
                }
            }
            if(typeof wd['add_wishlist_result']==='function'){
                let callback=wd['add_wishlist_result'];
                wd['add_wishlist_result']=function(){
                    eventPages.ADD_TO_WISHLIST();
                    return callback.apply(callback,arguments);
                }
            }
            let items={id:'상품코드',name:'상품명',price:'상품가'};
            eventTrigger('item',{items:items});
        },
        ADD_TO_CART:function(){
            eventTrigger('add_to_cart');
        },
        ADD_TO_WISHLIST:function(){
            eventTrigger('add_to_favorite');
        },
        ORDER_ORDERRESULT:function(){
            eventTrigger('purchase',{items:CAFE24.FRONT_EXTERNAL_SCRIPT_VARIABLE_DATA.order_product});
        }
    }

    // Agent Service JS 삽입처리
    let appendServiceJS=function(src,trigger){
        let js = dc.createElement('script');
        js.type='text/javascript';
        src+=(src.indexOf('?')!=-1) ? "&":"?";
        src+='client_id=' + MAPSAPI.client_id + '&dc=' + Math.round(+new Date()/3600000);
        js.src = src;
        if(trigger){
            js.onload=function(){
                eval(trigger);
            }
        }
        elementBody.appendChild(js);
    }
    let insertServiceJS=function(){
        return new Promise(function (resolve, reject) {
            if ( typeof MAPSAPI.getAgentService == "function" ) {
                let  scripts = MAPSAPI.getAgentService();
                if( Array.isArray(scripts) ) {
                    scripts.forEach(function(v) {
                        appendServiceJS(v['src'], v['trigger']);
                    });
                    resolve();
                }else{
                    reject();
                }
            }else{
                reject();
            }
        });
    }

    // 실행
    wd.addEventListener('DOMContentLoaded',function(){
        if( typeof MAPSAPI === 'object' ){
            insertServiceJS()
            .then(function(){
                let pageRole = getPageRole();
                if(typeof eventPages[pageRole]==='function') eventPages[pageRole]();
                console.log('MAPS Script v'+MAPSAPI.version);
            })
            .catch(function(err){
                console.error('MAPS Script load failed.');
            });
        }else{
            console.error('MAPS API not found.');
        }
    });
})(window,document);