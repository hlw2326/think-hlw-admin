<template>
    <hlw-page is-back :is-bar="true" title="主题设置">
        <view class="container">
            <view class="theme-list">
                <view 
                    v-for="item in themePresets" 
                    :key="item.id" 
                    class="theme-item" 
                    @tap="selectTheme(item.id)"
                >
                    <view class="theme-left">
                        <view class="theme-icon" :style="{ color: getIconColor(item.id) }">
                            <text class="i-fa6-solid-palette theme-icon-symbol" />
                        </view>
                        <text class="theme-text">{{ item.name }}</text>
                    </view>
                    <text v-if="theme === item.id" class="i-fa6-solid-circle-check checked-icon" />
                    <text v-else class="i-fa6-solid-chevron-right menu-arrow" />
                </view>
            </view>
        </view>
    </hlw-page>
</template>

<script setup lang="ts">
import { useTheme, themePresets } from "@/core";

const { theme, store } = useTheme();

function getIconColor(id: string) {
    const colors: Record<string, string> = {
        "white-theme": "#334155",  // 白色主题用深灰色图标，显得干净高级
        "light-theme": "#3b82f6",  // 简洁主题用蓝色图标，代表简洁现代
        "mono-theme": "#6366f1",   // 单色主题用靛蓝色图标
        "color-theme": "#10b981",  // 颜色主题用绿色图标
    };
    return colors[id] || "#3b82f6";
}

function selectTheme(themeId: string) {
    store.theme = themeId;
    uni.showToast({
        title: "设置成功",
        icon: "success",
        duration: 1000,
    });
}
</script>

<style scoped lang="scss">
.theme-list {
    overflow: hidden;
    padding: 12rpx 0;
    border: 1rpx solid var(--border-color-light);
    border-radius: var(--card-radius);
    background: #ffffff;
}

.theme-item {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 32rpx 36rpx;
    background: transparent;
    line-height: 46rpx;
}

.theme-item:not(:last-child)::after {
    position: absolute;
    right: 36rpx;
    bottom: 0;
    left: 36rpx;
    height: 2rpx;
    background: var(--border-color-light);
    content: "";
}

.theme-item:active {
    background-color: rgba(49, 118, 255, 0.05);
}

.theme-left {
    display: flex;
    align-items: center;
    min-width: 0;
}

.theme-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48rpx;
    height: 46rpx;
    flex-shrink: 0;
}

.theme-icon-symbol {
    font-size: var(--font-base);
    line-height: 1;
}

.theme-text {
    margin-left: 24rpx;
    color: #333333;
    font-size: var(--font-base);
    letter-spacing: 0.3px;
}

.menu-arrow {
    display: flex;
    align-items: center;
    justify-content: center;
    color: #bebebe;
    font-size: var(--font-sm);
}

.checked-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    font-size: var(--font-base);
}
</style>
