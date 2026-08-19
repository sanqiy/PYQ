/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */
/*********************************
[苏画弹窗系统]：开发时间2023/1/5

使用说明：
******>>>>>> [参数1] = 需要提示的内容
******>>>>>> [参数2] = ok (设为ok即可弹出带全屏遮罩的弹窗,为空则无遮罩)
******> 【加载中提示】：loadpop("加载中，请稍后...",'ok');
******> 【成功提示】：successpop("评论发送成功");
******> 【失败提示】：errorpop("评论发送失败，请重试")
******> 【警告提示】：warnpop("评论发送成功，通过审核后即可显示");

[作者]:苏画
[Time]:2023/1/7
[url]:qemao.com
[qq]:2517270540

(原创工程)使用时请保留作者版权
*********************************/



//生成随机数函数，用于生成信息框元素随机id名
function getRandomAlphaNum(len) {
    var rdmString = "";
    for (; rdmString.length < len; rdmString += Math.random().toString(36).substr(2));
    return rdmString.substr(0, len);
}


//点击关闭并删除被点击的信息框元素
function closetheck() {
    //获取当前点击的div的id
    var ele = window.event.srcElement.id;
    //将信息框class切换为退场动画开始退场
    if (document.getElementById(ele)) {//如果名为此id的div存在才执行
        document.getElementById(ele).className += ' sh_move_close';
        window.setTimeout(function () {
            if (document.getElementById(ele)) {//如果名为此id的div存在才执行
                //弹窗信息删除前关闭全屏遮罩
                if (maskstate != "none") {
                    document.getElementById(masksname).style.display="none";
                }
                //取出本次创建的信息框div的id
                var rem = document.getElementById(ele);
                //获取到本次创建的信息框元素后执行删除
                if (rem != null) {
                    rem.remove();//按顺序删除
                }
            }
        }, 250);
    }

}


//删除所有的加载中提示弹窗元素
function delclose() {
    //通过name获取所有的加载提示弹窗元素
    var arrs = document.getElementsByName("sh-notyn-load");
    //当存在加载提示弹窗的时候才会执行删除所有
    if (arrs.length) {
        //循环操作每一个
        for (var i = 0; i < arrs.length; i++) {
            //给每个加载提示弹窗元素设置退出动画,并设置不独占一行
            //document.getElementById(arrs[i].id).className += ' sh_move_loadout_close notynload';
            //取出本次的信息框div的id
            var rem = document.getElementById(arrs[i].id);
            if (rem != null) {
                rem.remove();//按顺序删除
            }
            i--;
        }
    }

}



/* 全屏遮罩点击事件 */
function carriermask(){
    event.stopPropagation();//禁止冒泡,禁止遮罩层以下的所有元素响应任何事件
}


/* -------------------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------------------- */





/* 只要调用这个文件就会默认在屏幕右下角创建一个外部框架用于承载信息提示框 (这是必要的)*/

//创建一个div作为整个提示信息的外部框架，承载显示信息提示框，被定为在右下角
var div = document.createElement("div");
//为div创建属性class = "suhua-notyn-carrier"
var divattr = document.createAttribute("class");
//class命名为notyn 并加上入场动画
divattr.value = "suhua-notyn-carrier";
//把属性class = "suhua-notyn-carrier"添加到div
div.setAttributeNode(divattr);

//为div创建id属性
//为div创建属性id
var divattr = document.createAttribute("id");
//id命名为suhua-notyn-carrier
divattr.value = "suhua-notyn-carrier";
//把属性id添加到div
div.setAttributeNode(divattr);

document.getElementsByTagName("body").item(0).appendChild(div);



//创建一个全屏透明遮罩,用于需要时屏蔽屏幕上的所有操作
var div = document.createElement("div");
//为div创建属性class = "suhua-notyn-carrier"
var divattr = document.createAttribute("class");
//class命名为suhua-notyn-carrier-mask 并加上入场动画
divattr.value = "suhua-notyn-carrier-mask";
//把属性class = "suhua-notyn-carrier-mask"添加到div
div.setAttributeNode(divattr);

//为div创建id属性
//为div创建属性id
var divattr = document.createAttribute("id");
//id命名为suhua-notyn-carrier-mask
divattr.value = "suhua-notyn-carrier-mask";
//把属性id添加到div
div.setAttributeNode(divattr);

