/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */
// Comment box, replies, drafts, and visitor comment identity.

var _currentCommentCid = '';

function _saveDraft(cid) {
    if (!cid || cid === '-') return;
    var ta = document.getElementById('bletext');
    if (ta && ta.value.trim() !== '') {
        try { localStorage.setItem('mm_draft_' + cid, ta.value); } catch(e) {}
    }
}

function _restoreDraft(cid) {
    if (!cid || cid === '-') return;
    var ta = document.getElementById('bletext');
    if (!ta) return;
    try {
        var saved = localStorage.getItem('mm_draft_' + cid);
        ta.value = saved || '';
    } catch(e) { ta.value = ''; }
}

function _clearDraft(cid) {
    if (!cid) return;
    try { localStorage.removeItem('mm_draft_' + cid); } catch(e) {}
}

/* 点击评论框以外区域关闭评论框 */
document.addEventListener('click', function(e) {
    var pinglunkuang = document.getElementById('pinglunkuang');
    if (!pinglunkuang) return;
    if (e.target && e.target.closest && e.target.closest('.sh-content-right-time-right')) return;
    var parent = pinglunkuang.parentNode;
    if (!parent || parent.id === 'pinglunkfk') return; // 已关闭，不处理
    if (pinglunkuang.contains(e.target)) return; // 点击评论框内部，忽略
    // 保存草稿后关闭
    _saveDraft(_currentCommentCid);
    plkgb();
});

/* 评论合集开 --评论与点赞按钮打开或关闭*/
function plk() {
    var evt = window.event || {};
    var target = evt.target || evt.srcElement || {};
    if (evt && evt.stopPropagation) {
        evt.stopPropagation();
    } else if (window.event) {
        window.event.cancelBubble = true;
    }
    var ele = target.id;
    if (!ele && target.closest) {
        var cidNode = target.closest("[data-cid]");
        if (cidNode) ele = cidNode.getAttribute("data-cid") || cidNode.id;
    }
    if (!ele && target.closest) {
        var onclickNode = target.closest("[onclick]");
        if (onclickNode && onclickNode.id) ele = onclickNode.id;
    }
    // 点击落在评论列表li上时，从data-comment-article获取文章ID
    if (!ele && target.closest) {
        var commentItem = target.closest('.sh-comment-item');
        if (commentItem) ele = commentItem.getAttribute('data-comment-article') || '';
    }
    if (!ele) return;
    /* console.log(scid); */
    var ids = "pl-" + ele;
    var plElement = document.getElementById(ids);
    if (!plElement) return;
    if (plElement.style.display != "flex") {
        //先隐藏上次打开评论菜单↓
        var arrs = document.getElementsByName("pl");
        for (var i = 0; i < arrs.length; i++) {
            /* alert(arrs[i].id); */
            document.getElementById(arrs[i].id).style = "display: none";
        }
        //先隐藏上次打开的评论菜单↑
        plElement.style = "display: flex";
    } else {
        plElement.style = "display: none";
    }
    //设置参数id
    document.getElementById("sh-tieid").innerText = ele;
}



//跳转到发布页
function fby(){
    window.location = '/edit';
}



//表情列表开关
function bqkg() {
    event.stopPropagation();//禁止冒泡

    var textarea = document.getElementById("bletext");
    textarea.focus(); // 将焦点设置到textarea

    if (document.getElementById('biaoqing').style.display != "grid") {
        document.getElementById('biaoqing').style = "display: grid";
        document.getElementById('sh-pinglun-fs-right-bqimg').className = "iconfont icon-biaoqing ri-sxbqxzls"//修改表情开关为绿色
    } else {
        document.getElementById('biaoqing').style = "display: none";
        document.getElementById('sh-pinglun-fs-right-bqimg').className = "iconfont icon-biaoqing ri-sxbqxz"//修复表情开关为灰色
    }
}




