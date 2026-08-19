/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */
// Header state on scroll
window.onscroll = function () {
    Sanqi.scrollHeader({
        activeClasses: {
            'top-left-1': 'iconfont icon-weibiaoti al-sxbh',
            'top-right-1': 'iconfont icon-gengduo al-sxbh'
        },
        normalClasses: {
            'top-left-1': 'iconfont icon-weibiaoti al-sxb',
            'top-right-1': 'iconfont icon-gengduo al-sxb'
        },
        titleColorId: 'setup-view-title'
    });
};
/* Send comment on detail page */
function fasongv() {
    var id = Sanqi.id("sh-tieid") ? Sanqi.id("sh-tieid").innerText : "";
    var tid = "sh-zanp-pl-" + id;
    var textInput = Sanqi.id("bletext");
    if (!textInput || textInput.value == "") {
        warnpop("\u8bf7\u8f93\u5165\u8bc4\u8bba\u5185\u5bb9");
        return;
    }

    var user_id = getCookie("username");
    var user_passid = getCookie("passid");
    var vis_name = "";
    var vis_email = "";
    var vis_url = "";
    if (Sanqi.id("sh-plk-yk")) {
        vis_name = Sanqi.id("vis_name").value;
        vis_email = Sanqi.id("vis_email").value;
        vis_url = Sanqi.id("vis_url").value;
    }

    if (user_id == "" || user_passid == "") {
        if (!Sanqi.id("sh-plk-yk")) {
            warnpop("\u8bf7\u5148\u767b\u5f55");
            return;
        }
        if (vis_name == "" && vis_email == "") {
            ykkg();
            return;
        }
        if (vis_name == "") {
            warnpop("\u8bf7\u8f93\u5165\u6635\u79f0");
            return;
        } else if (vis_email == "") {
            warnpop("\u8bf7\u8f93\u5165\u90ae\u7bb1");
            return;
        }
        if (!vis_email.match(/^\w+@\w+\.\w+$/i)) {
            warnpop("\u90ae\u7bb1\u683c\u5f0f\u4e0d\u6b63\u786e");
            return;
        }
        if (vis_url != "") {
            var urlPattern = /^(https?:\/\/)?([\w.-]+)\.([a-z]{2,})(\/\S*)?$/i;
            if (!urlPattern.test(vis_url)) {
                warnpop("\u7f51\u5740\u683c\u5f0f\u4e0d\u6b63\u786e");
                return;
            }
        }
    }
    if ((user_id == "" || user_passid == "") && Sanqi.id("sh-plk-yk") && typeof _saveVisitorCommentIdentity === "function") {
        _saveVisitorCommentIdentity(vis_name, vis_email, vis_url);
    }

    var tieid = Sanqi.id("sh-tieid").innerText;
    var tiehf = Sanqi.id("sh-tiehf").innerText;
    var tieea = Sanqi.id("sh-tieea").innerText;
    var pltext = textInput.value;

    loadpop("\u6b63\u5728\u53d1\u9001\u8bc4\u8bba,\u8bf7\u7a0d\u540e...", "ok");
    Sanqi.apiPost('/api/comment/submit', {
        tieid: tieid,
        tiehf: tiehf,
        tieea: tieea,
        pltext: pltext,
        vis_name: vis_name,
        vis_email: vis_email,
        vis_url: vis_url
    }, function(obj){
        if (!obj || !obj.code) {
            errorpop("\u672a\u83b7\u53d6\u5230\u6570\u636e");
            return;
        }

        var code = obj.code;
        var msg = obj.msg || "";
        var d = obj.data || {};

        if (code == 200 || code == '200') {
            plkgb();
            _clearDraft(id);
            if (msg.indexOf('\u7b49\u5f85\u5ba1\u6838') > -1) {
                warnpop(msg);
                return;
            }

            var commentList = Sanqi.id(tid);
            if (commentList && commentList.style.display == "none") {
                commentList.style.display = "block";
                var commentBar = Sanqi.id("sh-dz-z-" + id);
                if (commentBar) {
                    commentBar.style.display = "flex";
                }
            }

            if (d.html) {
                $("#" + tid).append(d.html);
            }
            successpop(msg || "\u8bc4\u8bba\u53d1\u9001\u6210\u529f");
            loaddemand();
        } else if (msg.indexOf('\u5ba1\u6838') > -1) {
            warnpop(msg);
            plkgb();
        } else {
            errorpop(msg);
        }
    }, function(res){
        errorpop((res && res.msg) || "\u8bc4\u8bba\u53d1\u9001\u5931\u8d25,\u8bf7\u7a0d\u540e\u91cd\u8bd5");
    });
}





/* Like button event */
function _removeDetailGuestLike(likeList) {
    var guest = Sanqi.id("fkzan-zan");
    if (!guest || !likeList) return;
    var span = guest.querySelector("span");
    var text = span ? span.textContent : "";
    var match = text.match(/\d+/);
    var count = match ? parseInt(match[0], 10) : 1;
    if (isNaN(count) || count <= 1) {
        guest.remove();
        return;
    }
    count--;
    if (span) {
        span.textContent = text.replace(/\d+/, String(count));
    }
}

