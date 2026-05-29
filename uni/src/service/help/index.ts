import { BaseService, ServiceNamespace, PluginService } from "@hlw-uni/mp-vue";

@PluginService
@ServiceNamespace("api/v1.help")
class Help extends BaseService {
    list(params: { cate_id?: number } = {}) {
        return this.request<IHelp.ListResult>({
            url: "/list",
            method: "GET",
            data: {
                ...params,
            },
        });
    }

    click(id: number) {
        return this.request({
            url: "/click",
            method: "POST",
            data: {
                id,
            },
        });
    }
}

export default Help;