//游客输入框开关
function ykkg() {
    var evt = window.event || {};
    if (evt && evt.stopPropagation) {
        evt.stopPropagation();
    } else if (evt) {
        evt.cancelBubble = true;
    }

    var textarea = document.getElementById("bletext");
    textarea.focus(); // 将焦点设置到textarea

    if (document.getElementById('sh-plk-yk').style.display != "flex") {
        document.getElementById('sh-plk-yk').style = "display: flex";
        _restoreVisitorCommentIdentity();
        document.getElementById('sh-pinglun-fs-right-ykkgb').className = "iconfont icon-yonghu1 ri-sxbqxzls"//修改表情开关为绿色
    } else {
        document.getElementById('sh-plk-yk').style = "display: none";
        document.getElementById('sh-pinglun-fs-right-ykkgb').className = "iconfont icon-yonghu1 ri-sxbqxz"//修复表情开关为灰色
    }
}




var _plkkgJustRan = false;

function _findWechatArticleCommentInput(cid) {
    var inputs = document.getElementsByClassName('wx-comment-input');
    for (var i = 0; i < inputs.length; i++) {
        if (inputs[i].getAttribute('data-cid') === cid) {
            return inputs[i];
        }
    }
    return null;
}

function _resetWechatArticleCommentInputs() {
    var slots = document.getElementsByClassName('wx-comment-editor-slot');
    for (var i = 0; i < slots.length; i++) {
        if (!slots[i].contains(document.getElementById('pinglunkuang'))) {
            slots[i].style.display = 'none';
        }
    }

    var inputs = document.getElementsByClassName('wx-comment-input');
    for (var j = 0; j < inputs.length; j++) {
        if (inputs[j].getAttribute('data-cid')) {
            inputs[j].style.display = '';
        }
    }
}

function _isPostMenuCommentTrigger(target) {
    return !!(target && target.closest && target.closest('.sh-content-right-time-right-left-y'));
}

function _focusCommentBox(box) {
    if (!box) return;
    setTimeout(function() {
        box.scrollIntoView({ behavior: 'smooth', block: 'center' });
        box.style.transition = 'box-shadow 0.3s';
        box.style.boxShadow = '0 0 0 2px #4CAF50';
        setTimeout(function() { box.style.boxShadow = ''; }, 1500);
        var textarea = document.getElementById("bletext");
        if (textarea) textarea.focus();
    }, 100);
}

function _resetRootCommentState(ele) {
    var elements = document.querySelectorAll('[data-comkzt]');
    for (var i = 0; i < elements.length; i++) {
        elements[i].setAttribute('data-comkzt', '0');
    }

    var tieid = document.getElementById("sh-tieid");
    var tiehf = document.getElementById("sh-tiehf");
    var tieea = document.getElementById("sh-tieea");
    var textarea = document.getElementById("bletext");
    if (tieid) tieid.innerText = ele;
    if (tiehf) tiehf.innerText = "false";
    if (tieea) tieea.innerText = "false";
    if (textarea) textarea.placeholder = "评论";
}

function _hidePostActionMenu(ele) {
    var menu = document.getElementById("pl-" + ele);
    if (!menu || menu.style.display == "none") return;
    var arrs = document.getElementsByName("pl");
    for (var i = 0; i < arrs.length; i++) {
        document.getElementById(arrs[i].id).style = "display: none";
    }
}

function _openRootCommentBoxFromPostMenu(ele) {
    var pinglunkuangElement = document.getElementById('pinglunkuang');
    var shZanpPlElement = document.getElementById('sh-zanp-pl-' + ele);
    if (!pinglunkuangElement || !shZanpPlElement) return false;

    if (shZanpPlElement.style.display == "none") {
        shZanpPlElement.style.display = "flex";
    }

    var detailCommentBar = document.getElementById("sh-dz-z-" + ele);
    if (detailCommentBar && detailCommentBar.style.display == "none") {
        detailCommentBar.style.display = "flex";
    }

    _resetWechatArticleCommentInputs();
    _resetRootCommentState(ele);

    if (shZanpPlElement.firstElementChild !== pinglunkuangElement) {
        shZanpPlElement.insertBefore(pinglunkuangElement, shZanpPlElement.firstChild);
    }

    _focusCommentBox(pinglunkuangElement);
    _hidePostActionMenu(ele);
    return true;
}

