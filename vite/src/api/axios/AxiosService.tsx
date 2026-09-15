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

// Mass delete goes through POST with a SKU array (backend also accepts DELETE on /api/*).
const remove = (sku: string[] | string) => {
  return axiosConfig({
    method: "post",
    url: "/",
    data: sku,
  });
};

const axiosService = {
  getAll,
  post,
  remove,
};

export default axiosService;
