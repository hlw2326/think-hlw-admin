import { computed } from "vue";
import type { ComputedRef } from "vue";
import { useConfig } from "@/core/config";

export function useAd(): {
    banner_unit_id: ComputedRef<string>;
    grid_unit_id: ComputedRef<string>;
    custom_unit_id: ComputedRef<string>;
    reward_unit_id: ComputedRef<string>;
    popup_unit_id: ComputedRef<string>;
} {
    const { ad } = useConfig();

    const banner_unit_id = computed(() => {
        if (ad.value.ad_global_enabled !== 1 || ad.value.ad_enabled_banner !== 1) return "";
        return ad.value.banner_unit_id || "";
    });

    const grid_unit_id = computed(() => {
        if (ad.value.ad_global_enabled !== 1 || ad.value.ad_enabled_grid !== 1) return "";
        return ad.value.grid_unit_id || "";
    });

    const custom_unit_id = computed(() => {
        if (ad.value.ad_global_enabled !== 1 || ad.value.ad_enabled_custom !== 1) return "";
        return ad.value.custom_unit_id || "";
    });

    const reward_unit_id = computed(() => {
        if (ad.value.ad_global_enabled !== 1 || ad.value.ad_enabled_reward !== 1) return "";
        return ad.value.reward_unit_id || "";
    });

    const popup_unit_id = computed(() => {
        if (ad.value.ad_global_enabled !== 1 || ad.value.ad_enabled_popup !== 1) return "";
        return ad.value.popup_unit_id || "";
    });

    return {
        banner_unit_id,
        grid_unit_id,
        custom_unit_id,
        reward_unit_id,
        popup_unit_id,
    };
}