//为div创建onclick事件
var divattr = document.createAttribute("onclick");
//函数名为carriermask
divattr.value = "carriermask()";
//把属性事件添加到div
div.setAttributeNode(divattr);

document.getElementsByTagName("body").item(0).appendChild(div);





/* -------------------------------------------------------------------------------------- */
/* -------------------------------------------------------------------------------------- */





























//获取遮罩状态
var maskstate = document.getElementById("suhua-notyn-carrier-mask").style.display;
//定义遮罩class名
var masksname="suhua-notyn-carrier-mask";




//创建成功提示
function successpop(text,maskok) {

    //判断是否有正在显示的遮罩,如果有则先隐藏
    if (maskstate != "none") {
        document.getElementById(masksname).style.display="none";
    }
    //判断需不需要遮罩,需要则显示
    if (maskok == "ok") {
        document.getElementById(masksname).style.display="flex";
    }

    delclose();//创建前先删除所有的加载提示弹窗

    //创建一个div作为整个提示信息的框架
    var div = document.createElement("div");
    //为div创建属性class = "notyn"
    var divattr = document.createAttribute("class");
    //class命名为notyn 并加上入场动画
    divattr.value = "notyn sh_move_open";
    //把属性class = "notyn"添加到div
    div.setAttributeNode(divattr);

    //为div创建id属性
    //生成随机id
    var numsg = getRandomAlphaNum(32);//生成随机数id
    //为div创建属性id
    var divattr = document.createAttribute("id");
    //id命名为sh-notyno- 加随机数
    divattr.value = "sh-notyno-" + numsg;
    //把属性id添加到div
    div.setAttributeNode(divattr);

    //为div创建onclick事件
    var divattr = document.createAttribute("onclick");
    //函数名为closetheck
    divattr.value = "closetheck()";
    //把属性事件添加到div
    div.setAttributeNode(divattr);







    //创建一个i标签
    var i = document.createElement("i");
    //给i标签添加class
    var divattr = document.createAttribute("class");
    //i标签class命名为notyn-icon 并设置图标iconfont icon-xinxi1
    divattr.value = "notyn-icon iconfont-sh icon-zhengque";
    //将class属性添加到i标签
    i.setAttributeNode(divattr);

    //创建一个span标签
    var span = document.createElement("span");
    //为span标签添加文本内容
    span.innerText = text;//内容为变量text传过来的值
    //给span标签添加class
    var divattr = document.createAttribute("class");
    //span标签class命名为notyn-span
    divattr.value = "notyn-span";
    //将class属性添加到i标签
    span.setAttributeNode(divattr);


    //创建一hello,world个文本节点
    //var text = document.createTextNode("hello,world");

    //将按钮和文本节点追加到div
    div.appendChild(i);
    div.appendChild(span);
    //div.appendChild(text);

    //为div添加样式
    var style = document.createAttribute("style");
    div.setAttributeNode(style);
    //添加背景颜色：蓝色成功渐变色
    div.style.background = "linear-gradient(90deg,rgba(12,145,250,0.8),rgba(70,195,250,0.9))";
    /*var style = document.createAttribute("style");
    div.setAttributeNode(style);
    div.style.backgroundColor = "#F00";
    div.style.borderWidth = "20px";
    div.style.borderColor = "#000";
    div.style.width = "500px";
    div.style.height = "500px";
    div.style.marginLeft = "30%";
    div.style.marginTop = "1%";*/

    //把div追加到class名为suhua-notyn-carrier的div里
    document.getElementsByClassName("suhua-notyn-carrier").item(0).appendChild(div);

    //判断是否存在本次创建的信息弹窗--执行显示指定时间后自动删除
    if (document.getElementById("sh-notyno-" + numsg)) {
        //存在则开始延迟,定时信息框只显示5秒
        window.setTimeout(function () {
            //5秒时间到将信息框class切换为退场动画开始退场
            if (document.getElementById("sh-notyno-" + numsg)) {
                document.getElementById("sh-notyno-" + numsg).className += ' sh_move_close';
                //退场后再延迟250毫秒，随后删除整个信息框div元素
                window.setTimeout(function () {
                    //弹窗信息删除前关闭全屏遮罩,删除的前提是必须由打开的弹窗删除
                    if (maskok == "ok") {
                        if (maskstate != "none") {
                            document.getElementById(masksname).style.display="none";
                        }
                    }
                    //取出本次创建的信息框div的id
                    var rem = document.getElementById("sh-notyno-" + numsg);
                    //获取到本次创建的信息框元素后执行删除
                    if (rem != null) {
                        rem.remove();//按顺序删除
                    }
                }, 250);
            }
        }, 5000);
    }

}





