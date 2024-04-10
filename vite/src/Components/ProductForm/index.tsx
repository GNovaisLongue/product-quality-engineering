import React from "react";
import { useNavigate } from "react-router-dom";

import axiosService from "../../api/axios/AxiosService";

import "./styles.css";
import ProductType from "./ProductType";
import Footer from "../Footer";

import { ProductProps } from "../../model/ProductProps";

export default function ProductForm() {
  const navigate = useNavigate();

  const skuRef = React.useRef<HTMLInputElement | null>(null);
  const nameRef = React.useRef<HTMLInputElement | null>(null);
  const priceRef = React.useRef<HTMLInputElement | null>(null);

  const [prodType, setProdType] = React.useState<string>("DVD");
  const [prodAttr, setProdAttr] = React.useState<{ [K: string]: string }>({});

  const prodTypehandler = (prodType: React.SetStateAction<string>) => {
    setProdType(prodType);
  };
  const productAttributesHandler = (attr: { [K: string]: string }) => {
    setProdAttr(attr);
  };

  const handlePost = (product: ProductProps) => {
    axiosService
      .post(product)
      .then(() => {
        const interval = setTimeout(() => {
          navigate(-1);
        }, 600);
        return () => clearTimeout(interval);
      })
      .catch((err) => {
        alert(err.message);
      });
  };

  const onSubmit = (e: React.FormEvent) => {
    e.preventDefault(); //prevents the browser from reloading once a form updates

    const sku: string = skuRef.current?.value || "";
    const name: string = nameRef.current?.value || "";
    const price: string = priceRef.current?.value || "";

    const isEmpty =
      Object.values(prodAttr).length === 0 ||
      Object.values(prodAttr).some((x) => x === "" || x === undefined);

    if (name === "" || price === "" || sku === "" || isEmpty === true) {
      return alert("Please, submit required data.");
    }

    let prod_attr: string = ""; //deal with furniture dimensions
    if (prodType === "Furniture") {
      prod_attr = `${prodAttr.height}x${prodAttr.width}x${prodAttr.length}`;
    } else {
      for (const prod in prodAttr) {
        prod_attr = prodAttr[prod];
      }
    }

    /*
      handle class instance here
    */

    const newProduct: ProductProps = {
      sku: sku,
      name: name,
      price: price,
      product_type: prodType,
      product_attribute: prod_attr,
    };
    handlePost(newProduct);
  };

  return (
    <div>
      <form id="product_form" onSubmit={onSubmit}>
        <div className="header">
          <b>Product add</b>
          <div className="button_group">
            <button type="submit">Save</button>
            <button
              type="reset"
              onClick={() => {
                navigate(-1);
              }}
            >
              Cancel
            </button>
          </div>
        </div>
        <hr />
        <div className="new-product__form">
          <div className="new-product__input">
            <label htmlFor="sku">SKU</label>
            <input
              id="sku"
              type="text"
              ref={skuRef}
              maxLength={100}
              size={20}
            />
          </div>
          <div className="new-product__input">
            <label htmlFor="name">Name</label>
            <input
              id="name"
              type="text"
              ref={nameRef}
              maxLength={100}
              size={20}
            />
          </div>
          <div className="new-product__input">
            <label htmlFor="price">Price ($)</label>
            <input
              id="price"
              type="number"
              min={0.01}
              step={0.01}
              ref={priceRef}
              maxLength={10}
            />
          </div>
          <ProductType
            prodType={prodTypehandler}
            typeAttributes={productAttributesHandler}
          />
        </div>
      </form>
      <Footer>Scandiweb test Assignment</Footer>
    </div>
  );
}
