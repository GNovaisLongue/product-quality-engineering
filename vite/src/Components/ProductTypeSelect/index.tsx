import "./styles.css";

interface ProductTypeSelectProps {
  label: string;
  select_id: string;
  value: string;
  onChange: React.ChangeEventHandler<HTMLSelectElement>;
  options: string[];
}

export default function ProductTypeSelect(props: ProductTypeSelectProps) {
  const options = props.options;

  return (
    <div className="new-product-type__select">
      <label htmlFor={props.select_id}>{props.label}</label>
      <select
        id={props.select_id}
        value={props.value}
        onChange={props.onChange}
      >
        {options.map((option, idx) => (
          <option key={idx} value={option}>
            {option}
          </option>
        ))}
      </select>
    </div>
  );
}
