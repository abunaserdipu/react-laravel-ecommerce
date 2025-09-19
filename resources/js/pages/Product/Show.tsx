import Carousel from "@/Components/Core/Carousel";
import CurrencyFormatter from "@/Components/Core/CurrencyFormatter";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Product, VariationTypeOption } from "@/types";
import { Head, router, useForm, usePage } from "@inertiajs/react";
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
    },[]);

    const getOptionIdMap = (newOptions:object) => {
        return Object.fromEntries(
            Object.entries(newOptions).map(([a,b]) => [a,b.id])
        )
    };

    const chooseOption = (
        typeId: number,
        option: VariationTypeOption,
        updateRouter: boolean = true
    ) => {
        setSelectedOptions((prevSelectedOptions) => {
            const newOptions = {
                ...prevSelectedOptions,
                [typeId]: option
            }

            if(updateRouter){
                router.getUrl(url,{
                    options: getOptionIdMap(newOptions)
                },{
                    preserveScroll: true,
                    preserveState: true
                })
            }
            return newOptions;
        })
    }

    const onQuantityChange = (ev: React.ChangeEvent<HTMLSelectElement>) => {
        form.setData('quantity',parseInt(ev.target.value));
    }

    const addToCart = () => {
        form.post(route('cart.store',product.id),{
            preserveScroll: true,
            preserveState: true,
            onError: (err) => {
                console.log(err);
            }
        })
    }

    const renderProductVariationTypes = () => {
        return(
            product.variationTypes.map((type,i)=>(
                <div key={type.id}>
                    <b>{type.name}</b>
                    {type.type === 'image' && 
                    <div className="flex gap-2 mb-4">
                        {type.options.map(option => (
                            <div onClick={() => chooseOption(type.id,option)} key={option.id}>
                                {option.images && <img src={option.images[0].thumb} alt="" className={'w-[50px]' +}/>}
                            </div>
                        ))}
                    </div>
                    }
                </div>
            ))
        )
    }

    const renderAddToCartButton = () => {
        return(

        )
    }

    useEffect(() => {
        const idsMap = Object.fromEntries(
            Object.entries(selectedOptions).map(([typeId,option]:[string,VariationTypeOption]) => [typeId,option.id])
        )
        console.log(idsMap)
        form.setData('option_ids',idsMap)

    },[selectedOptions]);
    return (
        <AuthenticatedLayout>
            <Head title="Home" />
            <div className="container mx-auto p-8">
                <div className="grid gap-8 grid-cols-1 lg:grid-cols-12">
                    <div className="col-span-7">
                        <Carousel images={images}/>
                    </div>
                    <div className="col-span-5">
                        <h1 className="text-2xl mb-8">{product.title}</h1>

                        <div>
                            <div className="text-3xl-text-semibold">
                                <CurrencyFormatter amount={computedProduct.price} />
                            </div>
                        </div>

                        {renderProductVariationTypes()}

                        {computedProduct.quantity != undefied &&
                         computedProduct.quantity < 10 &&
                         <div className="text-error my-4">
                            <span>Only {computedProduct.quantity} left</span>
                         </div>
                         }
                         {renderAddToCartButton()}

                         <b className="text-xl">About the Item</b>
                         <div className="wysiwyg-item"
                         dangerouslySetInnerHTML={{__html: product.description}}>

                         </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
