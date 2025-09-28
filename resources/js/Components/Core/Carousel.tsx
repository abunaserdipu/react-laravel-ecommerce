import { Image } from "@/types";

export default function Carousel({ images }: { images: Image[] }) {
    // console.log(images);
    return (
        <>
            <div className="flex items-start gap-8">
                <div className="flex flex-col items-center gap-2 py-2">
                    {images.map((image, i) => (
                        <a
                            href={"#item" + i}
                            className="border-2 hover:border-blue-500"
                            key={image.id}
                        >
                            <img
                                src={image.thumb}
                                alt=""
                                className="w-[50px]"
                            />
                        </a>
                    ))}
                </div>
                <div className="carousel w-full">
                    {images.map((image, i) => (
                        <div
                            id={"item" + i}
                            className="carousel-item w-full flex-shrink-0 flex-grow-0"
                            key={image.id}
                        >
                            <img src={image.large} alt="" className="w-full h-96 object-contain" />
                        </div>
                    ))}
                </div>
            </div>
        </>
    );
}
