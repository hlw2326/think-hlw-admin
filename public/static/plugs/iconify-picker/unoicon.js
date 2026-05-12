(function (window, $) {
    var observerStarted = false;

    function init() {
        $('[data-unoicon]').each(function () {
            var $host = $(this);
            if ($host.data('unoicon-inited')) return;
            $host.data('unoicon-inited', true);

            var field = $host.data('unoicon') || 'icon';
            var value = $host.data('value') || '';
            var placeholder = $host.data('placeholder') || '请输入或选择图标，如 i-fa6-solid-image';

            $host.addClass('iconify-picker-root').html(
                '<div class="flex" style="gap:10px;align-items:center">' +
                '<div class="flex-1">' +
                '<input name="' + field + '" value="' + value + '" placeholder="' + placeholder + '" class="layui-input iconify-picker-input">' +
                '</div>' +
                '<span style="padding:0 12px;min-width:45px;margin:0" class="layui-btn layui-btn-primary">' +
                '<iconify-icon class="iconify-picker-preview" icon="" style="font-size:1.2em;margin:0;float:none;vertical-align:middle"></iconify-icon>' +
                '</span>' +
                '<button type="button" class="layui-btn layui-btn-primary iconify-picker-trigger" style="margin:0">选择图标</button>' +
                '</div>'
            );
        });

        if (window.iconifyPickerInit) window.iconifyPickerInit();
    }

    function observe() {
        if (observerStarted || !window.MutationObserver) return;
        observerStarted = true;
        new MutationObserver(function (mutations) {
            var shouldInit = false;
            mutations.forEach(function (mutation) {
                $(mutation.addedNodes).each(function () {
                    if (this.nodeType !== 1) return;
                    if ($(this).is('[data-unoicon]') || $(this).find('[data-unoicon]').length) {
                        shouldInit = true;
                    }
                });
            });
            if (shouldInit) init();
        }).observe(document.body, { childList: true, subtree: true });
    }

    window.qsyUnoIconInit = init;
    $(function () {
        init();
        observe();
    });
})(window, jQuery);
