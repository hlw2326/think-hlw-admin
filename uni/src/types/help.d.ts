declare namespace IHelp {
    interface Item {
        id: number;
        title: string;
        content: string;
    }

    interface ListResult {
        steps: string[];
        faqs: Item[];
    }
}