//评论框插入与删除
function plkkg(evtArg) {
    var evt = evtArg || window.event || {};
    var target = evt.target || evt.srcElement || {};
    var ele = target.id;
    if (!ele && target.closest) {
        var cidNode = target.closest("[data-cid]");
        if (cidNode) ele = cidNode.getAttribute("data-cid") || cidNode.id;
    }
    if (!ele && target.closest) {
        var onclickNode = target.closest("[onclick]");
        if (onclickNode && onclickNode.id) ele = onclickNode.id;
    }
    // 点击落在评论列表li上时，从data-comment-article获取文章ID
    if (!ele && target.closest) {
        var commentItem = target.closest('.sh-comment-item');
        if (commentItem) ele = commentItem.getAttribute('data-comment-article') || '';
    }
    if (!ele) return;
    _plkkgJustRan = true;
    setTimeout(function() { _plkkgJustRan = false; }, 200);
    //设置参数id
    document.getElementById("sh-tieid").innerText = ele;

    if (evt && evt.stopPropagation) {
        evt.stopPropagation();//禁止冒泡
    } else if (window.event) {
        window.event.cancelBubble = true;
    }

    // 切换文章时保存旧草稿、恢复新草稿
    if (_currentCommentCid && _currentCommentCid !== ele) {
        _saveDraft(_currentCommentCid);
    }
    _currentCommentCid = ele;
    _restoreDraft(ele);
    //document.getElementById("sh-plkz").style = "display: flex";

    var isPostMenuCommentTrigger = _isPostMenuCommentTrigger(target);

    var wxCommentSlot = document.getElementById('wx-comment-editor-slot-' + ele);
    if (wxCommentSlot) {
        var wxPinglunkuangElement = document.getElementById('pinglunkuang');
        if (!wxPinglunkuangElement) return;
        if (wxPinglunkuangElement.parentNode === wxCommentSlot && !isPostMenuCommentTrigger) {
            plkgb();
            return;
        }

        var wxElements = document.querySelectorAll('[data-comkzt]');
        for (var wxi = 0; wxi < wxElements.length; wxi++) {
            wxElements[wxi].setAttribute('data-comkzt', '0');
        }

        _resetWechatArticleCommentInputs();
        wxCommentSlot.style.display = 'block';
        wxCommentSlot.appendChild(wxPinglunkuangElement);

        var wxInput = _findWechatArticleCommentInput(ele);
        if (wxInput) {
            wxInput.style.display = 'none';
        }

        document.getElementById("sh-tiehf").innerText = "false";
        document.getElementById("sh-tieea").innerText = "false";
        document.getElementById("bletext").placeholder = "评论";

        setTimeout(function() {
            wxPinglunkuangElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            wxPinglunkuangElement.style.transition = 'box-shadow 0.3s';
            wxPinglunkuangElement.style.boxShadow = '0 0 0 2px #4CAF50';
            setTimeout(function() { wxPinglunkuangElement.style.boxShadow = ''; }, 1500);
            var textarea = document.getElementById("bletext");
            if (textarea) textarea.focus();
        }, 100);
        _hidePostActionMenu(ele);
        return;
    }

    if (isPostMenuCommentTrigger && _openRootCommentBoxFromPostMenu(ele)) {
        return;
    }

    var zanpPl = document.getElementById("sh-zanp-pl-"+ele);
    if (!zanpPl) return;
    if (zanpPl.style.display == "none") {
        zanpPl.style.display="flex";
    }
    //详情页
    if (document.getElementById("sh-dz-z-"+ele)) {
        if (document.getElementById("sh-dz-z-"+ele).style.display == "none") {
            document.getElementById("sh-dz-z-"+ele).style.display="flex";
        }
    }

    var shZanpPlElement = document.getElementById('sh-zanp-pl-'+ele);
    var firstChildElement = shZanpPlElement ? shZanpPlElement.firstElementChild : null;
    if (firstChildElement && firstChildElement.id === 'pinglunkuang' && !isPostMenuCommentTrigger) {
        //恢复原位
        plkgb();//清除上次插入操作
        //恢复原位
    } else {
        //在执行完上面代码后都需要把所有的自定义属性都改为0，防止有的属性没有复位到0导致bug
        var elements = document.querySelectorAll('[data-comkzt]');
        for (var i = 0; i < elements.length; i++) {
            elements[i].setAttribute('data-comkzt', '0');
        }

        //移动到第一个位置
        var pinglunkuangElement = document.getElementById('pinglunkuang');
        var shZanpPlElement = document.getElementById('sh-zanp-pl-'+ele);
        if (pinglunkuangElement && shZanpPlElement && shZanpPlElement.firstElementChild !== pinglunkuangElement) {
            shZanpPlElement.insertBefore(pinglunkuangElement, shZanpPlElement.firstChild);
        }
        setTimeout(function() {
            pinglunkuangElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            pinglunkuangElement.style.transition = 'box-shadow 0.3s';
            pinglunkuangElement.style.boxShadow = '0 0 0 2px #4CAF50';
            setTimeout(function() { pinglunkuangElement.style.boxShadow = ''; }, 1500);
        }, 100);

        //隐藏所有没有评论但是显示了的评论列表元素
        var elements = document.getElementsByClassName("sh-zanp-pl");
        for (var i = 0; i < elements.length; i++) {
          var element = elements[i];
          if (element.children.length === 0) {
            element.style.display = "none";
          }
        }

        document.getElementById("sh-tiehf").innerText = "false";
        document.getElementById("sh-tieea").innerText = "false";
        document.getElementById("bletext").placeholder = "评论";
        var textarea = document.getElementById("bletext");
        textarea.focus(); // 将焦点设置到textarea
    }

    //隐藏菜单
    var ids = "pl-" + ele;
    if (document.getElementById(ids).style.display != "none") {
        //先隐藏上次打开评论菜单↓
        var arrs = document.getElementsByName("pl");
        for (var i = 0; i < arrs.length; i++) {
            /* alert(arrs[i].id); */
            document.getElementById(arrs[i].id).style = "display: none";
        }
        //先隐藏上次打开的评论菜单↑
    }

}




