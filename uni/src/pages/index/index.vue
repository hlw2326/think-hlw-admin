<template>
    <hlw-page is-bar title="首页">
        <view class="container">
            <hlw-ad type="banner" :unit-id="banner_unit_id" />

            <!-- 演示激励广告 -->
            <view class="demo-card">
                <view class="demo-title">激励视频广告演示</view>
                <view class="demo-desc">使用新开发的 hlw-reward-ad 组件，支持提前静默预加载以优化首帧加载体验。</view>

                <hlw-reward-ad :unit-id="reward_unit_id" @onHandle="handleRewardAd">
                    <hlw-button type="primary" round> 点击观看广告获取金币 </hlw-button>
                </hlw-reward-ad>
            </view>
        </view>
    </hlw-page>
</template>

<script setup lang="ts">
import { onLoad, onShareAppMessage, onShareTimeline, onShow } from "@dcloudio/uni-app";
import { useHlwAd } from "@hlw-uni/mp-vue";
import { useAppShare, useAd, useConfig, useUser } from "@/core";

const { getUserInfo } = useUser();
const { banner_unit_id, popup_unit_id, reward_unit_id } = useAd();
const { setAdPopup, showAdPopup } = useHlwAd();
const { base, getConfig } = useConfig();
const share = useAppShare();

onLoad(() => {
    setAdPopup(popup_unit_id.value);
});

onShow(async () => {
    await getConfig();
    getUserInfo();
    setAdPopup(popup_unit_id.value);
    showAdPopup();
});

function handleRewardAd(res: { ok: boolean; isEnded: boolean; err?: any }) {
    if (res.ok && res.isEnded) {
        hlw.$msg.success("看完广告，奖励已发放");
    } else {
        hlw.$msg.toast("播放中途退出或拉起失败");
    }
}

onShareAppMessage(share.appMessage);
onShareTimeline(share.timeline);
</script>

<style lang="scss" scoped>
.container {
    padding: 30rpx;
    display: flex;
    flex-direction: column;
    gap: 30rpx;
}

.demo-card {
    background: var(--surface-card, #ffffff);
    padding: 40rpx 30rpx;
    border-radius: var(--radius-lg, 24rpx);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20rpx;
    text-align: center;
}

.demo-title {
    font-size: 32rpx;
    font-weight: 600;
    color: #1a1a1a;
}

.demo-desc {
    font-size: 26rpx;
    color: #666666;
    line-height: 1.5;
    margin-bottom: 10rpx;
}
</style>
