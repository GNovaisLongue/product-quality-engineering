import { ProductProps } from "../../../model/ProductProps";
import "./styles.css";

export default function ProductItem(props: ProductProps) {
  return (
    <div>
      <p>
        {props.sku}
        <br />
        {props.name}
        <br />
        {props.price}
        <br />
        {props.product_type === "Furniture" &&
          `Dimensions: ${props.product_attribute} `}
        {props.product_type === "DVD" && `Size: ${props.product_attribute} MB`}
        {props.product_type === "Book" &&
          `Weight: ${props.product_attribute} KG`}
      </p>
    </div>
  );
}