/* ###################################################################################### */





//创建失败提示
function errorpop(text,maskok) {

    //判断是否有正在显示的遮罩,如果有则先隐藏
    if (maskstate != "none") {
        document.getElementById(masksname).style.display="none";
    }
    //判断需不需要遮罩,需要则显示
    if (maskok == "ok") {
        document.getElementById(masksname).style.display="flex";
    }

    delclose();//创建前先删除所有的加载提示弹窗

    //创建一个div作为整个提示信息的框架
    var div = document.createElement("div");
    //为div创建属性class = "notyn"
    var divattr = document.createAttribute("class");
    //class命名为notyn 并加上入场动画
    divattr.value = "notyn sh_move_open";
    //把属性class = "notyn"添加到div
    div.setAttributeNode(divattr);

    //为div创建id属性
    //生成随机id
    var numsg = getRandomAlphaNum(32);//生成随机数id
    //为div创建属性id
    var divattr = document.createAttribute("id");
    //id命名为sh-notyno- 加随机数
    divattr.value = "sh-notyno-" + numsg;
    //把属性id添加到div
    div.setAttributeNode(divattr);

    //为div创建onclick事件
    var divattr = document.createAttribute("onclick");
    //函数名为closetheck
    divattr.value = "closetheck()";
    //把属性事件添加到div
    div.setAttributeNode(divattr);







    //创建一个i标签
    var i = document.createElement("i");
    //给i标签添加class
    var divattr = document.createAttribute("class");
    //i标签class命名为notyn-icon 并设置图标iconfont icon-xinxi1
    divattr.value = "notyn-icon iconfont-sh icon-cuowu";
    //将class属性添加到i标签
    i.setAttributeNode(divattr);

    //创建一个span标签
    var span = document.createElement("span");
    //为span标签添加文本内容
    span.innerText = text;//内容为变量text传过来的值
    //给span标签添加class
    var divattr = document.createAttribute("class");
    //span标签class命名为notyn-span
    divattr.value = "notyn-span";
    //将class属性添加到i标签
    span.setAttributeNode(divattr);


    //创建一hello,world个文本节点
    //var text = document.createTextNode("hello,world");

    //将按钮和文本节点追加到div
    div.appendChild(i);
    div.appendChild(span);
    //div.appendChild(text);

    //为div添加样式
    var style = document.createAttribute("style");
    div.setAttributeNode(style);
    //添加背景颜色：红色失败渐变色
    div.style.background = "linear-gradient(90deg,rgb(255,105,80,0.8),rgb(250,50,50,0.9))";
    /*var style = document.createAttribute("style");
    div.setAttributeNode(style);
    div.style.backgroundColor = "#F00";
    div.style.borderWidth = "20px";
    div.style.borderColor = "#000";
    div.style.width = "500px";
    div.style.height = "500px";
    div.style.marginLeft = "30%";
    div.style.marginTop = "1%";*/

    //把div追加到class名为suhua-notyn-carrier的div里
    document.getElementsByClassName("suhua-notyn-carrier").item(0).appendChild(div);

    //判断是否存在本次创建的信息弹窗--执行显示指定时间后自动删除
    if (document.getElementById("sh-notyno-" + numsg)) {
        //存在则开始延迟,定时信息框只显示5秒
        window.setTimeout(function () {
            //5秒时间到将信息框class切换为退场动画开始退场
            if (document.getElementById("sh-notyno-" + numsg)) {
                document.getElementById("sh-notyno-" + numsg).className += ' sh_move_close';
                //退场后再延迟250毫秒，随后删除整个信息框div元素
                window.setTimeout(function () {
                    //弹窗信息删除前关闭全屏遮罩,删除的前提是必须由打开的弹窗删除
                    if (maskok == "ok") {
                        if (maskstate != "none") {
                            document.getElementById(masksname).style.display="none";
                        }
                    }
                    //取出本次创建的信息框div的id
                    var rem = document.getElementById("sh-notyno-" + numsg);
                    //获取到本次创建的信息框元素后执行删除
                    if (rem != null) {
                        rem.remove();//按顺序删除
                    }
                }, 250);
            }
        }, 5000);
    }

}





