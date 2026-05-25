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

}

export default Help;