function plkgb() {
    //设置出场动画

    // 保存当前草稿
    _saveDraft(_currentCommentCid);

    //恢复输入框到原位
    var shPinglunElement = document.getElementById('pinglunkuang');
    var pinglunkfkElement = document.getElementById('pinglunkfk');
    if (pinglunkfkElement && shPinglunElement) {
        pinglunkfkElement.appendChild(shPinglunElement);
    }
    _resetWechatArticleCommentInputs();

    //在设置1前先把所有的自定义属性都改为0，防止有的属性没有复位到0导致bug
    var elements = document.querySelectorAll('[data-comkzt]');
    for (var i = 0; i < elements.length; i++) {
        elements[i].setAttribute('data-comkzt', '0');
    }

    //隐藏所有没有评论但是显示了的评论列表元素
    var elements = document.getElementsByClassName("sh-zanp-pl");
    for (var i = 0; i < elements.length; i++) {
        var element = elements[i];
        if (element.children.length === 0) {
            element.style.display = "none";
            var detailCommentBar = element.closest ? element.closest('.sh-dz-z') : null;
            if (detailCommentBar) {
              detailCommentBar.style.display = 'none';
            }
            //详情页
        }
    }

    //清空输入框内容
    document.getElementById("bletext").value = "";
    document.getElementById("sh-tieid").innerText="-";
    document.getElementById("sh-tiehf").innerText="-";
    document.getElementById("sh-tieea").innerText="-";
}