function dinazanv() {
    var id = Sanqi.id("sh-tieid") ? Sanqi.id("sh-tieid").innerText : "";
    if (!id || id == "-") {
        var target = window.event && window.event.target ? window.event.target : null;
        var action = target && target.closest ? target.closest("[data-cid]") : null;
        if (action) {
            id = action.getAttribute("data-cid") || action.id || "";
        }
    }
    if ((!id || id == "-") && document.querySelector("[id^='tiezdz-']")) {
        id = document.querySelector("[id^='tiezdz-']").id.replace("tiezdz-", "");
    }
    var user_id = getCookie("username");
    var user_passid = getCookie("passid");
    var likeText = Sanqi.id("tiezdz-" + id);
    var likeIcon = Sanqi.id("tiezimg-" + id);
    var likeAction = likeIcon && likeIcon.closest ? likeIcon.closest(".wx-bottom-actions button") : null;
    var likeBox = Sanqi.id("zans-" + id);
    var likeList = Sanqi.id("sh-zanp-ul");
    function setLikeIconClass() {
        var nextClass = "wx-bottom-icon wx-like-icon";
        if (likeIcon.setAttribute) {
            likeIcon.setAttribute("class", nextClass);
        } else {
            likeIcon.className = nextClass;
        }
    }

    if (!id || !likeText || !likeIcon || !likeList) {
        warnpop("\u672a\u83b7\u53d6\u5230\u6587\u7ae0\u4fe1\u606f");
        return;
    }

    var isLike = likeText.innerText == "\u8d5e";
    var isGuest = user_id == "" || user_passid == "";

    var zts = isLike ? 0 : -1;
    var visitorProfile = isGuest && typeof _getVisitorCommentIdentity === "function"
        ? _getVisitorCommentIdentity()
        : { name: "", email: "", url: "" };
    loadpop(isLike ? "\u6b63\u5728\u70b9\u8d5e,\u8bf7\u7a0d\u540e..." : "\u6b63\u5728\u53d6\u6d88\u70b9\u8d5e,\u8bf7\u7a0d\u540e...", "ok");
    Sanqi.apiPost('/api/like/toggle', {
        tieid: id,
        user_id: user_id,
        zts: zts,
        vis_name: visitorProfile.name,
        vis_email: visitorProfile.email,
        vis_url: visitorProfile.url
    }, function(obj){
        if (!obj || !obj.code) {
            errorpop("\u672a\u83b7\u53d6\u5230\u6570\u636e");
            return;
        }
        var code = obj.code;
        var msg = obj.msg;
        var data = obj.data || {};
        var img = data.img || "/assets/img/tx.png";
        var name = data.name || msg;
        var likeUserId = data.user || user_id;

        if (code != 200 && code != '200') {
            warnpop(msg || "\u8bf7\u6c42\u5931\u8d25,\u8bf7\u7a0d\u540e\u91cd\u8bd5");
            return;
        }

        if (isLike) {
            if (likeBox && likeBox.style.display == "none") {
                likeBox.style.display = "flex";
            }
            if (isGuest && !data.profiled) {
                if (!Sanqi.id("fkzan-zan")) {
                    var guestLi = '<li id="fkzan-zan" style="width: fit-content;height: 30px;margin-right: 5px;margin-bottom: 5px;display: flex;align-items: center;"><span style="font-size: 12px;color: var(--adgg);max-width: 100px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;"></span></li>';
                    $("#sh-zanp-ul").append(guestLi);
                }
                var guest = Sanqi.id("fkzan-zan");
                var span = guest ? guest.querySelector("span") : null;
                if (span) {
                    var match = span.textContent.match(/\d+/);
                    var count = match ? parseInt(match[0], 10) : 0;
                    if (isNaN(count) || count < 0) count = 0;
                    span.textContent = (count + 1) + "\u4f4d\u8bbf\u5ba2";
                }
            } else {
                var li = '<li id="zan-' + likeUserId + '" title="' + name + '"><img src="/assets/img/thumbnail.svg" data-src="' + img + '" data-fallback="/assets/img/tx.png" alt="' + name + '"></li>';
                if (Sanqi.id("fkzan-zan")) {
                    $("#sh-zanp-ul li").eq(-1).before(li);
                } else {
                    $("#sh-zanp-ul").append(li);
                }
            }
            likeText.innerText = "\u53d6\u6d88";
            likeText.setAttribute("data-like-name", name);
            likeText.setAttribute("data-like-user", likeUserId || "");
            setLikeIconClass();
            if (likeAction) likeAction.classList.add("wx-liked");
            successpop("\u70b9\u8d5e\u6210\u529f");
            loaddemand();
        } else {
            var unlikeUserId = data.user || likeText.getAttribute("data-like-user") || user_id || "";
            var item = Sanqi.id("zan-" + unlikeUserId);
            if (item) item.remove();
            if (isGuest && !data.profiled && !item) {
                _removeDetailGuestLike(likeList);
            }
            var count = likeList.getElementsByTagName('li').length;
            if (count <= 0 && likeBox) {
                likeBox.style.display = "none";
            }
            likeText.innerText = "\u8d5e";
            likeText.removeAttribute("data-like-name");
            likeText.removeAttribute("data-like-user");
            setLikeIconClass();
            if (likeAction) likeAction.classList.remove("wx-liked");
            successpop("\u70b9\u8d5e\u53d6\u6d88");
        }
    }, function(res){
        warnpop((res && res.msg) || "\u8bf7\u6c42\u5931\u8d25,\u8bf7\u7a0d\u540e\u91cd\u8bd5");
    });

    var ids = "pl-" + id;
    var menu = Sanqi.id(ids);
    if (menu && menu.style.display != "none") {
        var arrs = document.getElementsByName("pl");
        for (var i = 0; i < arrs.length; i++) {
            if (Sanqi.id(arrs[i].id)) {
                Sanqi.id(arrs[i].id).style = "display: none";
            }
        }
    }
}