/* ###################################################################################### */





//创建警告提示
function warnpop(text,maskok) {

    //判断是否有正在显示的遮罩,如果有则先隐藏
    if (maskstate != "none") {
        document.getElementById(masksname).style.display="none";
    }
    //判断需不需要遮罩,需要则显示
    if (maskok == "ok") {
        document.getElementById(masksname).style.display="flex";
    }

    delclose();//创建前先删除所有的加载提示弹窗

    //创建一个div作为整个提示信息的框架
    var div = document.createElement("div");
    //为div创建属性class = "notyn"
    var divattr = document.createAttribute("class");
    //class命名为notyn 并加上入场动画
    divattr.value = "notyn sh_move_open";
    //把属性class = "notyn"添加到div
    div.setAttributeNode(divattr);

    //为div创建id属性
    //生成随机id
    var numsg = getRandomAlphaNum(32);//生成随机数id
    //为div创建属性id
    var divattr = document.createAttribute("id");
    //id命名为sh-notyno- 加随机数
    divattr.value = "sh-notyno-" + numsg;
    //把属性id添加到div
    div.setAttributeNode(divattr);

    //为div创建onclick事件
    var divattr = document.createAttribute("onclick");
    //id命名为closetheck
    divattr.value = "closetheck()";
    //把属性事件添加到div
    div.setAttributeNode(divattr);







    //创建一个i标签
    var i = document.createElement("i");
    //给i标签添加class
    var divattr = document.createAttribute("class");
    //i标签class命名为notyn-icon 并设置图标iconfont icon-xinxi1
    divattr.value = "notyn-icon iconfont-sh icon-xinxi";
    //将class属性添加到i标签
    i.setAttributeNode(divattr);

    //创建一个span标签
    var span = document.createElement("span");
    //为span标签添加文本内容
    span.innerText = text;//内容为变量text传过来的值
    //给span标签添加class
    var divattr = document.createAttribute("class");
    //span标签class命名为notyn-span
    divattr.value = "notyn-span";
    //将class属性添加到i标签
    span.setAttributeNode(divattr);


    //创建一hello,world个文本节点
    //var text = document.createTextNode("hello,world");

    //将按钮和文本节点追加到div
    div.appendChild(i);
    div.appendChild(span);
    //div.appendChild(text);

    //为div添加样式
    var style = document.createAttribute("style");
    div.setAttributeNode(style);
    //添加背景颜色：黄色警告渐变色
    div.style.background = "linear-gradient(90deg,rgba(255,170,70,0.8),rgba(250,155,15,0.9))";
    /*var style = document.createAttribute("style");
    div.setAttributeNode(style);
    div.style.backgroundColor = "#F00";
    div.style.borderWidth = "20px";
    div.style.borderColor = "#000";
    div.style.width = "500px";
    div.style.height = "500px";
    div.style.marginLeft = "30%";
    div.style.marginTop = "1%";*/

    //把div追加到class名为suhua-notyn-carrier的div里
    document.getElementsByClassName("suhua-notyn-carrier").item(0).appendChild(div);

    //判断是否存在本次创建的信息弹窗--执行显示指定时间后自动删除
    if (document.getElementById("sh-notyno-" + numsg)) {
        //存在则开始延迟,定时信息框只显示5秒
        window.setTimeout(function () {
            //5秒时间到将信息框class切换为退场动画开始退场
            if (document.getElementById("sh-notyno-" + numsg)) {
                document.getElementById("sh-notyno-" + numsg).className += ' sh_move_close';
                //退场后再延迟250毫秒，随后删除整个信息框div元素
                window.setTimeout(function () {
                    //弹窗信息删除前关闭全屏遮罩,删除的前提是必须由打开的弹窗删除
                    if (maskok == "ok") {
                        if (maskstate != "none") {
                            document.getElementById(masksname).style.display="none";
                        }
                    }
                    //取出本次创建的信息框div的id
                    var rem = document.getElementById("sh-notyno-" + numsg);
                    //获取到本次创建的信息框元素后执行删除
                    if (rem != null) {
                        rem.remove();//按顺序删除
                    }
                }, 250);
            }
        }, 5000);
    }

}





/* ###################################################################################### */





