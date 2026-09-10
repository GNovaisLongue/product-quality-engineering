import { IProductProps } from "../../model/ProductProps";
import axiosConfig from "./config/AxiosConfig";

const getAll = () => {
  return axiosConfig({
    method: "get",
    url: "/",
  });
};

const post = (product: IProductProps) => {
  return axiosConfig({
    method: "post",
    url: "/",
    data: {
      ...product,
    },
  });
};

// !!! for some reason, delete method doesn't work on 000webhost
// bypassed using 'post' method
const remove = (sku: string[] | string) => {
  return axiosConfig({
    method: "post",
    url: "/",
    data: sku,
  });

  // return axiosConfig({
  //   method: "delete",
  //   url: "/",
  //   ...sku,
  // });
};

const axiosService = {
  getAll,
  post,
  remove,
};

export default axiosService;
