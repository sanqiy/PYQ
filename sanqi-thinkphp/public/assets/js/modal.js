/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */
(function (window, document) {
    'use strict';

    var Sanqi = window.Sanqi || {};

    function el(id) {
        return Sanqi.id ? Sanqi.id(id) : document.getElementById(id);
    }

    function open(id, display) {
        if (Sanqi.openModal) {
            return Sanqi.openModal(id, display || 'flex');
        }
        var node = typeof id === 'string' ? el(id) : id;
        if (node) node.style.display = display || 'flex';
        return node;
    }

    function close(id) {
        if (Sanqi.closeModal) {
            return Sanqi.closeModal(id);
        }
        var node = typeof id === 'string' ? el(id) : id;
        if (node) node.style.display = 'none';
        return node;
    }

    function openFirst(ids) {
        for (var i = 0; i < ids.length; i++) {
            var node = el(ids[i]);
            if (node) {
                open(node);
                return node;
            }
        }
        return null;
    }

    function closeAll(ids) {
        ids.forEach(function (id) {
            close(id);
        });
    }

    function setText(id, text) {
        var node = el(id);
        if (node) node.innerText = text;
    }

    function setDisplay(id, display) {
        var node = el(id);
        if (node) node.style.display = display;
    }

    function setClass(id, className) {
        var node = el(id);
        if (node) node.className = className;
    }

    function setValue(id, value) {
        var node = el(id);
        if (node) node.value = value;
    }

    function setClick(id, handler) {
        var node = el(id);
        if (node) node.setAttribute('onclick', handler);
    }

    function restoreLoginAccount() {
        var account = el('login-zh');
        if (!account || account.value) return;
        try {
            account.value = window.localStorage.getItem('sanqi_login_account') || '';
        } catch (e) {}
    }

    function kqlogin() {
        var user = typeof getCookie === 'function' ? getCookie('username') : '';
        var passid = typeof getCookie === 'function' ? getCookie('passid') : '';
        if (user != '' && passid != '') {
            return;
        }
        open('sh-login');
        restoreLoginAccount();
        setTimeout(function () {
            var account = el('login-zh');
            var password = el('login-pass');
            if (account && !account.value) {
                account.focus();
            } else if (password) {
                password.focus();
            }
        }, 80);
    }

    function gblogin() {
        if (typeof window._resetAdminTwoFactorLogin === 'function') {
            window._resetAdminTwoFactorLogin();
        }
        close('sh-login');
    }

    function kqfabu() {
        open('sh-fabu');
    }

    function gbfabu() {
        close('sh-fabu');
    }

    function kqnews() {
        openFirst(['sh-news', 'sh-news-inline']);
    }

    function gbnews() {
        closeAll(['sh-news', 'sh-news-inline']);
    }

    function kqlink() {
        openFirst(['sh-link', 'sh-link-inline']);
    }

    function gblink() {
        closeAll(['sh-link', 'sh-link-inline']);
    }

    function kqso() {
        var search = el('so');
        if (!search) return;
        if (search.style.display != 'flex') {
            open(search);
            var input = (Sanqi.qs ? Sanqi.qs('.sobd-in', search) : search.querySelector('.sobd-in'));
            if (input) input.focus();
        } else {
            close(search);
        }
    }

    function gbso() {
        close('so');
    }

    function gbsousuo() {
        close('sh-sousuo');
    }

    function sousuo() {
        var input = el('sousuo-input');
        var value = input ? input.value.replace(/^\s+|\s+$/g, '') : '';
        if (!value) {
            if (typeof warnpop === 'function') warnpop('\u8bf7\u8f93\u5165\u5173\u952e\u5b57');
            return;
        }
        window.location.href = '/?so=' + encodeURIComponent(value);
    }

    function checkKeyDown(event) {
        event = event || window.event;
        if (event && event.keyCode === 13) {
            event.preventDefault();
        }
    }

    function zcanxy() {
        var emailBox = el('sh-login-main-con-anu');
        if (!emailBox) return false;

        if (emailBox.style.display == 'none') {
            setDisplay('sh-login-main-con-anu', 'flex');
            setDisplay('sh-login-main-con-yzmwk', 'flex');
            setText('sh-zck-an', '\u767b\u5f55\u8d26\u53f7');
            setText('zhdzsx', '\u8d26\u53f7\u6ce8\u518c');
            setClass('sh-left-dlzc', 'sh-left');
            setValue('sh-left-dlzc', '\u6ce8\u518c');
            setClick('sh-left-dlzc', 'regzc()');
        } else {
            setDisplay('sh-login-main-con-anu', 'none');
            setDisplay('sh-login-main-con-yzmwk', 'none');
            setText('sh-zck-an', '\u6ce8\u518c\u8d26\u53f7');
            setText('zhdzsx', '\u8d26\u53f7\u767b\u5f55');
            setClass('sh-left-dlzc', 'sh-right');
            setValue('sh-left-dlzc', '\u767b\u5f55');
            setClick('sh-left-dlzc', 'logy()');
        }
        return false;
    }

    window.kqlogin = kqlogin;
    window.gblogin = gblogin;
    window.kqfabu = kqfabu;
    window.gbfabu = gbfabu;
    window.kqnews = kqnews;
    window.gbnews = gbnews;
    window.kqlink = kqlink;
    window.gblink = gblink;
    window.kqso = kqso;
    window.gbso = gbso;
    window.gbsousuo = gbsousuo;
    window.sousuo = sousuo;
    window.checkKeyDown = checkKeyDown;
    window.zcanxy = zcanxy;
    window.restoreLoginAccount = restoreLoginAccount;
})(window, document);