//创建加载中提示
function loadpop(text,maskok) {

    //判断是否有正在显示的遮罩,如果有则先隐藏
    if (maskstate != "none") {
        document.getElementById(masksname).style.display="none";
    }
    //判断需不需要遮罩,需要则显示
    if (maskok == "ok") {
        document.getElementById(masksname).style.display="flex";
    }

    //创建一个div作为整个提示信息的框架
    var div = document.createElement("div");
    //为div创建属性class = "notyn"
    var divattr = document.createAttribute("class");
    //class命名为notyn 并加上入场动画
    divattr.value = "notyn sh_move_open";
    //把属性class = "notyn"添加到div
    div.setAttributeNode(divattr);

    //为div创建id属性
    //生成随机id
    var numsg = getRandomAlphaNum(32);//生成随机数id
    //为div创建属性id
    var divattr = document.createAttribute("id");
    //id命名为sh-notyno- 加随机数
    divattr.value = "sh-notyn-load-" + numsg;
    //把属性id添加到div
    div.setAttributeNode(divattr);

    //为div创建name属性
    //定义name的名字
    var numsg2 = "sh-notyn-load";//设置name
    //为div创建属性name
    var divattr = document.createAttribute("name");
    //name命名为sh-notyn-load
    divattr.value = numsg2;
    //把属性sh-notyn-load添加到div
    div.setAttributeNode(divattr);

    //为div创建onclick属性
    var divattr = document.createAttribute("onclick");
    //id命名为sh-notyno- 加随机数
    divattr.value = "closetheck()";
    //把属性id添加到div
    div.setAttributeNode(divattr);







    //创建一个i标签
    var i = document.createElement("i");
    //给i标签添加class
    var divattr = document.createAttribute("class");
    //i标签class命名为notyn-icon 并设置图标iconfont icon-xinxi1
    divattr.value = "sh-notyn-load";
    //将class属性添加到i标签
    i.setAttributeNode(divattr);

    //创建一个span标签
    var span = document.createElement("span");
    //为span标签添加文本内容
    span.innerText = text;//内容为变量text传过来的值
    //给span标签添加class
    var divattr = document.createAttribute("class");
    //span标签class命名为notyn-span
    divattr.value = "notyn-span";
    //将class属性添加到i标签
    span.setAttributeNode(divattr);


    //创建一hello,world个文本节点
    //var text = document.createTextNode("hello,world");

    //将按钮和文本节点追加到div
    div.appendChild(i);
    div.appendChild(span);
    //div.appendChild(text);

    //为div添加样式
    var style = document.createAttribute("style");
    div.setAttributeNode(style);
    //添加背景颜色：黄色警告渐变色
    div.style.background = "linear-gradient(90deg,rgba(255,170,70,0.8),rgba(250,155,15,0.9))";
    /*var style = document.createAttribute("style");
    div.setAttributeNode(style);
    div.style.backgroundColor = "#F00";
    div.style.borderWidth = "20px";
    div.style.borderColor = "#000";
    div.style.width = "500px";
    div.style.height = "500px";
    div.style.marginLeft = "30%";
    div.style.marginTop = "1%";*/

    //把div追加到class名为suhua-notyn-carrier的div里
    document.getElementsByClassName("suhua-notyn-carrier").item(0).appendChild(div);

    //判断是否存在本次创建的信息弹窗--执行显示指定时间后自动删除--加载中提示不自动删除,如果需要自动删除功能 取消下面的注释即可
    /* if (document.getElementById("sh-notyn-load-" + numsg)) {
        //存在则开始延迟,定时信息框只显示5秒
    window.setTimeout(function () {
        //5秒时间到将信息框class切换为退场动画开始退场
        if (document.getElementById("sh-notyn-load-" + numsg)) {
            document.getElementById("sh-notyn-load-" + numsg).className += ' sh_move_close';
            //退场后再延迟250毫秒，随后删除整个信息框div元素
            window.setTimeout(function () {
                //弹窗信息删除前关闭全屏遮罩,删除的前提是必须由打开的弹窗删除
                    if (maskok == "ok") {
                        if (maskstate != "none") {
                            document.getElementById(masksname).style.display="none";
                        }
                    }
                //取出本次创建的信息框div的id
                var rem = document.getElementById("sh-notyn-load-" + numsg);
                //获取到本次创建的信息框元素后执行删除
                if (rem != null) {
                    rem.remove();//按顺序删除
                }
            }, 250);
        }
    }, 5000);
    } */

}
