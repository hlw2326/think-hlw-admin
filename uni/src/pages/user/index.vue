<template>
    <hlw-page :is-bar="true" title="我的">
        <view class="container">
            <comp-profile />

            <comp-stats />

            <comp-menu @open-official="openOfficial" />
            <hlw-ad type="custom" :unit-id="custom_unit_id" />
        </view>
    </hlw-page>
    <hlw-add-mini :show="add_mini_visible" @close="closeAddMini" />
    <hlw-ad type="grid" :unit-id="grid_unit_id" placement="left-bottom" custom-style="left: 10rpx;" />
    <official-popup v-if="official_visible && contact.official_qrcode" :qrcode="contact.official_qrcode" @close="closeOfficial" />
</template>

<script setup lang="ts">
import { onShareAppMessage, onShareTimeline, onShow } from "@dcloudio/uni-app";
import { ref } from "vue";
import { useAppShare, useAd, useUser, useConfig } from "@/core";
import CompMenu from "./comps/menu.vue";
import CompProfile from "./comps/profile.vue";
import CompStats from "./comps/stats.vue";
import OfficialPopup from "./comps/official-popup.vue";

const { getUserInfo } = useUser();
const { contact } = useConfig();
const { grid_unit_id, custom_unit_id } = useAd();
const share = useAppShare();

const add_mini_visible = ref(true);
const official_visible = ref(false);

onShareAppMessage(share.appMessage);
onShareTimeline(share.timeline);

onShow(() => {
    getUserInfo();
});

function closeAddMini() {
    add_mini_visible.value = false;
}

function openOfficial() {
    official_visible.value = true;
}

function closeOfficial() {
    official_visible.value = false;
}
</script>
