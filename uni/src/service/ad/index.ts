import { BaseService, ServiceNamespace, PluginService } from "@hlw-uni/mp-vue";

@PluginService
@ServiceNamespace("api/v1.ad")
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
