import ReactDOM from "react-dom/client";
import {
  BrowserRouter as Router,
  Routes,
  Route,
  Navigate,
} from "react-router-dom";

import "./index.css";
import ProductListPage from "./pages/ProductListPage.tsx";
import AddProductPage from "./pages/AddProductPage.tsx";

ReactDOM.createRoot(document.getElementById("root")!).render(
  <Router>
    <Routes>
      <Route path="/" element={<Navigate to="/list-products" />} />
      <Route path="/list-products" element={<ProductListPage />} />
      <Route path="/add-product" element={<AddProductPage />} />
    </Routes>
  </Router>
);
