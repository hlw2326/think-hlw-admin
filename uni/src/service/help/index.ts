import { BaseService, ServiceNamespace, ServicePrefix } from "@hlw-uni/mp-vue";

@ServicePrefix("api")
@ServiceNamespace("v1.help")
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
