<template>
    <view class="menu-wrapper">
        <hlw-menu :items="menuItems" :border="true" @click="handleItemClick" />

        <!-- 使用 root-portal 将弹窗挂载到微信小程序页面根节点，以完美覆盖自定义导航栏 -->
        <root-portal v-if="official_visible && contact.official_qrcode">
            <official-popup :qrcode="contact.official_qrcode" @close="closeOfficial" />
        </root-portal>
    </view>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import { useConfig } from "@/core";
import OfficialPopup from "./official-popup.vue";
import type { HlwMenuItem } from "@hlw-uni/mp-vue";

const { contact } = useConfig();
const official_visible = ref(false);

const menuItems = computed<HlwMenuItem[]>(() => [
    {
        icon: "i-fa6-solid-circle-question",
        iconTheme: "blue",
        label: "帮助中心",
        url: "/pages/help/index",
    },
    {
        icon: "i-fa6-brands-weixin",
        iconTheme: "emerald",
        label: "关注公众号",
        tag: "领奖励",
        tagTheme: "blue",
        visible: !!contact.value.official_qrcode,
        action: "official",
    },
    {
        icon: "i-fa6-solid-palette",
        iconTheme: "purple",
        label: "主题设置",
        url: "/pages/user/theme/index",
    },
    {
        icon: "i-fa6-solid-headset",
        iconTheme: "cyan",
        label: "联系客服",
        openType: "contact",
        sendMessageTitle: contact.value.send_message_title,
        sendMessagePath: contact.value.send_message_path,
        sendMessageImg: contact.value.send_message_img,
        showMessageCard: contact.value.show_message_card,
    },
]);

function handleItemClick(item: any) {
    if (item.action === "official") {
        official_visible.value = true;
    }
}

function closeOfficial() {
    official_visible.value = false;
}
</script>

<style scoped lang="scss"></style>