/* 表情按钮事件 */
var input = document.getElementById("bletext");
var rangeIndex=null//光标地位
//监听失焦
if(input){
  input.onblur = function(){
    rangeIndex = this.selectionStart;//获取失焦时光标的地位
  }
}
//插入函数
function biaoqzj(event){
    event = event || window.event;
    var target = event.target || event.srcElement;
    var ele = target.alt || '';//获取点击的表情alt
    if(!ele) return;
    if(!input) input = document.getElementById("bletext");
    if(!input) return;
    // 如果没有记录光标位置，则使用当前光标位置或末尾
    if(rangeIndex === null || rangeIndex === undefined){
        rangeIndex = input.selectionStart || input.value.length;
    }
    let oldVaue = input.value;
    input.value = oldVaue.slice(0,rangeIndex)+ele+oldVaue.slice(rangeIndex);
    rangeIndex = rangeIndex+ele.toString().length;
    input.focus();
    input.setSelectionRange(rangeIndex,rangeIndex);//重新定位光标
}





//获取cookie函数

var _visitorCommentCookieNames = {
    name: "comment_vis_name",
    email: "comment_vis_email",
    url: "comment_vis_url"
};

function _readCookieValue(name) {
    var cookies = document.cookie ? document.cookie.split("; ") : [];
    for (var i = 0; i < cookies.length; i++) {
        var item = cookies[i];
        var index = item.indexOf("=");
        var key = index > -1 ? item.slice(0, index) : item;
        if (key === name) {
            var value = index > -1 ? item.slice(index + 1) : "";
            try {
                return decodeURIComponent(value);
            } catch (e) {
                return value;
            }
        }
    }
    return "";
}

function _writeCookieValue(name, value, days) {
    var expires = new Date();
    expires.setTime(expires.getTime() + (days || 365) * 24 * 60 * 60 * 1000);
    document.cookie = name + "=" + encodeURIComponent(value || "") + "; expires=" + expires.toUTCString() + "; path=/; SameSite=Lax";
}

function _saveLoginAccount(account) {
    account = (account || "").trim();
    if (!account) return;
    try { localStorage.setItem("sanqi_login_account", account); } catch (e) {}
}

function _restoreLoginAccount() {
    var accountInput = document.getElementById("login-zh");
    if (!accountInput || accountInput.value) return;
    try {
        accountInput.value = localStorage.getItem("sanqi_login_account") || "";
    } catch (e) {}
}

function _getLoginRedirect() {
    var params;
    try {
        params = new URLSearchParams(window.location.search || "");
    } catch (e) {
        return "/";
    }

    var redirect = params.get("redirect") || "/";
    if (redirect.charAt(0) !== "/" || redirect.indexOf("//") === 0 || redirect.indexOf("\\") !== -1) {
        return "/";
    }
    return redirect;
}

function _resetAdminTwoFactorLogin() {
    var box = document.getElementById("login-2fa-box");
    var code = document.getElementById("login-2fa-code");
    var token = document.getElementById("login-2fa-token");
    var button = document.getElementById("sh-left-dlzc");
    var title = document.getElementById("zhdzsx");
    if (box) box.style.display = "none";
    if (code) code.value = "";
    if (token) token.value = "";
    if (button) button.value = "\u767b\u5f55";
    if (title) title.innerText = "\u8d26\u53f7\u767b\u5f55";
}

