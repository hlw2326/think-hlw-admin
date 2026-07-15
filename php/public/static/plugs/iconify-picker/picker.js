/**
 * iconify 图标选择器 - 外置 JS
 *
 * 本文件不经过 ThinkPHP 模板引擎，可以放心使用任何 JS 语法。
 * 由 view/api/iconify.html 通过 <script src> 引入。
 *
 * 依赖全局：jQuery ($)、layui 的 layer。
 * 本地化文案通过 window.QzIconPickerI18n 传入。
 */
(function () {
    'use strict';

    var COLLECTIONS = ['ri', 'fa6-solid', 'fa6-brands'];
    var COLLECTION_LABELS = {
        'fa6-solid': 'FontAwesome Solid',
        'fa6-brands': 'FontAwesome Brands',
        'ri': 'Remix Icon'
    };
    var PAGE_SIZE = 200;
    var ASSET_BASE = '/static/plugs/iconify-picker';

    /** 从 .iconify-picker-root 的 data-* 属性读取 i18n；找不到用默认值 */
    function getI18n($root) {
        return {
            choose: $root.data('i18n-choose') || '选择图标',
            searchPlaceholder: $root.data('i18n-search') || '输入关键词搜索，如 app、video、image',
            empty: $root.data('i18n-empty') || '没有匹配的图标',
            loading: $root.data('i18n-loading') || '加载中...',
            loadFailed: $root.data('i18n-load-failed') || '加载失败，请检查静态资源路径',
            searchFirst: $root.data('i18n-search-first') || '请输入关键词搜索图标'
        };
    }

    var collectionDataCache = {};
    var registeredCollections = {};
    var scriptLoaded = false;
    var scriptLoadingPromise = null;

    function loadIconifyScript() {
        if (scriptLoaded) return $.Deferred().resolve().promise();
        if (scriptLoadingPromise) return scriptLoadingPromise;
        var dfd = $.Deferred();
        scriptLoadingPromise = dfd.promise();
        var s = document.createElement('script');
        s.src = ASSET_BASE + '/iconify-icon.min.js';
        s.onload = function () { scriptLoaded = true; dfd.resolve(); };
        s.onerror = function () { dfd.reject(new Error('iconify-icon load failed')); };
        document.head.appendChild(s);
        return scriptLoadingPromise;
    }

    /** i-<col>-<name> → <col>:<name> */
    function parseIconClass(cls) {
        if (!cls || typeof cls !== 'string') return '';
        cls = cls.trim();
        if (cls.indexOf('i-') !== 0) return '';
        var rest = cls.substring(2);
        for (var i = 0; i < COLLECTIONS.length; i++) {
            var prefix = COLLECTIONS[i];
            if (rest.indexOf(prefix + '-') === 0) {
                return prefix + ':' + rest.substring(prefix.length + 1);
            }
        }
        return '';
    }

    function formatIconClass(col, name) { return 'i-' + col + '-' + name; }

    function loadCollection(prefix) {
        if (collectionDataCache[prefix]) {
            return $.Deferred().resolve(collectionDataCache[prefix]).promise();
        }
        return $.ajax({
            url: ASSET_BASE + '/' + prefix + '.json',
            dataType: 'json'
        }).then(function (data) {
            var names = data && data.icons ? Object.keys(data.icons) : [];
            collectionDataCache[prefix] = { json: data, names: names };
            if (!registeredCollections[prefix] && window.Iconify && window.Iconify.addCollection) {
                window.Iconify.addCollection(data);
                registeredCollections[prefix] = true;
            }
            return collectionDataCache[prefix];
        });
    }

    /** 暴露给外部的预加载函数（列表页 Icon 列用） */
    window.ensureIconifyAll = function () {
        return loadIconifyScript().then(function () {
            var tasks = COLLECTIONS.map(function (p) { return loadCollection(p); });
            return $.when.apply($, tasks);
        });
    };

    window.parseIconClass = parseIconClass;

    function refreshPreview($root) {
        var value = $root.find('.iconify-picker-input').val();
        var parsed = parseIconClass(value);
        $root.find('.iconify-picker-preview').attr('icon', parsed);
    }

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function openPicker($root) {
        var I18N = getI18n($root);
        var currentPrefix = COLLECTIONS[0];
        var currentKeyword = '';
        var currentNames = [];
        var currentPage = 1;

        var tabsHtml = COLLECTIONS.map(function (p, i) {
            return '<div class="iconify-dialog-tab' + (i === 0 ? ' is-active' : '') +
                   '" data-prefix="' + p + '">' + COLLECTION_LABELS[p] + '</div>';
        }).join('');

        var html =
            '<div class="iconify-dialog">' +
              '<div class="iconify-dialog-toolbar">' +
                '<div class="iconify-dialog-tabs">' + tabsHtml + '</div>' +
                '<input type="text" class="iconify-dialog-search" placeholder="' + escapeHtml(I18N.searchPlaceholder) + '">' +
                '<span class="iconify-dialog-count">—</span>' +
              '</div>' +
              '<div class="iconify-dialog-pages"></div>' +
              '<div class="iconify-dialog-grid"></div>' +
            '</div>';

        var layerIndex = layer.open({
            type: 1,
            title: I18N.choose,
            area: ['820px', '800px'],
            shadeClose: true,
            content: html,
            success: function ($container) {
                $container.find('.layui-layer-content').css('overflow', 'hidden');
                var $grid = $container.find('.iconify-dialog-grid');
                var $count = $container.find('.iconify-dialog-count');
                var $search = $container.find('.iconify-dialog-search');
                var $tabs = $container.find('.iconify-dialog-tab');
                var $pages = $container.find('.iconify-dialog-pages');

                function renderPages(total) {
                    var totalPage = Math.max(1, Math.ceil(total / PAGE_SIZE));
                    if (currentPage > totalPage) currentPage = totalPage;
                    if (totalPage <= 1) {
                        $pages.empty();
                        return;
                    }

                    var parts = [];
                    for (var i = 1; i <= totalPage; i++) {
                        var start = (i - 1) * PAGE_SIZE + 1;
                        var end = Math.min(i * PAGE_SIZE, total);
                        parts.push(
                            '<button type="button" class="iconify-dialog-page' + (i === currentPage ? ' is-active' : '') +
                            '" data-page="' + i + '">' + start + '-' + end + '</button>'
                        );
                    }
                    $pages.html(parts.join(''));
                }

                function renderGrid() {
                    var keyword = (currentKeyword || '').toLowerCase().trim();
                    var filtered = keyword
                        ? currentNames.filter(function (n) { return n.toLowerCase().indexOf(keyword) !== -1; })
                        : currentNames;

                    if (filtered.length === 0) {
                        $pages.empty();
                        $grid.html('<div class="iconify-dialog-empty">' + I18N.empty + '</div>');
                        $count.text('0');
                        return;
                    }
                    renderPages(filtered.length);
                    var start = (currentPage - 1) * PAGE_SIZE;
                    var end = Math.min(start + PAGE_SIZE, filtered.length);
                    var pageNames = filtered.slice(start, end);
                    var parts = pageNames.map(function (name) {
                        var className = formatIconClass(currentPrefix, name);
                        return '<div class="iconify-dialog-item" data-cls="' + className + '" title="' + className + '">' +
                               '<iconify-icon icon="' + currentPrefix + ':' + name + '"></iconify-icon>' +
                               '<span class="iconify-dialog-item-name">' + name + '</span>' +
                               '</div>';
                    }).join('');
                    $grid.html(parts);
                    $count.text((start + 1) + '-' + end + ' / ' + filtered.length);
                }

                function switchPrefix(prefix) {
                    currentPrefix = prefix;
                    currentPage = 1;
                    $tabs.removeClass('is-active').filter('[data-prefix="' + prefix + '"]').addClass('is-active');
                    $pages.empty();
                    $grid.html('<div class="iconify-dialog-loading">' + I18N.loading + '</div>');
                    loadCollection(prefix).done(function (data) {
                        currentNames = data.names;
                        renderGrid();
                    }).fail(function () {
                        $grid.html('<div class="iconify-dialog-empty">' + I18N.loadFailed + ' ' + ASSET_BASE + '/</div>');
                    });
                }

                switchPrefix(currentPrefix);

                $tabs.on('click', function () { switchPrefix($(this).data('prefix')); });
                $pages.on('click', '.iconify-dialog-page', function () {
                    currentPage = Number($(this).data('page')) || 1;
                    renderGrid();
                });

                var searchTimer;
                $search.on('input', function () {
                    var val = $(this).val();
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(function () {
                        currentKeyword = val;
                        currentPage = 1;
                        renderGrid();
                    }, 150);
                });

                $grid.on('click', '.iconify-dialog-item', function () {
                    var cls = $(this).data('cls');
                    $root.find('.iconify-picker-input').val(cls).trigger('change');
                    refreshPreview($root);
                    layer.close(layerIndex);
                });
            }
        });
    }

    /** 初始化所有 .iconify-picker-root 挂载点 */
    function init() {
        loadIconifyScript().done(function () {
            $('.iconify-picker-root').each(function () {
                var $root = $(this);
                if ($root.data('iconify-picker-inited')) return;
                $root.data('iconify-picker-inited', true);

                refreshPreview($root);
                $root.find('.iconify-picker-input').on('input change', function () {
                    refreshPreview($root);
                });
                $root.find('.iconify-picker-trigger').on('click', function () {
                    openPicker($root);
                });
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // 暴露 re-init，modal 动态插入 DOM 后可以手动调用
    window.iconifyPickerInit = init;
})();
