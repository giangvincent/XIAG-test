import React from "react";
import { render, screen } from "@testing-library/react";
import { Provider } from "react-redux";
import store from "../../store";
import UserSelect from "./index";
import "@testing-library/jest-dom"; // For toBeInTheDocument

describe("UserSelect", () => {
  const mockUsers = [
    { id: 1, name: "User 1" },
    { id: 2, name: "User 2" },
  ];

  it("renders select element", () => {
    render(
      <Provider store={store}>
        <UserSelect user={1} users={mockUsers} onChange={() => {}} />
      </Provider>,
    );
    expect(screen.getByRole("combobox")).toBeInTheDocument();
  });

  it("displays default option", () => {
    render(
      <Provider store={store}>
        <UserSelect user={1} users={mockUsers} onChange={() => {}} />
      </Provider>,
    );
    expect(screen.getByText("User 1")).toBeInTheDocument();
  });
});