function _showAdminTwoFactorLogin(data) {
    data = data || {};
    var box = document.getElementById("login-2fa-box");
    var code = document.getElementById("login-2fa-code");
    var token = document.getElementById("login-2fa-token");
    var button = document.getElementById("sh-left-dlzc");
    var title = document.getElementById("zhdzsx");
    if (box) box.style.display = "flex";
    if (token) token.value = data.token || "";
    if (code) {
        code.value = "";
        code.placeholder = data.method === "totp" ? "TOTP \u52a8\u6001\u9a8c\u8bc1\u7801" : "\u90ae\u7bb1\u9a8c\u8bc1\u7801";
        setTimeout(function(){ code.focus(); }, 60);
    }
    if (button) button.value = "\u5b8c\u6210\u9a8c\u8bc1";
    if (title) title.innerText = data.method === "totp" ? "TOTP \u4e8c\u6b65\u9a8c\u8bc1" : "\u90ae\u7bb1\u4e8c\u6b65\u9a8c\u8bc1";
}

function _isAdminTwoFactorStep() {
    var box = document.getElementById("login-2fa-box");
    var token = document.getElementById("login-2fa-token");
    return !!(box && box.style.display !== "none" && token && token.value);
}

function _openLoginFromQuery() {
    var params;
    try {
        params = new URLSearchParams(window.location.search || "");
    } catch (e) {
        return;
    }

    if (params.get("login") !== "1") return;
    setTimeout(function() {
        if (typeof kqlogin === "function") {
            kqlogin();
        } else if (document.getElementById("sh-login")) {
            document.getElementById("sh-login").style.display = "flex";
        }
    }, 80);
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", _restoreLoginAccount);
    document.addEventListener("DOMContentLoaded", _openLoginFromQuery);
} else {
    _restoreLoginAccount();
    _openLoginFromQuery();
}

function _restoreVisitorCommentIdentity() {
    var nameInput = document.getElementById("vis_name");
    var emailInput = document.getElementById("vis_email");
    var urlInput = document.getElementById("vis_url");
    if (!nameInput && !emailInput && !urlInput) return;

    var savedName = _readCookieValue(_visitorCommentCookieNames.name);
    var savedEmail = _readCookieValue(_visitorCommentCookieNames.email);
    var savedUrl = _readCookieValue(_visitorCommentCookieNames.url);

    if (nameInput && !nameInput.value && savedName) nameInput.value = savedName;
    if (emailInput && !emailInput.value && savedEmail) emailInput.value = savedEmail;
    if (urlInput && !urlInput.value && savedUrl) urlInput.value = savedUrl;
}

function _saveVisitorCommentIdentity(vis_name, vis_email, vis_url) {
    var name = (vis_name || "").trim();
    var email = (vis_email || "").trim();
    var url = (vis_url || "").trim();
    if (!name && !email && !url) return;

    _writeCookieValue(_visitorCommentCookieNames.name, name, 365);
    _writeCookieValue(_visitorCommentCookieNames.email, email, 365);
    _writeCookieValue(_visitorCommentCookieNames.url, url, 365);
}

function _saveVisitorCommentIdentityFromInputs() {
    var nameInput = document.getElementById("vis_name");
    var emailInput = document.getElementById("vis_email");
    var urlInput = document.getElementById("vis_url");
    if (!nameInput && !emailInput && !urlInput) return;
    _saveVisitorCommentIdentity(
        nameInput ? nameInput.value : "",
        emailInput ? emailInput.value : "",
        urlInput ? urlInput.value : ""
    );
}

function _getVisitorCommentIdentity() {
    var nameInput = document.getElementById("vis_name");
    var emailInput = document.getElementById("vis_email");
    var urlInput = document.getElementById("vis_url");
    return {
        name: ((nameInput && nameInput.value) || _readCookieValue(_visitorCommentCookieNames.name) || "").trim(),
        email: ((emailInput && emailInput.value) || _readCookieValue(_visitorCommentCookieNames.email) || "").trim(),
        url: ((urlInput && urlInput.value) || _readCookieValue(_visitorCommentCookieNames.url) || "").trim()
    };
}

function _bindVisitorCommentIdentity() {
    var ids = ["vis_name", "vis_email", "vis_url"];
    var hasInput = false;
    _restoreVisitorCommentIdentity();
    for (var i = 0; i < ids.length; i++) {
        var input = document.getElementById(ids[i]);
        if (!input) continue;
        hasInput = true;
        input.addEventListener("change", _saveVisitorCommentIdentityFromInputs);
    }
    if (!hasInput) return;
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", _bindVisitorCommentIdentity);
} else {
    _bindVisitorCommentIdentity();
}

