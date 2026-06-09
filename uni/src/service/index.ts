import { md5 } from "js-md5";
import { BaseService, ServiceNamespace, getDevice, getDeviceQuery, request, withQuery, toQuery, signText, type RequestConfig } from "@hlw-uni/mp-vue";
import { useUser } from "@/core";
import Ad from "./v1/ad";
import Config from "./v1/config";
import Help from "./v1/help";
import Login from "./v1/login";
import Tools from "./v1/tools";
import Upload from "./v1/upload";
import User from "./v1/user";
import Invite from "./v1/invite";

type ServiceCtor = new () => BaseService;

export interface ServiceMap {
    v1: {
        ad: Ad;
        config: Config;
        help: Help;
        login: Login;
        tools: Tools;
        upload: Upload;
        user: User;
        invite: Invite;
    };
}

const modules: Record<string, Record<string, BaseService>> = {};
const files = import.meta.glob<{ default: ServiceCtor }>("./*/*.ts", { eager: true });

for (const [path, module] of Object.entries(files)) {
    const parts = path.split("/");
    const version = parts[1];
    const filename = parts[2];
    if (version && filename) {
        const name = filename.replace(/\.ts$/, "");
        modules[version] = modules[version] || {};
        modules[version][name] = new module.default();
    }
}

export const service = modules as unknown as ServiceMap;
export default service;
export { BaseService, ServiceNamespace };

globalThis.service = service;

request.setBaseURL(import.meta.env.VITE_API_BASE_URL ?? "");

request.onRequest(async (config) => {
    let url = config.url;

    let data = config.data;

    if ((config.method ?? "GET").toUpperCase() === "GET" && data && typeof data === "object") {
        url = withQuery(url, toQuery(data as Record<string, unknown>));
        data = undefined;
    }

    const info = getDevice();
    const device = getDeviceQuery();
    url = withQuery(url, device);

    // 附加当前时间戳参数 t（单位：秒）以配合后端做防重放校验
    url = withQuery(url, `t=${Math.floor(Date.now() / 1000)}`);

    const secret = import.meta.env.VITE_SIG_SECRET ?? "";
    if (secret) {
        url = withQuery(url, `sig=${md5(signText(url) + secret)}`);
    }

    const token = useUser().token.value;
    const nextConfig = {
        ...config,
        url,
        data,
        headers: {
            ...config.headers,
            "x-appid": info.appid,
            ...(token ? { "x-token": token } : {}),
        },
    } as RequestConfig;

    return nextConfig;
});

request.onResponse(async (res) => {
    if (res.code === 401) {
        useUser()
            .login()
            .catch(() => undefined);
        return res;
    }

    if (res.code === 404 || res.code === 500 || res.code === 502 || res.code === 503) {
        hlw.$msg.toast(res.info || `请求失败 (${res.code})`);
    }

    return res;
});

request.onError(async (err) => {
    if (err.message.includes("401")) {
        useUser()
            .login()
            .catch(() => undefined);
    } else {
        let msg = err.message || "请求失败";
        // 过滤/移除 URL 地址及域名信息
        msg = msg.replace(/https?:\/\/[^\s]+/g, "").replace(/url:\s*/gi, "").trim();
        hlw.$msg.toast(msg || "请求失败");
    }
});
