<template>
    <hlw-page is-back :is-bar="true" title="个性化设置">
        <view class="container">
            <!-- 字体大小设置 -->
            <view class="section-title">字体大小</view>
            <view class="theme-list">
                <view v-for="item in fontSizePresets" :key="item.id" class="theme-item" @tap="selectFontSize(item.id)">
                    <view class="theme-left">
                        <view class="theme-icon" style="color: #6366f1">
                            <span class="i-fa6-solid-font theme-icon-symbol" />
                        </view>
                        <text class="theme-text">{{ item.name }}</text>
                        <text class="theme-preview" :class="item.class">（样例字体）</text>
                    </view>
                    <span v-if="themeStore.fontSize === item.id" class="i-fa6-solid-circle-check checked-icon" />
                    <span v-else class="i-fa6-solid-chevron-right menu-arrow" />
                </view>
            </view>

            <!-- 字体样式设置 -->
            <view class="section-title">字体样式</view>
            <view class="theme-list">
                <view v-for="item in fontFamilyPresets" :key="item.id" class="theme-item" @tap="selectFontFamily(item.id)">
                    <view class="theme-left">
                        <view class="theme-icon" style="color: #10b981">
                            <span class="i-fa6-solid-font theme-icon-symbol" />
                        </view>
                        <text class="theme-text">{{ item.name }}</text>
                        <text class="theme-preview" :class="item.class">（Aa/你好）</text>
                    </view>
                    <span v-if="themeStore.fontFamily === item.id" class="i-fa6-solid-circle-check checked-icon" />
                    <span v-else class="i-fa6-solid-chevron-right menu-arrow" />
                </view>
            </view>
        </view>
    </hlw-page>
</template>

<script setup lang="ts">
import { useTheme, fontFamilyPresets, fontSizePresets } from "@/core";

const { fontSize, setFontSize, fontFamily, setFontFamily, store: themeStore } = useTheme();

function selectFontSize(sizeId: string) {
    setFontSize(sizeId);
    uni.showToast({
        title: "设置成功",
        icon: "success",
        duration: 1000,
    });
}

function selectFontFamily(fontId: string) {
    setFontFamily(fontId);
    uni.showToast({
        title: "设置成功",
        icon: "success",
        duration: 1000,
    });
}
</script>

<style scoped lang="scss">
.section-title {
    padding: 0rpx 12rpx 0rpx;
    color: var(--text-muted, #64748b);
    font-size: var(--font-sm, 24rpx);
    font-weight: 500;
    letter-spacing: 0.5px;
}

.theme-list {
    overflow: hidden;
    padding: 12rpx 0;
    border: 1rpx solid var(--border-color-light);
    border-radius: var(--card-radius);
    background: #ffffff;
    margin-bottom: 20rpx;
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

.theme-preview {
    margin-left: 16rpx;
    color: var(--text-muted, #94a3b8);
    font-size: var(--font-sm);
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
