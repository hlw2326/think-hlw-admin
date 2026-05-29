<script setup lang="ts">
import { onLaunch, onShow, onHide } from "@dcloudio/uni-app";
import { useApp } from "@/core/app";

const { applyLaunch } = useApp();

onLaunch((options: any) => {
    applyLaunch(options);
    console.log("App Launch", options);
});
onShow((options: any) => {
    applyLaunch(options);
    console.log("App Show", options);
});
onHide(() => {
    console.log("App Hide");
});
</script>

<style lang="scss">
@use "./static/css/style.scss";
@use "./static/css/iconfont.css";
</style>
