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
        const valStr = String(value || "").trim();
        let uid = 0;

        if (valStr.startsWith("{") && valStr.endsWith("}")) {
            try {
                const data = JSON.parse(valStr);
                uid = Number.parseInt(String(data.uid || data.invite_uid || data.id || "0"), 10);
            } catch (e) {
                // ignore
            }
        }

        if (uid <= 0 && valStr.includes(",")) {
            const parts = valStr.split(",");
            uid = Number.parseInt(parts[0], 10);
        }

        if (uid <= 0) {
            uid = Number.parseInt(valStr || "0", 10);
        }

        if (uid > 0) {
            store.invite_uid = uid;
        }
    }

    function applyLaunch(options: any): void {
        let inviteUid = options?.query?.invite_uid || options?.query?.uid;
        if (!inviteUid && options?.query?.scene) {
            inviteUid = decodeURIComponent(options.query.scene);
        }
        setInviteUid(inviteUid);
    }

    return {
        clipboard,
        setClipboard,
        setInviteUid,
        applyLaunch,
        store,
    };
}
