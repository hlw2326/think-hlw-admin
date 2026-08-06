declare namespace ITools {
    interface Item {
        id: number;
        title: string;
        desc: string;
        appid: string;
        path: string;
        logo: string;
        sort: number;
        status: number;
        click_count: number;
    }

    interface ListResult {
        list: Item[];
    }
}
