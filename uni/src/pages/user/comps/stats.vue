<template>
    <view class="stats-section">
        <view class="stat-item">
            <text class="stat-value">0</text>
            <text class="stat-label">会员</text>
        </view>
        <view class="stat-item">
            <text class="stat-value">0</text>
            <text class="stat-label">积分</text>
        </view>
    </view>
</template>

<script setup lang="ts">
import { useUser } from "@/core";

const { user } = useUser();
</script>

<style scoped lang="scss">
.stats-section {
    display: flex;
    justify-content: space-around;
    padding: var(--card-padding);
    border: 1rpx solid var(--border-color-light);
    border-radius: var(--card-radius);
    background: #ffffff;
}

.stat-item {
    position: relative;
    display: flex;
    flex: 1;
    min-width: 0;
    flex-direction: column;
    align-items: center;
}

.stat-item + .stat-item::before {
    position: absolute;
    top: 10rpx;
    bottom: 10rpx;
    left: 0;
    width: 2rpx;
    background: var(--border-color-light);
    content: "";
}

.stat-value {
    max-width: 100%;
    overflow: hidden;
    margin-bottom: 12rpx;
    color: #2b2b2b;
    font-size: var(--font-38);
    font-weight: 600;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.stat-label {
    color: #8c8c8c;
    font-size: var(--font-22);
}
</style>
