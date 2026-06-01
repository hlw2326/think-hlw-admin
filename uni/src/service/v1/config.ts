import { BaseService, ServiceNamespace, ServicePrefix } from "@hlw-uni/mp-vue";

@ServicePrefix("plugin-base")
@ServiceNamespace("api.v1.config")
class Config extends BaseService {
    index(params: Record<string, unknown> = {}) {
        return this.request<{
            base?: IConfig.Base;
            share?: IConfig.Share;
            contact?: IConfig.Contact;
            ad?: IConfig.Ad;
        }>({
            url: "/index",
            method: "GET",
            data: {
                ...params,
            },
        });
    }

}

export default Config;
