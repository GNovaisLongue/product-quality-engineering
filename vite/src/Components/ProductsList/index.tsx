import React, { useEffect } from "react";
import { useNavigate } from "react-router-dom";

import axiosService from "../../api/axios/AxiosService";

import "./styles.css";
import ProductItem from "./ProductItem";
import Footer from "../Footer";

import { ProductProps } from "../../model/ProductProps";

export default function ProductsList() {
  const navigate = useNavigate();
  const gotoAddProductPage = "/add-product";
  const [productsDeletion, setProductsDeletion] = React.useState<string[]>([]); //saves the SKUs to future deletion
  const [products, setProducts] = React.useState<ProductProps[]>([]);

  const onCheckHandler =
    (sku: string) => (e: React.ChangeEvent<HTMLInputElement>) => {
      if (e.target.checked) {
        setProductsDeletion([...productsDeletion, sku]); //saves the product ID/sku for deletion
      } else {
        setProductsDeletion(productsDeletion.filter((prod) => prod !== sku)); //keeps the products not related to the unchecked one
      }
    };

  ///
  const handleDelete = (e: React.FormEvent) => {
    e.preventDefault(); //prevents the browser from reloading once a form updates
    if (productsDeletion.length !== 0) {
      handleDeletion(productsDeletion);
    }
  };

  const refresh = () => {
    const interval = setTimeout(() => {
      navigate(0);
    }, 250);
    return () => clearTimeout(interval);
  };

  const getData = () => {
    axiosService
      .getAll()
      .then((res: { data: React.SetStateAction<ProductProps[]> }) => {
        setProducts(res.data);
      })
      .catch((err: { message: unknown }) => {
        console.log("GET error: ", err.message);
      });
  };

  const handleDeletion = (sku: string[] | string) => {
    axiosService
      .remove(sku)
      .then(() => {
        refresh();
      })
      .catch((err: { message: unknown }) => {
        console.log("Delete error: ", err.message);
      });
  };

  useEffect(() => {
    getData();
  }, []);

  return (
    <div>
      <form id="list_form" onSubmit={handleDelete}>
        <div className="header">
          <b>Product List</b>
          <div className="button_group">
            <button
              type="reset"
              onClick={() => {
                navigate(gotoAddProductPage);
              }}
            >
              ADD
            </button>
            <button id="delete-product-btn" type="submit">
              MASS DELETE
            </button>
          </div>
        </div>
        <hr />
        <ul className="div-products">
          {products.length === 0 ? (
            <h2 className="products-list_fallback">Found no Products.</h2>
          ) : (
            products.map((product) => (
              <div className="div-product" key={product.sku}>
                <input
                  type="checkbox"
                  className="delete-checkbox"
                  onChange={onCheckHandler(product.sku)}
                />
                <ProductItem
                  sku={product.sku}
                  name={product.name}
                  price={product.price}
                  product_type={product.product_type}
                  product_attribute={product.product_attribute}
                />
              </div>
            ))
          )}
        </ul>
      </form>

      <Footer>Scandiweb test Assignment</Footer>
    </div>
  );
}
