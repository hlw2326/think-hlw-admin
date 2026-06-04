<script setup lang="ts">
import { onLaunch, onShow, onHide } from "@dcloudio/uni-app";
import { useApp } from "@/core/app";

const { applyLaunch } = useApp();

onLaunch((options: any) => {
    applyLaunch(options);
    console.log("App Launch", options);
});
const updateManager = uni.getUpdateManager();

onShow((options: any) => {
    applyLaunch(options);
    console.log("App Show", options);
    updateManager.onUpdateReady(() => {
        uni.showModal({
            title: '更新提示',
            content: '新版本已经准备好，是否重启应用？',
            success: (res: any) => {
                if (res.confirm) {
                    updateManager.applyUpdate();
                }
            }
        });
    });
});
onHide(() => {
    console.log("App Hide");
});
</script>

<style lang="scss">
@use "./static/css/style.scss";
@use "./static/css/iconfont.css";
</style>