//删除文章事件
  function delewenz(el){
    if (!confirm("\u786e\u5b9a\u8981\u5220\u9664\u6b64\u6587\u7ae0\u5417?")) {
        return;
    }

    if (!el || !el.id) {
        warnpop("\u672a\u83b7\u53d6\u5230\u6587\u7ae0\u4fe1\u606f");
        return;
    }
    var eles = el.id.replace("sh-delewza-", "");

    loadpop("\u6b63\u5728\u5220\u9664\u6587\u7ae0,\u8bf7\u7a0d\u540e...", "ok");
    Sanqi.apiPost('/api/article/delete', { wzdid: eles }, function(res){
        if (!res || !res.code) {
            errorpop("\u672a\u83b7\u53d6\u5230\u6570\u636e");
            return;
        }
        if (res.code == '200') {
            successpop("\u5df2\u5220\u9664\u8be5\u6587\u7ae0");
            window.history.back(-1);
        } else {
            errorpop(res.msg);
        }
    }, function(res){
        errorpop((res && res.msg) || "\u5220\u9664\u5931\u8d25,\u8bf7\u7a0d\u540e\u91cd\u8bd5");
    });
}




//删除评论
  function pldels(Obj){
    var evt = window.event;
    if (evt && evt.stopPropagation) {
        evt.stopPropagation();
    }

    if (!confirm("\u786e\u5b9a\u8981\u5220\u9664\u6b64\u8bc4\u8bba\u5417?")) {
        return;
    }

    var target = evt ? (evt.target || evt.srcElement) : Obj;
    if (!target || !target.id) {
        warnpop("\u672a\u83b7\u53d6\u5230\u8bc4\u8bba\u4fe1\u606f");
        return;
    }
    var eleecid = target.id;
    var elewidl = target.lang;
    var liswd = elewidl.replace("yswid-", "sh-zanp-pl-");
    var ulmli = liswd;

    loadpop("\u6b63\u5728\u5220\u9664\u8bc4\u8bba,\u8bf7\u7a0d\u540e...", "ok");
    Sanqi.apiPost('/api/comment/delete', { plid: eleecid }, function(res){
        if (!res || !res.code) {
            errorpop("\u672a\u83b7\u53d6\u5230\u6570\u636e");
            return;
        }
        if (res.code == '200') {
            if (Obj && Obj.parentNode && Obj.parentNode.parentNode) {
                Obj.parentNode.parentNode.removeChild(Obj.parentNode);
            }

            var list = document.getElementById(liswd);
            var lisew = list ? list.getElementsByTagName('li').length : 0;
            if (lisew <= 0) {
                var ul = document.getElementById(ulmli);
                if (ul) ul.style.display = "none";
                var dz = document.querySelector(".sh-dz-z");
                if (dz) dz.style.display = "none";
            }

            successpop("\u5df2\u5220\u9664\u8be5\u8bc4\u8bba");
        } else {
            warnpop(res.msg);
        }
    }, function(res){
        warnpop((res && res.msg) || "\u5220\u9664\u5931\u8d25,\u8bf7\u7a0d\u540e\u91cd\u8bd5");
    });
}


//打开设置弹窗层
function viewsetk(){
    //显示最外层
    document.getElementById("sh-view-set").style.display="flex";
    //给弹窗菜单设置从下往上出现的动画
    document.getElementById("sh-view-set-wk-con").style.animation = "move_4 0.2s";
}
//关闭设置弹窗层
function viewsetg(evt){
    evt = evt || window.event;
    if (evt && evt.stopPropagation) {
        evt.stopPropagation();//禁止冒泡
    }
    //让弹窗背景遮罩层淡出
    document.getElementById('sh-view-set-wk').style.transition = 'opacity 0.25s';
    document.getElementById('sh-view-set-wk').style.opacity = '0';
    //给给弹窗菜单设置从上往下的退出动画
    document.getElementById("sh-view-set-wk-con").style.animation = "move_4t 0.2s";

        let throttleTimer; // 声明用于节流的定时器变量
        function throttleFunction() {//定时器中要执行的代码
          if (document.getElementById("sh-view-set-wk-con")) { // 如果名为此id的div存在才执行
            //弹窗关闭后恢复所有的变动
            document.getElementById("sh-view-set").style.display = "none";
            document.getElementById('sh-view-set-wk').style.opacity = "";
            document.getElementById("sh-view-set-wk-con").removeAttribute("style");
            document.getElementById("sh-view-set-wk").removeAttribute("style");
          }
        }
        function throttle() {
          clearTimeout(throttleTimer); // 清除上一次的节流定时器
          throttleTimer = setTimeout(throttleFunction, 200); // 创建新的节流定时器
        }
        // 调用throttle函数来触发节流逻辑
        throttle();


}

if (document.getElementById('sh-view-set-wk-con')) {
  // 如果元素存在，则给它绑定事件
  document.getElementById('sh-view-set-wk-con').addEventListener('click', function(event) {
    event.stopPropagation();
  });
}



