import { computed } from "vue";
import type { ComputedRef } from "vue";
import { useAppStore } from "@/store";

export function useApp() {
    const store = useAppStore();

    const clipboard: ComputedRef<boolean> = computed(() => store.clipboard);

    function setClipboard(value: boolean): void {
        store.clipboard = value;
    }

    function setInviteUid(value: unknown): void {
        const uid = Number.parseInt(String(value || "0"), 10);
        if (uid > 0) {
            store.invite_uid = uid;
        }
    }

    function applyLaunch(options: any): void {
        setInviteUid(options?.query?.invite_uid || options?.query?.uid);
    }

    return {
        clipboard,
        setClipboard,
        setInviteUid,
        applyLaunch,
        store,
    };
}
