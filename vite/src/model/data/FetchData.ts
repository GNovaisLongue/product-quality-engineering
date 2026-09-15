const ProductAttributes: ProductAttributeProps[] = [
  {
    type: "DVD",
    content: [
      {
        id: "size",
        label: "Size (MB)",
      },
    ],
    p_description: "Please, provide size in MB",
  },
  {
    type: "Book",
    content: [
      {
        id: "weight",
        label: "Weight (KG)",
      },
    ],
    p_description: "Please, provide weight in kg",
  },
  {
    type: "Furniture",
    content: [
      {
        id: "height",
        label: "Height (CM)",
      },
      {
        id: "width",
        label: "Width (CM)",
      },
      {
        id: "length",
        label: "Length (CM)",
      },
    ],
    p_description: "Please, provide dimensions in HxWxL",
  },
];

export interface ProductContent {
  id: string;
  label: string;
}

export interface ProductAttributeProps {
  type: string;
  content: ProductContent[];
  p_description: string;
}

const filterProductByType = (type: string): ProductAttributeProps => {
  const product = ProductAttributes.find(
    (e) => e.type === type
  ) as ProductAttributeProps;
  return product;
};

const filterContentByType = (type: string): ProductContent[] => {
  const product = ProductAttributes.find(
    (e) => e.type === type
  ) as ProductAttributeProps;
  return product.content;
};

const getTypes = (): string[] => {
  const types = ProductAttributes.map((e) => e.type);
  return types;
};

const FetchData = {
  filterContentByType,
  filterProductByType,
  getTypes,
};

export default FetchData;
