import axiosService from "../api/axios/AxiosService";
import { IProductProps } from "./ProductProps";

export abstract class Product implements IProductProps {
  sku: string;
  name: string;
  price: string;
  product_type: string;
  product_attribute: string;
  constructor(
    sku: string,
    name: string,
    price: string,
    product_type: string,
    product_attribute: string
  ) {
    this.sku = sku;
    this.name = name;
    this.price = price;
    this.product_type = product_type;
    this.product_attribute = product_attribute;
  }

  createProduct(product: Product) {
    axiosService.post(product).catch((err) => {
      alert(err.message);
    });
  }

  deleteProduct(sku: string[] | string) {
    axiosService.remove(sku).catch((err: { message: unknown }) => {
      alert(`Delete error:  ${err.message}`);
    });
  }

  /*
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
  */

  /*
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
  */
}
