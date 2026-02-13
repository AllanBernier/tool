export type Tag = {
    id: string;
    name: string;
    slug: string;
};

export type Category = {
    id: string;
    name: string;
    slug: string;
    description: string;
    icon: string;
    sort_order: number;
    tools_count?: number;
};

export type Tool = {
    id: string;
    name: string;
    slug: string;
    url: string;
    logo_url: string;
    logo_path: string | null;
    description: string | null;
    content: string | null;
    pricing: PricingPlan[] | null;
    pros: string[] | null;
    cons: string[] | null;
    features: string[] | null;
    faq: FaqItem[] | null;
    platforms: string[] | null;
    category: Category;
    tags: Tag[];
    is_published: boolean;
    is_sponsored: boolean;
    generation_status: string;
};

export type Comparison = {
    id: string;
    slug: string;
    content: string | null;
    verdict: string | null;
    tool_a: Tool;
    tool_b: Tool;
    is_published: boolean;
    generation_status: string;
};

export type PricingPlan = {
    name: string;
    price: string;
    period?: string;
    features: string[];
    is_popular?: boolean;
};

export type FaqItem = {
    question: string;
    answer: string;
};
