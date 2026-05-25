import { BaseService, ServiceNamespace, PluginService } from "@hlw-uni/mp-vue";

@PluginService
@ServiceNamespace("api/v1.login")
class Login extends BaseService {
    in(params: ILogin.Params) {
        return this.request<ILogin.Result>({
            url: "/in",
            method: "POST",
            data: {
                ...params,
            },
        });
    }

    wx(params: Partial<ILogin.Params> = {}) {
        return new Promise<ILogin.Result>((resolve, reject) => {
            uni.login({
                provider: "weixin",
                success: async (loginRes) => {
                    try {
                        const res = await this.in({ ...params, code: loginRes.code });
                        resolve(res.data);
                    } catch (e) {
                        reject(e);
                    }
                },
                fail: (err) => reject(new Error(err.errMsg || "微信登录失败")),
            });
        });
    }
}

export default Login;