//文章私密锁定与解除
function wzsdyj(obj){
    var evt = window.event || {};
    var target = obj || evt.target || evt.srcElement || null;
    var item = target && target.closest ? target.closest('[id^="sh-wzdsdzt-"]') : target;
    if (!item || !item.id) {
        warnpop("\u672a\u83b7\u53d6\u5230\u6587\u7ae0\u4fe1\u606f");
        return;
    }

    var ele = item.id;
    var eles = ele.replace("sh-wzdsdzt-", "");
    var eleid = item.lang;
    var next = eleid == 0
        ? { ztwid: 0, lang: "1", text: "\u53d6\u6d88\u9690\u79c1", msg: "\u6587\u7ae0\u5df2\u8bbe\u4e3a\u9690\u79c1" }
        : { ztwid: 1, lang: "0", text: "\u8bbe\u4e3a\u9690\u79c1", msg: "\u6587\u7ae0\u5df2\u8bbe\u4e3a\u516c\u5f00" };

    loadpop("\u4fee\u6539\u79c1\u5bc6\u4e2d,\u8bf7\u7a0d\u540e...", "ok");
    Sanqi.apiPost('/api/article/privacy', { ztys: eles, ztwid: next.ztwid }, function(res){
        if (!res || !res.code) {
            errorpop("\u672a\u83b7\u53d6\u5230\u6570\u636e");
            return;
        }
        if (res.code == '200') {
            item.lang = next.lang;
            var label = item.getElementsByTagName('span')[0];
            if (label) label.innerText = next.text;
            successpop(next.msg);
            viewsetg();
        } else {
            warnpop(res.msg);
        }
    }, function(res){
        warnpop((res && res.msg) || "\u8bf7\u6c42\u5931\u8d25,\u8bf7\u7a0d\u540e\u91cd\u8bd5");
    });
}



//文章置顶与取消
function wzszd(obj){
    var evt = window.event || {};
    var target = obj || evt.target || evt.srcElement || null;
    var item = target && target.closest ? target.closest('[id^="sh-wzzdyqx-"]') : target;
    if (!item || !item.id) {
        warnpop("\u672a\u83b7\u53d6\u5230\u6587\u7ae0\u4fe1\u606f");
        return;
    }

    var ele = item.id;
    var eles = ele.replace("sh-wzzdyqx-", "");
    var eleid = item.lang;
    var next = eleid == "sw"
        ? { lx: "sw", lang: "qx", text: "\u53d6\u6d88\u7f6e\u9876", loading: "\u6b63\u5728\u7f6e\u9876\u6587\u7ae0", msg: "\u5df2\u8bbe\u4e3a\u7f6e\u9876" }
        : { lx: "qx", lang: "sw", text: "\u7f6e\u9876", loading: "\u6b63\u5728\u53d6\u6d88\u7f6e\u9876", msg: "\u5df2\u53d6\u6d88\u7f6e\u9876" };

    loadpop(next.loading, "ok");
    Sanqi.apiPost('/api/article/pin', { wid: eles, lx: next.lx }, function(res){
        if (!res || !res.code) {
            errorpop("\u672a\u83b7\u53d6\u5230\u6570\u636e");
            return;
        }
        if (res.code == '200') {
            item.lang = next.lang;
            var label = item.getElementsByTagName('span')[0];
            if (label) label.innerText = next.text;
            successpop(next.msg);
            viewsetg();
        } else {
            warnpop(res.msg);
        }
    }, function(res){
        warnpop((res && res.msg) || "\u8bf7\u6c42\u5931\u8d25,\u8bf7\u7a0d\u540e\u91cd\u8bd5");
    });
}

//用户个人置顶与取消
function wzszd_user(obj){
    var evt = window.event || {};
    var target = obj || evt.target || evt.srcElement || null;
    var item = target && target.closest ? target.closest('[id^="sh-wzyhd-"]') : target;
    if (!item || !item.id) {
        warnpop("未获取到文章信息");
        return;
    }

    var ele = item.id;
    var eles = ele.replace("sh-wzyhd-", "");
    var eleid = item.lang;
    var next = eleid == "sw"
        ? { lx: "sw", lang: "qx", text: "取消个人置顶", loading: "正在置顶文章", msg: "已设为个人置顶" }
        : { lx: "qx", lang: "sw", text: "个人置顶", loading: "正在取消置顶", msg: "已取消个人置顶" };

    loadpop(next.loading, "ok");
    Sanqi.apiPost('/api/article/user-pin', { wid: eles, lx: next.lx }, function(res){
        if (!res || !res.code) {
            errorpop("未获取到数据");
            return;
        }
        if (res.code == '200') {
            item.lang = next.lang;
            var label = item.getElementsByTagName('span')[0];
            if (label) label.innerText = next.text;
            successpop(next.msg);
            viewsetg();
        } else {
            warnpop(res.msg);
        }
    }, function(res){
        warnpop((res && res.msg) || "请求失败,请稍后重试");
    });
}

// ========== 分享功能 ==========
var shareData = {
    title: '',
    url: '',
    image: '',
    desc: ''
};

