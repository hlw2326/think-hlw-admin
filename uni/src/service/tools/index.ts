import { BaseService, ServiceNamespace, PluginService } from "@hlw-uni/mp-vue";

@PluginService
@ServiceNamespace("api/v1.tools")
class Tools extends BaseService {
    list(params: Record<string, unknown> = {}) {
        return this.request<ITools.ListResult>({
            url: "/list",
            method: "GET",
            data: {
                ...params,
            },
        });
    }

    click(params: { id: number }) {
        return this.request<null>({
            url: "/click",
            method: "GET",
            data: {
                ...params,
            },
        });
    }
}

export default Tools;
