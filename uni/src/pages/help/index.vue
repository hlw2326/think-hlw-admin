<template>
    <hlw-page is-back :is-bar="true" title="帮助中心">
        <view class="container">
            <view v-if="steps && steps.length > 0" class="help-card">
                <view class="card-title">
                    <view class="title-line" />
                    <text class="title-text">怎么使用</text>
                </view>
                <view class="step-list">
                    <text v-for="(item, index) in steps" :key="item" class="step-item">{{ index + 1 }}. {{ item }}</text>
                </view>
            </view>

            <view class="help-card">
                <view class="card-title">
                    <view class="title-line" />
                    <text class="title-text">常见问题</text>
                </view>
                <view class="faq-list">
                    <view v-for="item in faqs" :key="item.question" class="faq-item">
                        <text class="faq-question">{{ item.question }}</text>
                        <text class="faq-answer">{{ item.answer }}</text>
                    </view>
                </view>
            </view>
            <hlw-custom-service title="还没解决？" desc="把遇到的问题发给客服" :contact="contact" />
            <hlw-custom-ad type="custom" />
        </view>
    </hlw-page>
</template>

<script setup lang="ts">
import { computed, ref } from "vue";
import { onShareAppMessage, onShareTimeline, onShow } from "@dcloudio/uni-app";
import HlwCustomService from "@hlw-uni/mp-vue/src/components/hlw-custom-service/hlw-custom-service.vue";
import { useAppShare, useConfig } from "@/core";

const { contact } = useConfig();
const share = useAppShare("/pages/help/index");

const steps = ref<string[]>([]);
const faqList = ref<IHelp.Faq[]>([]);

const faqs = computed(() =>
    faqList.value.map((item) => ({
        question: item.question,
        answer: item.answer,
    })),
);

onShareAppMessage(share.appMessage);
onShareTimeline(share.timeline);

onShow(() => {
    getHelpList();
});

async function getHelpList() {
    try {
        const res = await service.v1.help.list();
        if (res.code !== 1 || !res.data) {
            hlw.$msg.toast(res.info || "帮助内容加载失败");
            return;
        }
        if (res.data.steps?.length) steps.value = res.data.steps;
        if (res.data.faqs?.length) faqList.value = res.data.faqs;
    } catch (error) {
        console.warn("[help] list failed", error);
        hlw.$msg.toast("帮助内容加载失败");
    }
}
</script>

<style scoped lang="scss">
.help-card {
    padding: var(--card-padding);
    border: 1rpx solid var(--border-color-light);
    border-radius: var(--card-radius);
    background: #ffffff;
}

.card-title {
    display: flex;
    align-items: center;
    margin-bottom: 28rpx;
}

.title-line {
    width: 6rpx;
    height: 28rpx;
    margin-right: 16rpx;
    border-radius: 6rpx;
    background: var(--primary-color, #3b82f6);
}

.title-text {
    color: #1f2937;
    font-size: var(--font-md);
    line-height: 1.25;
}

.step-list {
    display: flex;
    flex-direction: column;
    gap: 18rpx;
}

.step-item {
    display: block;
    color: #334155;
    font-size: var(--font-base);
    line-height: 1.5;
}

.faq-list {
    display: flex;
    flex-direction: column;
}

.faq-item {
    padding: 24rpx 0;
    border-top: 1rpx solid var(--border-color-light);
}

.faq-item:first-child {
    padding-top: 0;
    border-top: 0;
}

.faq-item:last-child {
    padding-bottom: 0;
}

.faq-question {
    display: block;
    margin-bottom: 10rpx;
    color: #334155;
    font-size: var(--font-base);
    line-height: 1.35;
}

.faq-answer {
    display: block;
    color: #94a3b8;
    font-size: var(--font-sm);
    line-height: 1.55;
}
</style>
