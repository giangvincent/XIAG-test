import React from "react";
import { render, screen } from "@testing-library/react";
import { Provider } from "react-redux";
import store from "../../store";
import App from "./index";

test("renders learn react output", () => {
  render(
    <Provider store={store}>
      <App />
    </Provider>,
  );
  // Adjusted text expectation based on actual content
  const linkElement = screen.getByText(/TODO list with users/i);
  expect(linkElement).toBeInTheDocument();
});
