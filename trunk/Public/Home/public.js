/**
 * Created by Administrator on 2017/9/26.
 */


/**
 * 查看对象中的内容
 * @param obj
 */
function d(obj){
    console.dir(obj);
}


/**
 * debug打印传入数据
 */
function p() {
    for(var i in arguments){
        console.debug(arguments[i]);
    }
}

/**
 * 根据id获取id对应的标签对象
 * @param id
 * @returns {Element}
 */
function $(id){
    return document.getElementById(id);
}

/**
 * 根据id获取标签对象中的值(只适用于表单元素)
 * @param id
 * @returns {string|Number|jQuery}
 */
function $v(id){
    return $(id).value;
}



/**
 * 向object标签对象上添加一个type事件句柄的事件处理函数fn
 * @param object
 * @param type(不带on)
 * @param fn
 */
function addEvent(object,type,fn){
    if(object.addEventListener){ //非IE
        object.addEventListener(type,fn);
    }else{ //IE
        object.attachEvent("on"+type,fn);
    }
}


/**
 * 删除object对象上的type事件对应的事件处理函数fn
 * @param object  标签对象
 * @param type    事件句柄
 * @param fn      事件处理函数
 */
function removeEvent(object,type,fn){
    if(object.removeEventListener){ //非IE
        object.removeEventListener(type,fn);
    }else{
        object.detachEvent("on"+type,fn);
    }
}


/**
 *  创建AJAX对象
 * @returns {*}
 */
function createAjax() {
    var ajax;
    if(window.XMLHttpRequest){
        //>>1,高级浏览器
        ajax = new XMLHttpRequest();
    } else {
        //>>2.垃圾浏览器
        var versions = ["Microsoft.XMLHTTP","Msxml2.XMLHTTP","Msxml2.XMLHTTP.3.0","Msxml2.XMLHTTP.5.0","Msxml2.XMLHTTP.6.0"];
        for (var i in versions){
            try {
                ajax = new ActiveXObject(versions[i]);  //最低版本
                //break;
                if (ajax){
                    return ajax;
                }
            } catch (e){
                return false;
            }
        }
    }
    //返回
    return ajax;
}