function plhuifu() {
    if (_plkkgJustRan) return;
    var evt = window.event || {};
    var trigger = evt.target || null;
    if (trigger && trigger.nodeType === 1 && trigger.tagName !== 'LI') {
        trigger = trigger.closest ? trigger.closest('.sh-comment-item') : trigger.parentElement;
    }
    if (!trigger) {
        return;
    }

    var li = trigger.closest ? trigger.closest('.sh-comment-item') : trigger;
    if (!li || !li.getAttribute) {
        return;
    }

    var eles = li.getAttribute('data-comment-article') || li.id || '';
    var elee = li.getAttribute('data-comment-name') || li.getAttribute('lang') || '';
    var elea = li.getAttribute('data-comment-user') || li.getAttribute('value') || '';
    var dqzdsx = li.getAttribute('data-comkzt') || '0';

    if (dqzdsx == '1') {
        plkgb();
        return;
    }

    var elements = document.querySelectorAll('[data-comkzt]');
    for (var i = 0; i < elements.length; i++) {
        elements[i].setAttribute('data-comkzt', '0');
    }

    var divToMove = document.getElementById('pinglunkuang');
    if (divToMove) {
        li.insertAdjacentElement('afterend', divToMove);
        _resetWechatArticleCommentInputs();
    }

    li.setAttribute('data-comkzt', '1');

    var textarea = document.getElementById('bletext');
    if (textarea) {
        textarea.focus();
        textarea.placeholder = '回复' + elee;
    }

    var commentGroups = document.getElementsByClassName('sh-zanp-pl');
    for (var j = 0; j < commentGroups.length; j++) {
        var group = commentGroups[j];
        if (group.children.length === 0) {
            group.style.display = 'none';
        }
    }

    var tieid = document.getElementById('sh-tieid');
    var tiehf = document.getElementById('sh-tiehf');
    var tieea = document.getElementById('sh-tieea');
    if (tieid) tieid.innerText = eles;
    if (tiehf) tiehf.innerText = elee;
    if (tieea) tieea.innerText = elea;
}

document.addEventListener('click', function(event) {
    var link = event.target && event.target.closest ? event.target.closest('a') : null;
    if (link && link.closest('.sh-comment-item')) {
        return;
    }

    var item = event.target && event.target.closest ? event.target.closest('.sh-comment-item') : null;
    if (!item) {
        return;
    }

    window.event = event;
    plhuifu();
});






//回复者名字url时间
function hfljurl(){
    //点击评论者的名字时跳转到它的网站,并且禁止冒泡，防止触发父元素事件
    event.stopPropagation();
}











/* 消息通知弹窗 */


/* 友链弹窗 */


/* 发送评论按钮事件 */
function fasong() {
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
            if (Sanqi.id("sh-login")) {
                Sanqi.id("sh-login").style.display = "flex";
            } else {
                warnpop("\u8bf7\u5148\u767b\u5f55");
            }
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
    if ((user_id == "" || user_passid == "") && Sanqi.id("sh-plk-yk")) {
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
        var msg = obj.msg;
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
            }
            if (d.html) {
                $("#" + tid).append(d.html);
            }
            successpop(msg || "\u8bc4\u8bba\u53d1\u9001\u6210\u529f");
            loaddemand();
        } else {
            if (msg.indexOf('\u5ba1\u6838') > -1) {
                warnpop(msg);
                plkgb();
            } else {
                errorpop(msg);
            }
        }
    }, function(res){
        errorpop((res && res.msg) || "\u8bc4\u8bba\u53d1\u9001\u5931\u8d25,\u8bf7\u7a0d\u540e\u91cd\u8bd5");
    });
}






//注册账号按钮事件
