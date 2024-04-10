import "./styles.css";
import { ProductContent } from "../../model/data/FetchData";

interface ProductTypeInputDataProps {
  inputType: string;
  data: ProductContent[];
  onChange: React.ChangeEventHandler<HTMLInputElement>;
  inputDescription: string;
}

export default function ProductTypeInputData(props: ProductTypeInputDataProps) {
  const data = props.data;

  return (
    <>
      {data &&
        data.map((e) => (
          <div key={e.id} className="new-product-type__input">
            <label htmlFor={e.id}>{e.label}</label>
            <input
              id={e.id}
              onChange={props.onChange}
              type={props.inputType}
              maxLength={5}
              size={10}
            />
            <br />
          </div>
        ))}
      <p>{props.inputDescription}</p>
    </>
  );
}
