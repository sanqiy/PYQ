/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */
// Like interactions and like-list summary rendering.

function _escapeHtml(text) {
    return String(text == null ? "" : text)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
}

function _renderLikeSummary(list) {
    if (!list) return;

    var names = [];
    var items = list.querySelectorAll('.sh-like-data');
    for (var i = 0; i < items.length; i++) {
        var name = items[i].getAttribute('lang') || items[i].textContent || '';
        if (name) names.push(name);
    }

    var guestItem = list.querySelector('.sh-like-visitor-data');
    var visitorCount = guestItem ? parseInt(guestItem.getAttribute('data-count') || '0', 10) : 0;
    if (isNaN(visitorCount) || visitorCount < 0) visitorCount = 0;

    var total = names.length + visitorCount;
    var summary = list.querySelector('.sh-like-summary');
    if (!summary && total > 0) {
        summary = document.createElement('li');
        summary.className = 'sh-like-summary';
        list.appendChild(summary);
    }

    if (!summary) return;
    if (total < 1) {
        summary.remove();
        return;
    }

    if (names.length > 0) {
        summary.textContent = names.join('，') + '，等' + total + '人觉得很赞';
    } else {
        summary.textContent = '有' + total + '人觉得很赞';
    }
}

function _ensureLikeVisitorItem(list, id) {
    var guestItem = list.querySelector('.sh-like-visitor-data');
    if (!guestItem) {
        guestItem = document.createElement('li');
        guestItem.id = 'fkzan-' + id;
        guestItem.className = 'sh-like-visitor-data';
        guestItem.setAttribute('data-count', '0');
        guestItem.style.display = 'none';
        list.insertBefore(guestItem, list.querySelector('.sh-like-summary') || null);
    }
    return guestItem;
}

function _addLikeName(list, id, name, userId) {
    if (!list || !name) return;
    var existing = list.querySelectorAll('#zan-' + id);
    for (var i = 0; i < existing.length; i++) {
        if (userId && existing[i].getAttribute('data-user') === userId) return;
        if (existing[i].getAttribute('lang') === name) return;
    }

    var summary = list.querySelector('.sh-like-summary');
    var li = document.createElement('li');
    li.id = 'zan-' + id;
    li.className = 'sh-like-data';
    if (userId) li.setAttribute('data-user', userId);
    li.setAttribute('lang', name);
    li.style.display = 'none';
    li.textContent = name;
    list.insertBefore(li, summary || null);
}

function _addNamedGuestLike(list, id, name, userId) {
    _addLikeName(list, id, name, userId);
}

function _removeLikeName(list, id, name, userId) {
    if (!list || (!name && !userId)) return;
    var elements = list.querySelectorAll('#zan-' + id);
    elements.forEach(function(element) {
        if ((userId && element.getAttribute('data-user') === userId) || (!userId && element.getAttribute('lang') === name)) {
            element.remove();
        }
    });
}

function _addGuestLike(list, id) {
    var guestItem = _ensureLikeVisitorItem(list, id);
    var count = parseInt(guestItem.getAttribute('data-count') || '0', 10);
    if (isNaN(count) || count < 0) count = 0;
    guestItem.setAttribute('data-count', String(count + 1));
}

function _removeGuestLike(list) {
    if (!list) return;
    var guestItem = list.querySelector('.sh-like-visitor-data');
    if (!guestItem) return;
    var count = parseInt(guestItem.getAttribute('data-count') || '0', 10);
    if (isNaN(count) || count < 1) return;
    count--;
    if (count > 0) {
        guestItem.setAttribute('data-count', String(count));
    } else {
        guestItem.remove();
    }
}

/*点赞按钮事件 */
function dinazan() {
    var tie = Sanqi.id("sh-tieid");
    var id = tie ? tie.innerText : "";
    var user_id = getCookie("username");
    var user_passid = getCookie("passid");
    var likeText = Sanqi.id("tiezdz-" + id);
    var likeIcon = Sanqi.id("tiezimg-" + id);
    var zans = Sanqi.id("zans-" + id);
    var zanss = Sanqi.id("zanss-" + id);
    var list = Sanqi.id("zlbeh-" + id);

    if (!id || !likeText || !likeIcon || !list) {
        warnpop("\u672a\u83b7\u53d6\u5230\u6587\u7ae0\u4fe1\u606f");
        return;
    }

    var isGuest = user_id == "" || user_passid == "";
    var isLike = likeText.innerText == "\u8d5e";

    var zts = isLike ? 0 : -1;
    var visitorProfile = isGuest ? _getVisitorCommentIdentity() : { name: "", email: "", url: "" };
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
        if (code != 200 && code != '200') {
            warnpop(msg || "\u8bf7\u6c42\u5931\u8d25,\u8bf7\u7a0d\u540e\u91cd\u8bd5");
            return;
        }

        if (isLike) {
            var data = obj.data || {};
            var name = data.name || msg;
            if (zans && zans.style.display == "none") {
                zans.style.display = "flex";
            }
            if (zanss) {
                zanss.style.display = "block";
            }

            if (isGuest) {
                if (data.profiled && data.user && name) {
                    _addNamedGuestLike(list, id, name, data.user);
                } else {
                    _addGuestLike(list, id);
                }
            } else {
                _addLikeName(list, id, name, user_id);
            }
            _renderLikeSummary(list);

            likeText.innerText = "\u53d6\u6d88";
            likeText.setAttribute('data-like-name', (!isGuest || data.profiled) ? name : '');
            likeText.setAttribute('data-like-user', data.user || user_id || '');
            likeIcon.className = "iconfont icon-aixin2 ri-sxdzlikehs";
            successpop("\u70b9\u8d5e\u6210\u529f");
            loaddemand();
        } else {
            var data = obj.data || {};
            var unlikeName = likeText.getAttribute('data-like-name') || '';
            var unlikeUser = data.user || likeText.getAttribute('data-like-user') || user_id || '';
            _removeLikeName(list, id, unlikeName, unlikeUser);
            if (isGuest && !unlikeName && !data.profiled) {
                _removeGuestLike(list);
            }
            _renderLikeSummary(list);
            likeText.removeAttribute('data-like-name');
            likeText.removeAttribute('data-like-user');
            if (!list.querySelector('.sh-like-summary') && zans) {
                zans.style.display = "none";
            }
            likeText.innerText = "\u8d5e";
            likeIcon.className = "iconfont icon-aixin ri-sxdzlike";
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
