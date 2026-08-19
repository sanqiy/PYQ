/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */
(function (window, document) {
    'use strict';

    function byId(id) {
        return window.Sanqi && Sanqi.id ? Sanqi.id(id) : document.getElementById(id);
    }

    function getActiveMenu() {
        var menus = document.querySelectorAll('.sh-news-main-top-div-menu');
        for (var i = 0; i < menus.length; i++) {
            if (menus[i].style.display !== 'none') return menus[i];
        }
        return menus[0] || null;
    }

    function js_menu(e) {
        e = e || window.event;
        if (e && e.stopPropagation) e.stopPropagation();
        var count = document.getElementById('xxtzsul');
        if (!count || count.innerText == '0') {
            return;
        }
        var target = e && e.target ? e.target : null;
        var container = target ? target.closest('.sh-news-main-top-div2') : null;
        var adElement = container ? container.querySelector('.sh-news-main-top-div-menu') : document.getElementById('js_menu');
        if (!adElement) return;
        var isAdVisible = adElement.style.display === 'none';

        if (isAdVisible) {
            adElement.style.display = 'flex';
        } else {
            adElement.style.display = 'none';
        }
    }

    function xxsczt() {
        if (window.event && window.event.stopPropagation) window.event.stopPropagation();
        var count = document.getElementById('xxtzsul');
        var state = document.getElementById('xxsczt');
        if (!count || !state || count.innerText == '0') {
            return;
        }
        var currentState = state.lang;
        var menu = getActiveMenu();
        if (currentState == 0) {
            state.lang = '-1';
            if (menu) menu.style.display = 'none';
            document.querySelectorAll('.sh-xxliebfm .delmes').forEach(function (element) {
                element.style.display = 'flex';
            });
        } else {
            state.lang = '0';
            if (menu) menu.style.display = 'none';
            document.querySelectorAll('.sh-xxliebfm .delmes').forEach(function (element) {
                element.style.display = 'none';
            });
        }
    }

    function xxscztqb() {
        var count = byId('xxtzsul');
        if (!count || count.innerText == '0') {
            return;
        }
        if (!confirm('\u786e\u5b9a\u8981\u5220\u9664\u6240\u6709\u6d88\u606f\u5417?')) {
            return;
        }

        loadpop('\u6b63\u5728\u5220\u9664\u6d88\u606f,\u8bf7\u7a0d\u540e...', 'ok');
        Sanqi.apiPost('/api/message/operate', { plid: -2, type: -2 }, function (res) {
            if (!res || !res.code) {
                errorpop('\u672a\u83b7\u53d6\u5230\u6570\u636e');
                return;
            }
            if (res.code == '200') {
                var element = byId('sh-news-con');
                if (element) {
                    while (element.firstChild) {
                        element.removeChild(element.firstChild);
                    }
                }
                var state = byId('xxsczt');
                if (state) state.lang = '0';
                count.innerText = 0;
                if (byId('xiaoxhd')) byId('xiaoxhd').style.display = 'none';
                var menu = getActiveMenu();
                if (menu) menu.style.display = 'none';
                successpop('\u5df2\u5220\u9664\u6240\u6709\u6d88\u606f');
            } else {
                errorpop(res.msg);
            }
        }, function (res) {
            errorpop((res && res.msg) || '\u8bf7\u6c42\u5931\u8d25,\u8bf7\u7a0d\u540e\u91cd\u8bd5');
        });
        if (window.event && window.event.stopPropagation) window.event.stopPropagation();
    }

    function xxscyd() {
        var count = byId('xxtzsul');
        if (!count || count.innerText == '0') {
            return;
        }
        loadpop('\u6b63\u5728\u5df2\u8bfb,\u8bf7\u7a0d\u540e...', 'ok');
        Sanqi.apiPost('/api/message/operate', { plid: -3, type: -3 }, function (res) {
            if (!res || !res.code) {
                errorpop('\u672a\u83b7\u53d6\u5230\u6570\u636e');
                return;
            }
            if (res.code == '200') {
                var container = byId('sh-news-con');
                function hideElementsWithPrefix(element, prefix) {
                    if (!element) return;
                    for (var i = 0; i < element.children.length; i++) {
                        var child = element.children[i];
                        if (child.id && child.id.startsWith(prefix)) {
                            child.style.display = 'none';
                        }
                        hideElementsWithPrefix(child, prefix);
                    }
                }
                hideElementsWithPrefix(container, 'xxztx-');
                if (byId('xiaoxhd')) byId('xiaoxhd').style.display = 'none';
                var menu = getActiveMenu();
                if (menu) menu.style.display = 'none';
                successpop('\u5df2\u8bfb\u6240\u6709\u6d88\u606f');
            } else {
                errorpop(res.msg);
            }
        }, function (res) {
            errorpop((res && res.msg) || '\u8bf7\u6c42\u5931\u8d25,\u8bf7\u7a0d\u540e\u91cd\u8bd5');
        });
        if (window.event && window.event.stopPropagation) window.event.stopPropagation();
    }

    function mesgxq() {
        var evt = window.event || {};
        if (evt.stopPropagation) evt.stopPropagation();

        var target = evt.target || evt.srcElement || null;
        if (!target) return;
        var ele = target.id;
        var elela = target.lang;
        var xxzt = 'xxztx-' + ele;
        var state = byId('xxsczt');
        var currentState = state ? state.lang : '0';

        if (currentState == 0) {
            if (elela == '#-1') {
                warnpop('\u6b64\u6d88\u606f\u5df2\u4e0d\u5b58\u5728,\u65e0\u6cd5\u67e5\u770b');
                return;
            }
            Sanqi.apiPost('/api/message/operate', { plid: ele, type: 0 }, function (res) {
                if (!res || !res.code) {
                    errorpop('\u672a\u83b7\u53d6\u5230\u6570\u636e');
                    return;
                }
                if (res.code == '200') {
                    if (byId(xxzt)) byId(xxzt).style.display = 'none';
                    if (elela != '#-1') {
                        location.href = '/view/' + elela;
                    }
                } else {
                    errorpop(res.msg);
                }
            }, function (res) {
                errorpop((res && res.msg) || '\u8bf7\u6c42\u5931\u8d25,\u8bf7\u7a0d\u540e\u91cd\u8bd5');
            });
        } else if (currentState == -1) {
            // Delete mode is handled by demes().
        }
    }

    function demes() {
        var count = byId('xxtzsul');
        if (!count || count.innerText == '0') {
            return;
        }
        var evt = window.event || {};
        var target = evt.target || evt.srcElement || null;
        if (!target || !target.id) {
            return;
        }
        var ele = target.id;
        loadpop('\u6b63\u5728\u5220\u9664\u6d88\u606f,\u8bf7\u7a0d\u540e...', 'ok');
        Sanqi.apiPost('/api/message/operate', { plid: ele, type: -1 }, function (res) {
            if (!res || !res.code) {
                errorpop('\u672a\u83b7\u53d6\u5230\u6570\u636e');
                return;
            }
            if (res.code == '200') {
                var item = byId(ele);
                if (item) item.remove();
                var current = parseInt(count.innerText, 10) || 0;
                var next = current > 0 ? current - 1 : current;
                count.innerText = next;
                if (next <= 0 && byId('xiaoxhd')) {
                    byId('xiaoxhd').style.display = 'none';
                }
                successpop('\u6d88\u606f\u5df2\u5220\u9664');
            } else {
                errorpop(res.msg);
            }
        }, function (res) {
            errorpop((res && res.msg) || '\u8bf7\u6c42\u5931\u8d25,\u8bf7\u7a0d\u540e\u91cd\u8bd5');
        });
        if (window.event && window.event.stopPropagation) window.event.stopPropagation();
    }

    window.getActiveMenu = getActiveMenu;
    window.js_menu = js_menu;
    window.xxsczt = xxsczt;
    window.xxscztqb = xxscztqb;
    window.xxscyd = xxscyd;
    window.mesgxq = mesgxq;
    window.demes = demes;
})(window, document);