function openSharePanel(el) {
    var cid = el.getAttribute('data-cid');
    var container = document.getElementById('sh-content-' + cid);
    if (container && container.classList && container.classList.contains('wx-bottom-bar')) {
        container = document.querySelector('.wx-article-page') || container;
    }
    if (!container) {
        container = el.closest('.sh-content') || el.closest('.sh-article-actions') || el.closest('.wx-article-page');
    }
    var titleEl = container ? container.querySelector('.sh-content-right-head-title p, .sh-article-title h1, .wx-article-title') : null;
    var textEl = container ? container.querySelector('.sh-content-right-head > span, .sh-article-body, .wx-article-content') : null;
    var imgEl = container ? container.querySelector('.sh-content-right-img img, .sh-article-cover img, .wx-article-cover img') : null;

    shareData.title = titleEl ? titleEl.textContent.trim() : document.title;
    shareData.desc = textEl ? textEl.textContent.trim().substring(0, 80) : '';
    shareData.url = window.location.origin + '/view/' + cid;
    shareData.image = imgEl ? (imgEl.getAttribute('data-src') || imgEl.src) : '';

    // 先关闭设置弹窗
    viewsetg();

    // 延迟显示分享面板，等待设置弹窗关闭动画完成
    setTimeout(function() {
        document.getElementById('sharePanel').style.display = 'flex';
    }, 200);
}

function closeSharePanel() {
    document.getElementById('sharePanel').style.display = 'none';
}

function shareToWechat() {
    closeSharePanel();
    var modal = document.getElementById('wechatModal');
    modal.style.display = 'flex';
    generateQRCode('qrCanvas', shareData.url, 200);
}

function generateQRCode(canvasId, url, size, callback) {
    var container = document.getElementById(canvasId);
    if (!container) {
        console.error('Container element not found:', canvasId);
        if (callback) callback(false);
        return;
    }

    function tryGenerate(retries) {
        if (typeof QRCode !== 'undefined') {
            // 清空容器
            container.innerHTML = '';
            try {
                new QRCode(container, {
                    text: url,
                    width: size,
                    height: size,
                    colorDark: '#333333',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.M
                });
                if (callback) callback(true);
            } catch(err) {
                console.error('QR code generation failed:', err);
                drawQRPlaceholder(container, size);
                if (callback) callback(false);
            }
        } else if (retries > 0) {
            setTimeout(function() { tryGenerate(retries - 1); }, 200);
        } else {
            console.error('QRCode library not loaded');
            drawQRPlaceholder(container, size);
            if (callback) callback(false);
        }
    }

    tryGenerate(10);
}

function drawQRPlaceholder(container, size) {
    container.innerHTML = '<div style="width:' + size + 'px;height:' + size + 'px;border:2px solid #ddd;display:flex;align-items:center;justify-content:center;color:#999;font-size:14px;">二维码</div>';
}

function shareToWeibo() {
    var url = 'https://service.weibo.com/share/share.php?' +
        'title=' + encodeURIComponent(shareData.title + ' ' + shareData.desc) +
        '&url=' + encodeURIComponent(shareData.url) +
        '&pic=' + encodeURIComponent(shareData.image);
    window.open(url, '_blank', 'width=600,height=500');
    closeSharePanel();
}

function shareToQQ() {
    var url = 'https://connect.qq.com/widget/shareqq/index.html?' +
        'title=' + encodeURIComponent(shareData.title) +
        '&desc=' + encodeURIComponent(shareData.desc) +
        '&url=' + encodeURIComponent(shareData.url) +
        '&pics=' + encodeURIComponent(shareData.image);
    window.open(url, '_blank', 'width=600,height=500');
    closeSharePanel();
}

function copyShareLink() {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(shareData.url).then(function() {
            successpop('链接已复制');
        });
    } else {
        var input = document.createElement('input');
        input.value = shareData.url;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        successpop('链接已复制');
    }
    closeSharePanel();
}

function closeWechatModal() {
    document.getElementById('wechatModal').style.display = 'none';
}

var posterRendering = false;
var POSTER_W = 400;
var POSTER_H = 667;
var POSTER_PAD = 16;
var POSTER_R = 18;
var HERO_H = 519;

function generatePoster() {
    closeSharePanel();
    document.getElementById('posterModal').style.display = 'flex';

    var canvas = document.getElementById('posterCanvas');
    var ctx = canvas.getContext('2d');
    var dpr = window.devicePixelRatio || 2;

    canvas.width = POSTER_W * dpr;
    canvas.height = POSTER_H * dpr;
    ctx.scale(dpr, dpr);

    posterRendering = true;

    var now = new Date();
    var months = ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'];
    var day = String(now.getDate());
    var monthYear = months[now.getMonth()] + '.' + now.getFullYear();

    function finalizePoster() {
        drawHeroOverlay(ctx, day, monthYear);
        drawPosterFooter(ctx);
    }

    // 白色背景 + 外层圆角容器
    ctx.fillStyle = '#ffffff';
    roundRect(ctx, 0, 0, POSTER_W, POSTER_H, 22);
    ctx.fill();

    // 绘制 Hero 区域背景
    if (shareData.image) {
        var img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = function() {
            drawHeroImage(ctx, img);
            finalizePoster();
        };
        img.onerror = function() {
            drawHeroGradient(ctx);
            finalizePoster();
        };
        img.src = shareData.image;
    } else if (window.__POSTER_RANDOM_API__) {
        fetchRandomImage(function(url) {
            if (url) {
                var img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = function() {
                    drawHeroImage(ctx, img);
                    finalizePoster();
                };
                img.onerror = function() {
                    drawHeroGradient(ctx);
                    finalizePoster();
                };
                img.src = url;
            } else {
                drawHeroGradient(ctx);
                finalizePoster();
            }
        });
    } else {
        drawHeroGradient(ctx);
        finalizePoster();
    }
}

