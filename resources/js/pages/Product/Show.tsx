import { Product, VariationTypeOption } from "@/types";
import { useForm, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";

export default function Show({
    product,
    variationOptions,
}: {
    product: Product;
    variationOptions: number[];
}) {
    const form = useForm<{
        option_ids: Record<string,number>;
        quantity: number;
        price: number | null;
    }>({
        option_ids: {},
        quantity: 1,
        price: null // Populate price on change
    })

    const url = usePage();

    const [selectedOptions,setSelectedOptions] = useState<Record<number,VariationTypeOption>>([]);

    const images = useMemo(()=>{
        for(let typeId in selectedOptions){
            const option = selectedOptions[typeId];
            if(option.images.length > 0) return option.images;
        }
        return product.images;
    },[product,selectedOptions]);

    const computedProduct = useMemo(()=>{
        const selectedOptionIds = Object.values(selectedOptions)
        .map(op => op.id)
        .sort();

        for(let variation of product.variations){
            const optionIds = variation.variation_type_option_ids.sort();
            if(arraysAreEqual(selectedOptionIds,optionIds)){
                return{
                    price: variation.price,
                    quantity: variation.quantity === null ? Number.MAX_VALUE : variation.quantity,
                }
            
            }
            return {
                price: product.price,
                quantity: product.quantity,
            }
        }
    },[product,selectedOptions]);

    useEffect(()=>{
        for(let type of product.variationTypes){
            const selectedOptionId:number = variationOptions[type.id];
            console.log(selectedOptionId,type.option);
            chooseOption(
                type.id,
                type.option.find(op => op.id == selectedOptionId) || type.option[0],
                false
            )
        }
    })
    return (

    );
}
