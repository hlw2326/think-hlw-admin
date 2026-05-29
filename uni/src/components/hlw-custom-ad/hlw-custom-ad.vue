<template>
    <hlw-ad
        v-if="unitId"
        :type="props.type"
        :unit-id="unitId"
        :placement="props.placement"
        :custom-style="props.customStyle"
        :custom-class="props.customClass"
    />
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useAd } from "@/core/ad";

defineOptions({ name: "HlwCustomAd" });

type GridPlacement = "left-top" | "right-top" | "left-middle" | "right-middle" | "left-bottom" | "right-bottom" | "center";

interface Props {
    /** 广告类型 — 仅展示型（banner / grid / custom），默认 custom */
    type?: "banner" | "grid" | "custom";
    /** grid 广告悬浮位置，默认 center */
    placement?: GridPlacement;
    /** 自定义样式（合并到根元素） */
    customStyle?: string;
    /** 自定义 class */
    customClass?: string;
}

const props = withDefaults(defineProps<Props>(), {
    type: "custom",
    placement: "center",
    customStyle: "",
    customClass: "",
});

const { banner_unit_id, grid_unit_id, custom_unit_id } = useAd();

const unitId = computed(() => {
    if (props.type === "grid") {
        return grid_unit_id.value;
    }
    if (props.type === "banner") {
        return banner_unit_id.value;
    }
    if (props.type === "custom") {
        return custom_unit_id.value || banner_unit_id.value;
    }
    return "";
});
</script>
