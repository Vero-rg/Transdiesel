export type AvailabilityValue = 'in_stock' | 'out_of_stock' | 'on_demand';

export type AvailabilityOption = {
    value: AvailabilityValue;
    label: string;
};

export type Category = {
    id: number;
    name: string;
    slug: string;
    products_count?: number;
};

export type Brand = {
    id: number;
    name: string;
    slug: string;
    logo_path: string | null;
    logo_url: string | null;
    is_active?: boolean;
    products_count?: number;
};

export type Product = {
    id: number;
    category_id: number;
    brand_id: number | null;
    sku: string;
    name: string;
    slug: string;
    description: string | null;
    specifications: Record<string, string> | null;
    price: number;
    availability: AvailabilityValue;
    image_path: string | null;
    image_url: string | null;
    category?: Category | null;
    brand?: Brand | null;
    approved_reviews_count?: number | null;
    approved_reviews_avg_rating?: number | null;
};

export type Review = {
    id: number;
    product_id: number;
    author_name: string;
    author_email: string;
    rating: number;
    comment: string | null;
    is_approved: boolean;
    created_at: string;
};

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
    prev_page_url: string | null;
    next_page_url: string | null;
};

export type CatalogFilters = {
    search: string | null;
    category: string | null;
    brand: string | null;
    availability: string | null;
};
