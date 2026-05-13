(function (window) {
    var iconPrefixes = ['ri', 'fa6-solid', 'fa6-brands'];

    if (!window.parseIconClass) {
        window.parseIconClass = function (cls) {
            if (!cls || typeof cls !== 'string') return '';
            cls = cls.trim();
            if (cls.indexOf('i-') !== 0) return '';

            var rest = cls.substring(2);
            for (var i = 0; i < iconPrefixes.length; i++) {
                var prefix = iconPrefixes[i];
                if (rest.indexOf(prefix + '-') === 0) {
                    return prefix + ':' + rest.substring(prefix.length + 1);
                }
            }
            return '';
        };
    }

    window.iconTpl = function (data, field) {
        var value = data ? data[field || 'icon'] : '';
        var parsed = window.parseIconClass(value);
        if (!parsed) return '<span style="color:#cbd5e1">-</span>';
        return '<iconify-icon icon="' + parsed + '" style="font-size:16px;color:#44556c;vertical-align:middle"></iconify-icon>';
    };

    window.colorTpl = function (data, field) {
        var value = data ? data[field || 'color'] : '';
        if (!value) return '<span style="color:#cbd5e1">-</span>';
        return '<span class="table-color-cell">' +
            '<span class="table-color-dot" style="background:' + value + '"></span>' +
            '<span class="table-color-text">' + value + '</span>' +
            '</span>';
    };
})(window);

(function (window, document) {
    var base = '/static/plugs/iconify-picker/';
    var files = ['picker.js', 'unoicon.js'];

    function hasScript(src) {
        return Array.prototype.some.call(document.scripts, function (script) {
            return (script.getAttribute('src') || '').indexOf(src) > -1;
        });
    }

    function load(index) {
        if (index >= files.length) {
            if (window.qsyUnoIconInit) window.qsyUnoIconInit();
            return;
        }

        var src = base + files[index];
        if (hasScript(src)) {
            load(index + 1);
            return;
        }

        var script = document.createElement('script');
        script.src = src;
        script.onload = function () {
            load(index + 1);
        };
        document.body.appendChild(script);
    }

    load(0);
})(window, document);

(function (window, document) {
    function getInput(field, trigger) {
        var scope = trigger.closest('form') || document;
        return scope.querySelector('[name="' + field + '"]') || document.querySelector('[name="' + field + '"]');
    }

    function getPreview(field, trigger) {
        var scope = trigger.closest('form') || document;
        return trigger.querySelector('[data-color-preview]') ||
            scope.querySelector('[data-color-preview="' + field + '"]') ||
            document.querySelector('[data-color-preview="' + field + '"]');
    }

    function isAlphaColor(field, trigger, value) {
        if (trigger.getAttribute('data-alpha') === 'true') return true;
        if (/rgba?\(/i.test(value || '')) return true;
        return /(^|_)bg($|_)|background/i.test(field);
    }

    function initColorPickers(root) {
        if (!window.layui) return;

        layui.use(['colorpicker'], function () {
            var colorpicker = layui.colorpicker;
            var $ = layui.$;
            var scope = root || document;
            var nodes = [];
            if (scope.nodeType === 1 && scope.matches('[data-color]')) nodes.push(scope);
            nodes = nodes.concat(Array.prototype.slice.call(scope.querySelectorAll('[data-color]')));

            Array.prototype.forEach.call(nodes, function (trigger) {
                if (trigger.getAttribute('data-color-ready') === '1') return;

                var field = trigger.getAttribute('data-color');
                var input = getInput(field, trigger);
                if (!field || !input) return;

                var preview = getPreview(field, trigger);
                var defaultColor = trigger.getAttribute('data-default') || input.getAttribute('data-default') || '#3176ff';
                var alpha = isAlphaColor(field, trigger, input.value || defaultColor);

                function updatePreview(value) {
                    if (preview) preview.style.background = value || defaultColor;
                }

                trigger.setAttribute('data-color-ready', '1');
                updatePreview(input.value);

                colorpicker.render({
                    elem: trigger,
                    color: input.value || defaultColor,
                    predefine: true,
                    alpha: alpha,
                    format: alpha ? 'rgb' : 'hex',
                    change: updatePreview,
                    done: function (color) {
                        input.value = color || '';
                        $(input).trigger('change');
                        updatePreview(color);
                    }
                });

                $(input).on('input change', function () {
                    updatePreview(this.value);
                });
            });
        });
    }

    window.qsyColorPickerInit = initColorPickers;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initColorPickers(document);
        });
    } else {
        initColorPickers(document);
    }

    if (window.MutationObserver) {
        new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                Array.prototype.forEach.call(mutation.addedNodes, function (node) {
                    if (node.nodeType === 1 && (node.matches('[data-color]') || node.querySelector('[data-color]'))) {
                        initColorPickers(node.matches('[data-color]') ? node.parentNode : node);
                    }
                });
            });
        }).observe(document.documentElement, {childList: true, subtree: true});
    }
})(window, document);
