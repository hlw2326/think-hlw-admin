# Iconify Picker 使用说明

用于后台表单选择 UnoCSS / Iconify 图标，最终保存为 `i-fa6-solid-image` 这类 class 字符串。

## 全局加载

项目已在下面两个文件里统一加载图标选择器：

```text
/static/extra/style.css
/static/extra/script.js
```

业务页面通常不需要再手动写：

```html
<link rel="stylesheet" href="/static/plugs/iconify-picker/picker.css">
<script src="/static/plugs/iconify-picker/picker.js"></script>
<script src="/static/plugs/iconify-picker/unoicon.js"></script>
```

## 表单调用

页面里只需要放一个挂载点：

```html
<div data-unoicon="icon" data-value="{$vo.icon|default='i-fa6-solid-wand-magic-sparkles'}"></div>
```

说明：

- `data-unoicon`：生成的 input 字段名，如 `icon`。
- `data-value`：默认值或编辑时已有值。
- `data-placeholder`：可选，占位文案。

示例：

```html
<div data-unoicon="badge_icon" data-value="{$vo.badge_icon|default=''}" data-placeholder="请选择角标图标"></div>
```

## 列表渲染

表格初始化前可以预加载图标：

```js
if (window.ensureIconifyAll) window.ensureIconifyAll();
```

Layui 表格列：

```js
{ field: 'icon', title: '图标', width: 80, align: 'center', templet: window.iconTpl }
```

## 支持集合

- `i-ri-*`
- `i-fa6-solid-*`
- `i-fa6-brands-*`

弹窗默认打开 `ri` 集合。为了避免 FontAwesome Solid 图标过多导致卡顿，弹窗未输入关键词时不会渲染图标；输入关键词后最多展示前 160 个匹配结果。

## 常用 App 图标

- `i-ri-apps-2-fill`
- `i-ri-apps-fill`
- `i-ri-app-store-fill`