function fetchRandomImage(callback) {
    var apiUrl = window.__POSTER_RANDOM_API__;
    var sep = apiUrl.indexOf('?') > -1 ? '&' : '?';
    apiUrl += sep + '_=' + Date.now();

    fetch(apiUrl, { cache: 'no-store' }).then(function(r) {
        if (!r.ok) throw new Error('random api status ' + r.status);
        var ct = r.headers.get('content-type') || '';
        if (ct.indexOf('json') > -1) {
            return r.json().then(function(json) {
                var url = json.url || json.image || json.src || '';
                callback(url);
            });
        }
        // 接口直接返回图片（重定向或二进制流），使用原始 URL
        callback(apiUrl);
    }).catch(function() {
        callback('');
    });
}

// 绘制 Hero 图片（带圆角裁剪）
function drawHeroImage(ctx, img) {
    ctx.save();
    roundRect(ctx, POSTER_PAD, POSTER_PAD, POSTER_W - POSTER_PAD * 2, HERO_H, POSTER_R);
    ctx.clip();
    ctx.drawImage(img, POSTER_PAD, POSTER_PAD, POSTER_W - POSTER_PAD * 2, HERO_H);
    ctx.restore();
}

// 无封面图时的默认渐变 Hero
function drawHeroGradient(ctx) {
    ctx.save();
    roundRect(ctx, POSTER_PAD, POSTER_PAD, POSTER_W - POSTER_PAD * 2, HERO_H, POSTER_R);
    ctx.clip();
    var gradient = ctx.createLinearGradient(POSTER_PAD, POSTER_PAD, POSTER_W - POSTER_PAD, POSTER_PAD + HERO_H);
    gradient.addColorStop(0, '#7b90ff');
    gradient.addColorStop(1, '#b35fff');
    ctx.fillStyle = gradient;
    ctx.fillRect(POSTER_PAD, POSTER_PAD, POSTER_W - POSTER_PAD * 2, HERO_H);
    ctx.restore();
}

// 绘制 Hero 叠加层：渐变遮罩 + 日期 + 标题 + 摘要
function drawHeroOverlay(ctx, day, monthYear) {
    var hx = POSTER_PAD;
    var hy = POSTER_PAD;
    var hw = POSTER_W - POSTER_PAD * 2;
    var hh = HERO_H;

    // 底部渐变遮罩（让文字清晰可读）
    ctx.save();
    roundRect(ctx, hx, hy, hw, hh, POSTER_R);
    ctx.clip();
    var gradient = ctx.createLinearGradient(0, hy + hh * 0.4, 0, hy + hh);
    gradient.addColorStop(0, 'rgba(0,0,0,0)');
    gradient.addColorStop(0.6, 'rgba(0,0,0,0.15)');
    gradient.addColorStop(1, 'rgba(0,0,0,0.55)');
    ctx.fillStyle = gradient;
    ctx.fillRect(hx, hy, hw, hh);
    ctx.restore();

    // 日期角标（左上角白色卡片）
    var badgeX = hx + 22;
    var badgeY = hy + 18;
    ctx.save();
    ctx.shadowColor = 'rgba(0,0,0,0.12)';
    ctx.shadowBlur = 8;
    ctx.shadowOffsetY = 2;
    ctx.fillStyle = 'rgba(255,255,255,0.92)';
    roundRect(ctx, badgeX, badgeY, 70, 70, 8);
    ctx.fill();
    ctx.restore();

    // 日期数字
    ctx.fillStyle = '#111111';
    ctx.font = '800 28px -apple-system, "PingFang SC", sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(day, badgeX + 35, badgeY + 28);

    // 月份年份
    ctx.fillStyle = '#444444';
    ctx.font = '11px -apple-system, "PingFang SC", sans-serif';
    ctx.fillText(monthYear, badgeX + 35, badgeY + 52);

    // 标题（Hero 底部左侧）
    ctx.textAlign = 'left';
    ctx.textBaseline = 'alphabetic';
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 22px -apple-system, "PingFang SC", "Microsoft YaHei", sans-serif';
    ctx.shadowColor = 'rgba(0,0,0,0.35)';
    ctx.shadowBlur = 10;
    ctx.shadowOffsetY = 2;
    wrapText(ctx, shareData.title || document.title, hx + 22, hy + hh - 80, hw - 44, 31, 2);
    ctx.shadowColor = 'transparent';
    ctx.shadowBlur = 0;
    ctx.shadowOffsetY = 0;

    // 摘要
    if (shareData.desc) {
        ctx.fillStyle = 'rgba(255,255,255,0.95)';
        ctx.font = '14px -apple-system, "PingFang SC", "Microsoft YaHei", sans-serif';
        ctx.shadowColor = 'rgba(0,0,0,0.35)';
        ctx.shadowBlur = 10;
        ctx.shadowOffsetY = 2;
        wrapText(ctx, shareData.desc, hx + 22, hy + hh - 35, hw - 44, 21, 2);
        ctx.shadowColor = 'transparent';
        ctx.shadowBlur = 0;
        ctx.shadowOffsetY = 0;
    }
}

