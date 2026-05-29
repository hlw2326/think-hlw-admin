<template>
    <view class="profile-card">
        <view class="profile-header">
            <view class="avatar-section">
                <hlw-avatar-upload class="avatar-wrapper" :src="user?.avatar_url" :name="user?.nickname" @onAvatar="updateAvatar" />
                <view class="user-info">
                    <hlw-nickname class="username" :text="user?.nickname || ''" @onNick="updateNickname" />
                    <view class="id-wrapper" v-copy="user?.id">
                        <text class="user-id">ID: {{ user?.id || "--" }}</text>
                        <view class="copy-id">
                            <span class="iconfont icon-copy"></span>
                        </view>
                    </view>
                </view>

                <view class="vip-tag">
                    <span class="iconfont icon-gift vip-icon" />
                    <text class="vip-text">永久免费</text>
                </view>
            </view>
        </view>

        <view class="divider" />

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
    </view>
</template>

<script setup lang="ts">
import { useUser } from "@/core";

const { user, updateAvatar, updateNickname } = useUser();
</script>

<style scoped lang="scss">
.profile-card {
    border: 1rpx solid var(--border-color-light);
    border-radius: var(--card-radius);
    background: #ffffff;
}

.profile-header {
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--card-padding);
}

.avatar-section {
    position: relative;
    display: flex;
    align-items: center;
    flex: 1;
    min-width: 0;
}

.avatar-wrapper {
    flex-shrink: 0;
    margin-right: 46rpx;
}

.user-info {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.username {
    overflow: hidden;
    max-width: 320rpx;
    margin-bottom: 12rpx;
    color: #0f172a;
    font-size: var(--font-md);
    font-weight: 500;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.id-wrapper {
    display: flex;
    align-items: center;
}

.user-id {
    margin-right: 12rpx;
    color: #64748b;
    font-size: var(--font-sm);
}

.copy-id {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4rpx 8rpx;
    border-radius: 8rpx;
    background: #f1f5f9;
    color: #64748b;
}

.copy-id .iconfont {
    font-size: var(--font-22);
}

.vip-tag {
    position: absolute;
    right: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 6rpx 16rpx;
    border: 1rpx solid var(--primary-color, #3b82f6);
    border-radius: 999rpx;
    background: #eff6ff;
}

.vip-icon {
    color: var(--primary-color, #3b82f6);
    font-size: var(--font-base);
    line-height: 1;
    margin-right: 5rpx;
}

.vip-text {
    margin-left: 4rpx;
    color: var(--primary-color, #3b82f6);
    font-size: var(--font-22);
    letter-spacing: 3rpx;
}

.divider {
    height: 1rpx;
    background: var(--border-color-light);
}

.stats-section {
    display: flex;
    justify-content: space-around;
    padding: var(--card-padding);
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
    margin-bottom: 8rpx;
    color: #2b2b2b;
    font-size: var(--font-lg, 32rpx);
    font-weight: 600;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.stat-label {
    color: #8c8c8c;
    font-size: var(--font-sm, 26rpx);
    letter-spacing: 1rpx;
}
</style>
