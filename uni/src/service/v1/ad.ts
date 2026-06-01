import { BaseService, ServiceNamespace, ServicePrefix } from "@hlw-uni/mp-vue";

@ServicePrefix("plugin-base")
@ServiceNamespace("api.v1.ad")
class Ad extends BaseService {
    reward(params: Record<string, unknown> = {}) {
        return this.request<IConfig.AdReward>({
            url: "/reward",
            method: "POST",
            data: {
                ...params,
            },
        });
    }
}

export default Ad;
