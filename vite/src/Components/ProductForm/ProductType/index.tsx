import React from "react";

import "./styles.css";
import ProductTypeInputData from "../../ProductTypeInputData";
import ProductTypeSelect from "../../ProductTypeSelect";
// import ProductTypeInput from "../../ProductTypeInput";

import FetchData, {
  ProductAttributeProps,
} from "../../../model/data/FetchData";

interface ProductTypeProps {
  prodType: (type: string) => void;
  typeAttributes: (attributes: { [key: string]: string }) => void;
}

export default function ProductType(props: ProductTypeProps) {
  const [inputs, setInputs] = React.useState<{ [K: string]: string }>({});
  const [type, setType] = React.useState<string>("DVD");
  const [productTypeInput, setProductTypeInput] =
    React.useState<ProductAttributeProps>({} as ProductAttributeProps);

  const handleTypeChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    setType(e.target.value); //re-render fields + update dropdown value
    props.prodType(e.target.value);
  };

  const onAttributesChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const key: string = e.target.id;
    const value: string = e.target.value;

    //updates field and input data
    const data: { [K: string]: string } = { ...inputs };
    data[key] = value;
    setInputs(data);

    props.typeAttributes(data);
  };

  const getAttrFields = () => {
    const data = FetchData.filterContentByType(type);

    const savedInputs: { [key: string]: string } = {};
    for (let i = 0; i < data.length; i++) {
      const name = data[i].id;
      savedInputs[name] = "";
    }

    setInputs(savedInputs);
    props.typeAttributes(savedInputs);
  };

  React.useEffect(() => {
    getAttrFields();
    // getting all data associated to a type
    setProductTypeInput(FetchData.filterProductByType(type));
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [type]);

  return (
    <div className="new-product-type__form">
      <ProductTypeSelect
        label={"Type Switcher"}
        select_id={"productType"}
        value={type}
        onChange={handleTypeChange}
        options={FetchData.getTypes()}
      />
      <ProductTypeInputData
        inputType={"number"}
        data={FetchData.filterContentByType(type)}
        onChange={onAttributesChange}
        inputDescription={productTypeInput.p_description}
      />
      {/* <ProductTypeInput type={type} onAttributesChange={onAttributesChange} /> */}
    </div>
  );
}
