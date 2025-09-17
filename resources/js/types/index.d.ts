import { Config } from 'ziggy-js';

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export type Image = {
    id: number;
    thumb: string;
    small: string;
    large: string;
}

export type VariationTypeOption = {
    id: number;
    name: string;
    image: Image[];
    type: VariationType;
}

export type VariationType = {
    id: number;
    name: string;
    type: 'select' | 'radio' | 'image',
    option: VariationTypeOption[]
}

export type Product = {
    id: number;
    title: string;
    slug: string;
    price: number;
    quantity: number;
    image: string;
    images: Image[];
    short_description: string;
    description: string;
    user: {
        id: number;
        name: string;
    };
    department: {
        id: number;
        name: string;
    };
    variationTypes: VariationType[],
    variations: Array<{
        id: number;
        variation_type_option_id: number[];
        quantity: number;
        price: number
    }>
 }

 export type PaginateProps<T> = {
    data: Array<T>;
 }

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    ziggy: Config & { location: string };
};
