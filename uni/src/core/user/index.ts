import { computed } from "vue";
import type { ComputedRef } from "vue";
import { useAppStore, useUserStore } from "@/store";

// 全局唯一的微信登录 Promise，用于防止并发接口请求重复触发登录流程
let loginPromise: Promise<void> | null = null;

export function useUser() {
    const store = useUserStore();
    const app = useAppStore();

    const token: ComputedRef<string> = computed(() => store.token);

    const user: ComputedRef<IUser.Info | null> = computed(() => store.user);

    const is_login: ComputedRef<boolean> = computed(() => !!store.token);

    function setLogin(newToken: string, info: IUser.Info): void {
        store.token = newToken;
        store.user = info;
    }

    function setUserInfo(info: IUser.Info): void {
        store.user = info;
    }

    function logout(): void {
        store.token = "";
        store.user = null;
    }

    async function login(): Promise<void> {
        if (loginPromise) {
            return loginPromise;
        }

        loginPromise = (async () => {
            try {
                const inviteUid = app.invite_uid > 0 ? String(app.invite_uid) : "";
                const res = await service.v1.login.wx(inviteUid ? { invite_uid: inviteUid } : undefined);
                if (res) {
                    store.token = res.token || "";
                    store.user = (res.user as IUser.Info) || null;
                }
            } finally {
                loginPromise = null;
            }
        })();

        return loginPromise;
    }

    async function getUserInfo(): Promise<IUser.Info | null> {
        if (!store.token) {
            try {
                await login();
            } catch (error) {
                console.warn("[user] login before fetch failed", error);
                return null;
            }
        }

        try {
            const res = await service.v1.user.info();
            if (res.code === 1 && res.data) {
                store.user = res.data as IUser.Info;
                return res.data as IUser.Info;
            }
            if (res.code !== 401) {
                hlw.$msg.toast(res.info || "用户信息加载失败");
            }
        } catch (error) {
            console.warn("[user] fetch failed", error);
        }

        return null;
    }

    async function updateAvatar(filePath: string): Promise<boolean> {
        if (!filePath) return false;
        uni.showLoading({ title: "上传中", mask: true });
        try {
            const avatarUrl = await service.v1.upload.file({ biz: "avatar", filePath });
            const res = await service.v1.user.update({ avatar_url: avatarUrl });
            if (res.code !== 1 || !res.data) {
                hlw.$msg.toast(res.info || "头像保存失败");
                return false;
            }
            setUserInfo(res.data);
            hlw.$msg.toast("头像已更新");
            return true;
        } catch (error: any) {
            console.warn("[user] avatar upload failed", error);
            hlw.$msg.toast(error?.message || "头像上传失败");
            return false;
        } finally {
            uni.hideLoading();
        }
    }

    async function updateNickname(nickname: string): Promise<boolean> {
        const name = nickname?.trim();
        if (!name) {
            hlw.$msg.toast("昵称不能为空");
            return false;
        }
        uni.showLoading({ title: "保存中", mask: true });
        try {
            const res = await service.v1.user.update({ nickname: name });
            if (res.code !== 1 || !res.data) {
                hlw.$msg.toast(res.info || "昵称保存失败");
                return false;
            }
            setUserInfo(res.data);
            hlw.$msg.toast("昵称已更新");
            return true;
        } catch (error: any) {
            console.warn("[user] nickname update failed", error);
            hlw.$msg.toast(error?.message || "昵称更新失败");
            return false;
        } finally {
            uni.hideLoading();
        }
    }

    return {
        token,
        user,
        is_login,
        setLogin,
        setUserInfo,
        logout,
        login,
        getUserInfo,
        updateAvatar,
        updateNickname,
        store,
    };
}
