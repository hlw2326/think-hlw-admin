<template>
    <hlw-status-bar />
    <hlw-page :is-bar="false" title="首页">
        <view class="container">
            <hlw-ad type="banner" :unit-id="banner_unit_id" />
        </view>
    </hlw-page>
</template>

<script setup lang="ts">
import { onLoad, onShareAppMessage, onShareTimeline, onShow } from "@dcloudio/uni-app";
import { useHlwAd } from "@hlw-uni/mp-vue";
import { useAppShare, useAd, useConfig, useUser } from "@/core";

const { getUserInfo } = useUser();
const { banner_unit_id, popup_unit_id } = useAd();
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

onShareAppMessage(share.appMessage);
onShareTimeline(share.timeline);
</script>
