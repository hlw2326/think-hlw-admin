<template>
    <hlw-page is-bar title="首页">
        <view class="container">
            <hlw-custom-ad type="banner" />

            <!-- 演示激励广告 -->
            <view class="demo-card">
                <view class="demo-title">激励视频广告演示</view>
                <view class="demo-desc">使用新开发的 hlw-reward-ad 组件，支持提前静默预加载以优化首帧加载体验。</view>

                <hlw-reward-ad :unit-id="reward_unit_id" @onHandle="handleRewardAd">
                    <hlw-button type="primary" round> 点击观看广告获取金币 </hlw-button>
                </hlw-reward-ad>
            </view>

            <!-- 邀请好友分享 -->
            <view class="demo-card">
                <view class="demo-title">专属邀请码</view>
                <view class="demo-desc">生成您专属的小程序二维码，邀请好友扫码注册即可建立邀请绑定关系。</view>
                <hlw-button type="success" round @click="showShareQrCode">
                    获取邀请二维码
                </hlw-button>
            </view>

            <!-- 二维码弹窗 -->
            <hlw-popup v-model:show="qrVisible" title="专属分享二维码" position="center">
                <view class="qr-container">
                    <image v-if="qrCodeUrl" class="qr-image" :src="qrCodeUrl" :show-menu-by-longpress="true" />
                    <text class="qr-tip">长按二维码保存或发送给朋友</text>
                </view>
            </hlw-popup>
        </view>
    </hlw-page>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { onLoad, onShareAppMessage, onShareTimeline, onShow } from "@dcloudio/uni-app";
import { setPopupAd, showPopupAd } from "@hlw-uni/mp-vue";
import { useAppShare, useAd, useConfig, useUser } from "@/core";

const { getUserInfo, is_login, login } = useUser();
const { popup_unit_id, reward_unit_id } = useAd();
const { getConfig } = useConfig();
const share = useAppShare();

const qrVisible = ref(false);
const qrCodeUrl = ref("");

onLoad(() => {
    setPopupAd(popup_unit_id.value);
});

onShow(async () => {
    await getConfig();
    getUserInfo();
    setPopupAd(popup_unit_id.value);
    showPopupAd();
});

async function showShareQrCode() {
    if (!is_login.value) {
        try {
            await login();
        } catch (error) {
            hlw.$msg.toast("登录失败，请重试");
            return;
        }
    }

    hlw.$msg.showLoading("正在生成...");
    try {
        const res = await service.v1.invite.qrcode();
        if (res.code === 1 && res.data?.qrcode_url) {
            qrCodeUrl.value = res.data.qrcode_url;
            qrVisible.value = true;
        } else {
            hlw.$msg.toast(res.info || "生成分享二维码失败");
        }
    } catch (error: any) {
        hlw.$msg.toast(error?.message || "请求失败");
    } finally {
        hlw.$msg.hideLoading();
    }
}

async function handleRewardAd(res: { success: boolean; isEnded: boolean; err?: any }) {
    if (res.success && res.isEnded) {
        hlw.$msg.success("看完广告，奖励已发放");
    } else if (res.err) {
        hlw.$msg.toast("广告播放失败，请稍后重试");
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

.qr-container {
    padding: 40rpx;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #ffffff;
}

.qr-image {
    width: 360rpx;
    height: 360rpx;
    background-color: #f1f5f9;
    border-radius: 12rpx;
}

.qr-tip {
    margin-top: 24rpx;
    font-size: 24rpx;
    color: #94a3b8;
    text-align: center;
}
</style>
