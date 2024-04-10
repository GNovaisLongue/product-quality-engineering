import "./styles.css";

export default function ProductTypeInput(props: {
  type: string;
  onAttributesChange: React.ChangeEventHandler<HTMLInputElement>;
}) {
  const { type, onAttributesChange } = props;

  return (
    <div className="new-product-type__form">
      {/* <div className="new-product-type__select">
        <label htmlFor="productType">Type Switcher</label>
        <select
          id="productType"
          value={type}
          ref={productAttr}
          onChange={handleTypeChange}
        >
          <option value="DVD">DVD</option>
          <option value="Book">Book</option>
          <option value="Furniture">Furniture</option>
        </select>
      </div> */}

      {/* DVD */}
      {type === "DVD" && (
        <div className="new-product-type__input">
          <label htmlFor="size">Size (MB)</label>
          <input
            id="size"
            onChange={onAttributesChange}
            type="number"
            maxLength={5}
            size={10}
          />
          <p>Please, provide size in cm</p>
        </div>
      )}

      {/* Book */}
      {type === "Book" && (
        <div className="new-product-type__input">
          <label htmlFor="weight">Weight (KG)</label>
          <input
            id="weight"
            onChange={onAttributesChange}
            type="number"
            maxLength={5}
            size={10}
          />
          <p>Please, provide weight in kg</p>
        </div>
      )}

      {/* Furniture */}
      {type === "Furniture" && (
        <div>
          <div className="new-product-type__input">
            <label htmlFor="height">Height (CM)</label>
            <input
              id="height"
              onChange={onAttributesChange}
              type="number"
              maxLength={5}
              size={10}
            />
          </div>
          <div className="new-product-type__input">
            <br />
            <label htmlFor="width">Width (CM)</label>
            <input
              id="width"
              onChange={onAttributesChange}
              type="number"
              maxLength={5}
              size={10}
            />
          </div>
          <br />
          <div className="new-product-type__input">
            <label htmlFor="length">Length (CM)</label>
            <input
              id="length"
              onChange={onAttributesChange}
              type="number"
              maxLength={5}
              size={10}
            />
          </div>
          <p>Please, provide dimensions in HxWxL</p>
        </div>
      )}
    </div>
  );
}
