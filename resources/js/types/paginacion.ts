export interface PaginatedPayload<T> {
    data: T[];
    current_page: number;
    per_page: number;
    total: number;
}

export interface Filters {
    q: string | null;
    sort: string | null;
    order: 'asc' | 'desc' | null;
    limit: number | null;
}

export interface FetchDataParams {
    [key: string]: string | number;
    page: number;
    limit: number;
    sort: string;
    order: 'asc' | 'desc';
    q: string;
}

export interface TableOptions {
    page: number;
    itemsPerPage: number;
    sortBy: { key: string; order: 'asc' | 'desc' }[];
}
