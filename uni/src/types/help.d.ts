declare namespace IHelp {
    interface Faq {
        id: number;
        title: string;
        content: string;
    }

    interface ListResult {
        steps: string[];
        faqs: Faq[];
    }
}
