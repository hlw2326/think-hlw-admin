import { BaseService, ServiceNamespace, ServicePrefix } from "@hlw-uni/mp-vue";

@ServicePrefix("plugin-base")
@ServiceNamespace("api.v1.tools")
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
