export interface IProductProps {
  sku: string;
  name: string;
  price: string;
  product_type: string;
  product_attribute: string;
}

export interface IDVDAttributes {
  type: "DVD";
  size: string;
}

export interface IBookAttributes {
  type: "Book";
  weight: string;
}

export interface IFurnitureAttributes {
  type: "Furniture";
  height: string;
  width: string;
  length: string;
}
export type ProductAttributes =
  | IDVDAttributes
  | IBookAttributes
  | IFurnitureAttributes;
