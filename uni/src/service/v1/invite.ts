import { BaseService, ServiceNamespace, ServicePrefix } from "@hlw-uni/mp-vue";

@ServicePrefix("plugin-base")
@ServiceNamespace("api.v1.invite")
class Invite extends BaseService {
    qrcode() {
        return this.request<{ qrcode_url: string; page: string }>({
            url: "/qrcode",
            method: "GET",
        });
    }
}

export default Invite;