// 绘制底部区域：站点信息 + 二维码
function drawPosterFooter(ctx) {
    var footerY = POSTER_PAD + HERO_H + 18;
    var footerX = POSTER_PAD + 18;
    var footerW = POSTER_W - POSTER_PAD * 2 - 36;

    // 站点名称
    var siteNameEl = document.querySelector('.sh-copyright-banquan');
    var siteNameText = siteNameEl ? siteNameEl.textContent.trim() : '朋友圈';

    ctx.fillStyle = '#111111';
    ctx.font = '800 26px -apple-system, "PingFang SC", "Microsoft YaHei", sans-serif';
    ctx.textAlign = 'left';
    ctx.textBaseline = 'alphabetic';
    ctx.fillText(siteNameText, footerX, footerY + 26);

    // 站点简介
    ctx.fillStyle = '#555555';
    ctx.font = '14px -apple-system, "PingFang SC", "Microsoft YaHei", sans-serif';
    ctx.fillText('发现更多精彩内容', footerX, footerY + 52);

    // 二维码（右下角）
    var qrSize = 100;
    var qrX = POSTER_W - POSTER_PAD - 18 - qrSize;
    var qrY = footerY;

    ctx.strokeStyle = '#e0e0e0';
    ctx.lineWidth = 1;
    roundRect(ctx, qrX, qrY, qrSize, qrSize, 4);
    ctx.stroke();
    ctx.fillStyle = '#999999';
    ctx.font = '11px sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText('加载中...', qrX + qrSize / 2, qrY + qrSize / 2);

    if (typeof QRCode !== 'undefined') {
        var tempDiv = document.createElement('div');
        tempDiv.style.cssText = 'position:absolute;left:-9999px;top:-9999px;';
        document.body.appendChild(tempDiv);

        try {
            new QRCode(tempDiv, {
                text: shareData.url,
                width: qrSize * 2,
                height: qrSize * 2,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });

            setTimeout(function() {
                var qrImg = tempDiv.querySelector('img');
                if (qrImg && qrImg.src) {
                    var img = new Image();
                    img.onload = function() {
                        ctx.drawImage(img, qrX, qrY, qrSize, qrSize);
                        posterRendering = false;
                    };
                    img.onerror = function() {
                        drawQRPlaceholder(ctx, qrX, qrY, qrSize);
                        posterRendering = false;
                    };
                    img.src = qrImg.src;
                } else {
                    drawQRPlaceholder(ctx, qrX, qrY, qrSize);
                    posterRendering = false;
                }
                document.body.removeChild(tempDiv);
            }, 100);
        } catch(err) {
            console.error('QR code generation failed:', err);
            document.body.removeChild(tempDiv);
            drawQRPlaceholder(ctx, qrX, qrY, qrSize);
            posterRendering = false;
        }
    } else {
        drawQRPlaceholder(ctx, qrX, qrY, qrSize);
        posterRendering = false;
    }
}

function drawQRPlaceholder(ctx, x, y, size) {
    ctx.strokeStyle = '#e0e0e0';
    ctx.lineWidth = 1;
    roundRect(ctx, x, y, size, size, 4);
    ctx.stroke();
    ctx.fillStyle = '#cccccc';
    ctx.font = '14px sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText('二维码', x + size / 2, y + size / 2);
    posterRendering = false;
}

function closePosterModal() {
    document.getElementById('posterModal').style.display = 'none';
}

function savePoster() {
    if (posterRendering) {
        warnpop('海报生成中，请稍候...');
        return;
    }
    var canvas = document.getElementById('posterCanvas');
    var link = document.createElement('a');
    link.download = 'share-poster.png';
    link.href = canvas.toDataURL('image/png');
    link.click();
    successpop('海报已保存');
}

function wrapText(ctx, text, x, y, maxWidth, lineHeight, maxLines) {
    var words = text.split('');
    var line = '';
    var lineCount = 1;
    for (var i = 0; i < words.length; i++) {
        var testLine = line + words[i];
        var metrics = ctx.measureText(testLine);
        if (metrics.width > maxWidth && i > 0) {
            if (maxLines && lineCount >= maxLines) {
                var ellipsis = '...';
                while (ctx.measureText(line + ellipsis).width > maxWidth && line.length > 0) {
                    line = line.substring(0, line.length - 1);
                }
                ctx.fillText(line + ellipsis, x, y);
                return;
            }
            ctx.fillText(line, x, y);
            line = words[i];
            y += lineHeight;
            lineCount++;
        } else {
            line = testLine;
        }
    }
    ctx.fillText(line, x, y);
}

function roundRect(ctx, x, y, w, h, r) {
    ctx.beginPath();
    ctx.moveTo(x + r, y);
    ctx.lineTo(x + w - r, y);
    ctx.quadraticCurveTo(x + w, y, x + w, y + r);
    ctx.lineTo(x + w, y + h - r);
    ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
    ctx.lineTo(x + r, y + h);
    ctx.quadraticCurveTo(x, y + h, x, y + h - r);
    ctx.lineTo(x, y + r);
    ctx.quadraticCurveTo(x, y, x + r, y);
    ctx.closePath();
}

// ========== 投票功能 ==========
var pollSelections = {}; // 存储多选模式下的选择状态

