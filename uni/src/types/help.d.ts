declare namespace IHelp {
    interface Faq {
        id: number;
        question: string;
        answer: string;
    }

    interface ListResult {
        steps: string[];
        faqs: Faq[];
    }
}