function pollVote(pollId, optionIndex, pollType) {
    var container = document.getElementById('sh-poll-options-' + pollId);
    if (!container) return;
    if (container.getAttribute('data-voted') === '1') return;

    // 多选模式：切换选中状态，不立即提交
    if (pollType == 2) {
        if (!pollSelections[pollId]) pollSelections[pollId] = {};
        var optEl = container.querySelector('[data-index="' + optionIndex + '"]');
        if (!optEl) return;

        if (pollSelections[pollId][optionIndex]) {
            delete pollSelections[pollId][optionIndex];
            optEl.classList.remove('sh-poll-view-option-selected');
        } else {
            pollSelections[pollId][optionIndex] = true;
            optEl.classList.add('sh-poll-view-option-selected');
        }
        // 显示/隐藏提交按钮
        updatePollSubmitBtn(pollId);
        return;
    }

    // 单选模式：直接提交
    Sanqi.apiPost('/api/poll/vote', {
        poll_id: pollId,
        option_index: optionIndex
    }, function(res) {
        if (res.code == '200') {
            successpop('投票成功');
            renderPollResult(pollId, res.data);
        } else {
            warnpop(res.msg || '投票失败');
        }
    }, function(res) {
        warnpop((res && res.msg) || '请求失败');
    });
}

function updatePollSubmitBtn(pollId) {
    var container = document.getElementById('sh-poll-' + pollId);
    if (!container) return;
    var btn = container.querySelector('.sh-poll-view-submit');
    var selected = pollSelections[pollId] ? Object.keys(pollSelections[pollId]) : [];
    if (selected.length > 0) {
        if (!btn) {
            btn = document.createElement('div');
            btn.className = 'sh-poll-view-submit';
            btn.textContent = '提交投票';
            btn.onclick = function() { submitMultiPoll(pollId); };
            container.querySelector('.sh-poll-view-footer').before(btn);
        }
        btn.textContent = '提交投票 (' + selected.length + '项)';
    } else if (btn) {
        btn.remove();
    }
}

function submitMultiPoll(pollId) {
    var indices = pollSelections[pollId] ? Object.keys(pollSelections[pollId]).map(Number) : [];
    if (indices.length === 0) {
        warnpop('请至少选择一个选项');
        return;
    }
    Sanqi.apiPost('/api/poll/vote', {
        poll_id: pollId,
        option_indices: indices
    }, function(res) {
        if (res.code == '200') {
            successpop('投票成功');
            renderPollResult(pollId, res.data);
        } else {
            warnpop(res.msg || '投票失败');
        }
    }, function(res) {
        warnpop((res && res.msg) || '请求失败');
    });
}

function renderPollResult(pollId, data) {
    var container = document.getElementById('sh-poll-options-' + pollId);
    if (!container || !data || !data.options) return;
    container.setAttribute('data-voted', '1');
    // 清理多选状态
    delete pollSelections[pollId];
    // 移除提交按钮
    var pollEl = document.getElementById('sh-poll-' + pollId);
    if (pollEl) {
        var submitBtn = pollEl.querySelector('.sh-poll-view-submit');
        if (submitBtn) submitBtn.remove();
    }

    var options = data.options;
    var optionEls = container.querySelectorAll('.sh-poll-view-option');
    optionEls.forEach(function(el) {
        var idx = parseInt(el.getAttribute('data-index'));
        var opt = options[idx];
        if (!opt) return;
        // 更新选中状态
        if (opt.selected) {
            el.classList.add('sh-poll-view-option-selected');
            if (!el.querySelector('.sh-poll-view-option-check')) {
                var check = document.createElement('span');
                check.className = 'sh-poll-view-option-check';
                check.textContent = '✓';
                el.appendChild(check);
            }
        }
        // 更新进度条
        var bar = el.querySelector('.sh-poll-view-option-bar');
        if (bar) bar.style.width = opt.percent + '%';
        // 更新统计文字
        var textDiv = el.querySelector('.sh-poll-view-option-text');
        if (textDiv && !textDiv.querySelector('.sh-poll-view-option-stat')) {
            var stat = document.createElement('span');
            stat.className = 'sh-poll-view-option-stat';
            stat.textContent = opt.count + '票 (' + opt.percent + '%)';
            textDiv.appendChild(stat);
        }
        // 移除点击事件
        el.onclick = null;
        el.style.cursor = 'default';
    });

    // 更新总人数
    var footer = document.getElementById('sh-poll-' + pollId);
    if (footer) {
        var footerSpan = footer.querySelector('.sh-poll-view-footer span');
        if (footerSpan) footerSpan.textContent = '共 ' + data.total_votes + ' 人参与';
    }
}

// ========== 打赏功能 ==========
function openTipPanel() {
    var modal = document.getElementById('tipModal');
    if (modal) modal.style.display = 'flex';
}

function closeTipPanel() {
    var modal = document.getElementById('tipModal');
    if (modal) modal.style.display = 'none';
}

function switchTipTab(type) {
    var alipayQr = document.getElementById('tipQrAlipay');
    var wechatQr = document.getElementById('tipQrWechat');
    var tabs = document.querySelectorAll('.tip-modal-tab');
    tabs.forEach(function(tab) { tab.classList.remove('active'); });

    if (type === 'alipay') {
        if (alipayQr) alipayQr.style.display = 'flex';
        if (wechatQr) wechatQr.style.display = 'none';
        if (tabs[0]) tabs[0].classList.add('active');
    } else {
        if (alipayQr) alipayQr.style.display = 'none';
        if (wechatQr) wechatQr.style.display = 'flex';
        if (tabs[1]) tabs[1].classList.add('active');
    }
}
